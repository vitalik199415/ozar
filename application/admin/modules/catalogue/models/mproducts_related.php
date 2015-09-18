<?php
class Mproducts_related extends AG_Model
{
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	const ID_PR_DESC 	= 'id_m_c_products_description';
	const PR_REL		= 'm_c_products_related';
	const ID_PR_REL		= 'id_m_c_products_related';
	
	
	function __construct()
	{
		parent::__construct();
	}
	public function render_product_related_grid()
	{	
		$this->load->library('grid');
		$this->grid->_init_grid('products_related_grid');
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`create_date`, A.`update_date`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('catalogue/products_related_helper');
		helper_products_related_grid_build($this->grid);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		$this->grid->render_grid();
	}
	
	public function add_related_grid_build($id)
	{
		$this->load->model('catalogue/mproducts');
		if(!$this->mproducts->check_isset_pr($id)) return FALSE;
		
		$relared_products = $this->get_related_products($id);
		$this->load->library('grid');
		$this->grid->_init_grid('product_'.$id.'_not_related_grid', array('limit' => 1000, 'url' => set_url('catalogue/products_related/get_ajax_add_related/id/'.$id)), TRUE);
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_PR."` <>".$id)
			->where("A.`".self::ID_USERS."`", $this->id_users);
			if(count($relared_products)>0) 
			{
				$this->grid->db->where_not_in("A.`".self::ID_PR."`", $relared_products);
			}
		$this->load->helper('catalogue/products_related_helper');
		add_related_grid_build($this->grid);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		return $this->grid->render_grid(TRUE);
	}
	
	public function get_related_grid_build($id)
	{
		$this->load->model('catalogue/mproducts');
		if(!$this->mproducts->check_isset_pr($id)) return FALSE;
		
		$relared_products = $this->get_related_products($id);
		//echo var_dump($relared_products);
		$this->load->library('grid');
		$this->grid->_init_grid('product_'.$id.'_related_products_grid', array('limit' => 1000, 'url' => set_url('catalogue/products_related/get_ajax_get_related/id/'.$id)), TRUE);
		$this->grid->init_fixed_buttons(FALSE);
		
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where_in("A.`".self::ID_PR."`", $relared_products)
			->where("A.`".self::ID_USERS."`", $this->id_users);
			
			/*if(count($relared_products)>0) 
			{
				$this->grid->db->where_in("A.`".self::ID_PR."`", $relared_products);
			}
			else
			{}*/
		
		$this->load->helper('catalogue/products_related_helper');
		get_related_grid_build($this->grid, $id);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		return $this->grid->render_grid(TRUE);
	}
	
	public function get_related($id)
	{
		$id = intval($id);
		$this->load->model('catalogue/mproducts');
		if(!$this->mproducts->check_isset_pr($id)) return FALSE;
		
		$query = $this->db->select("B.`name`")
				->from("`".self::PR."` AS A")
				->join("`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
				->where("A.`".self::ID_PR."`", $id)
				->where("A.`".self::ID_USERS."`", $this->id_users)
				->limit(1);
		$result = $query->get()->row_array();
		
		$this->template->add_navigation("Продукт: ".$result['name']);

		//$data['products'] = $this->add_related_grid_build($id);
		$relared_products = $this->get_related_products($id);
		if(count($relared_products)>0)
		{
			$data['is_related'] = $this->get_related_grid_build($id);
			$data['is_related'] .= $this->load->view('catalogue/products/show_product_js', array('show_products_block_id' => 'product_'.$id.'_related_products_grid'), TRUE);
		}
		//echo var_dump($data['is_related']);
		
		helper_get_related_form_build($data, $id);
		return TRUE;
		
	}
	
	public function add_related($id)
	{
		$id = intval($id);
		$this->load->model('catalogue/mproducts');
		if(!$this->mproducts->check_isset_pr($id)) return FALSE;
		
		$query = $this->db->select("B.`name`")
				->from("`".self::PR."` AS A")
				->join("`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
				->where("A.`".self::ID_PR."`", $id)
				->where("A.`".self::ID_USERS."`", $this->id_users)
				->limit(1);
		$result = $query->get()->row_array();
		
		$this->template->add_navigation("Добавление товаров к ".$result['name']);

		$data['products'] = $this->add_related_grid_build($id);
		$data['products'] .= $this->load->view('catalogue/products/show_product_js', array('show_products_block_id' => 'product_'.$id.'_not_related_grid'), TRUE);
				
		helper_add_related_form_build($data, $id);
		return TRUE;
		
	}
	
	public function save_related($id)
	{
		if($this->input->post('products_related_checkbox'))
		{
			$this->load->model('catalogue/mproducts');
			if(!$this->mproducts->check_isset_pr($id)) return FALSE;
			$id_related = $this->input->post('products_related_checkbox');
			$this->db->trans_start();
			foreach($id_related as $val)
			{
				$this->sql_add_data(array(self::ID_PR_REL => $val, self::ID_PR => $id))->sql_save(self::PR_REL);
			}
			$this->db->trans_complete();
			if($this->db->trans_status()) 
			{	
				return TRUE;
			}
			$this->messages->add_error_message('System error!');
			return FALSE;
		}
	}
	
	public function delete_related($id_parent, $id_related=false)
	{	
		if($id_related)
		{
			$this->db->where("`".self::ID_PR_REL."`",$id_related)->where("`".self::ID_PR."`",$id_parent )->delete("`".self::PR_REL."`");
			return TRUE;
		}
		else
		{
			if($id_related = $this->input->post('get_related_checkbox'))
			{
				$this->db->where_in("`".self::ID_PR_REL."`",$id_related)->where("`".self::ID_PR."`",$id_parent )->delete("`".self::PR_REL."`");
				return TRUE;
			}
		}
	}
	
	public function get_related_products($id)
	{
		$query = $this->db
				->select("A.`".self::ID_PR_REL."` AS ID")
				->from("`".self::PR_REL."` AS A")
				->where("A.`".self::ID_PR."`", $id);
		$result_arr = $query->get()->result_array();
		$result = array();
		foreach($result_arr as $val)
		{
			$result[] = $val['ID'];
		}
		return $result;		
	}
	

}

?>