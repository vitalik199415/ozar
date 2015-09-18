<?php
class Mproducts_similar extends AG_Model
{
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	const ID_PR_DESC 	= 'id_m_c_products_description';
	const PR_SIM		= 'm_c_products_similar';
	const ID_PR_SIM		= 'id_m_c_products_similar';
	
	
	function __construct()
	{
		parent::__construct();
	}
	public function render_product_similar_grid()
	{	
		$this->load->library('grid');
		$this->grid->_init_grid('products_similar_grid');
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`, A.`create_date`, A.`update_date`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('catalogue/products_similar_helper');
		helper_products_similar_grid_build($this->grid);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		$this->grid->render_grid();
	}
	
	public function add_similar_grid_build($id)
	{
		$this->load->model('catalogue/mproducts');
		if(!$this->mproducts->check_isset_pr($id)) return FALSE;
		
		$similar_products = $this->get_similar_products($id);
		$this->load->library('grid');
		$this->grid->_init_grid('product_'.$id.'_not_similar_grid', array('limit' => 1000, 'url' => set_url('catalogue/products_similar/get_ajax_add_similar/id/'.$id)), TRUE);
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_PR."` <>".$id)
			->where("A.`".self::ID_USERS."`", $this->id_users);
			if(count($similar_products)>0)
			{
				$this->grid->db->where_not_in("A.`".self::ID_PR."`", $similar_products);
			}
		$this->load->helper('catalogue/products_similar_helper');
		add_similar_grid_build($this->grid);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		return $this->grid->render_grid(TRUE);
	}
	
	public function get_similar_grid_build($id)
	{
		$this->load->model('catalogue/mproducts');
		if(!$this->mproducts->check_isset_pr($id)) return FALSE;
		
		$similar_products = $this->get_similar_products($id);
		//echo var_dump($relared_products);
		$this->load->library('grid');
		$this->grid->_init_grid('product_'.$id.'_similar_products_grid', array('limit' => 1000, 'url' => set_url('catalogue/products_similar/get_ajax_get_similar/id/'.$id)), TRUE);
		$this->grid->init_fixed_buttons(FALSE);
		
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, A.`in_stock`")
			->from("`".self::PR."` AS A")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where_in("A.`".self::ID_PR."`", $similar_products)
			->where("A.`".self::ID_USERS."`", $this->id_users);
			
			/*if(count($relared_products)>0) 
			{
				$this->grid->db->where_in("A.`".self::ID_PR."`", $relared_products);
			}
			else
			{}*/
		
		$this->load->helper('catalogue/products_similar_helper');
		get_similar_grid_build($this->grid, $id);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('in_stock',array('0'=>'Нет','1'=>'Да'));
		$this->grid->update_grid_data('status',array('0'=>'Нет','1'=>'Да'));
		return $this->grid->render_grid(TRUE);
		
		
	}
	
	public function get_similar($id)
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
		$similar_products = $this->get_similar_products($id);
		if(count($similar_products)>0)
		{
			$data['is_similar'] = $this->get_similar_grid_build($id);
			$data['is_similar'] .= $this->load->view('catalogue/products/show_product_js', array('show_products_block_id' => 'product_'.$id.'_similar_products_grid'), TRUE);
		}
		//echo var_dump($data['is_related']);
		
		helper_get_similar_form_build($data, $id);
		return TRUE;
		
	}
	
	public function add_similar($id)
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

		$data['products'] = $this->add_similar_grid_build($id);
		$data['products'] .= $this->load->view('catalogue/products/show_product_js', array('show_products_block_id' => 'product_'.$id.'_not_similar_grid'), TRUE);		
		helper_add_similar_form_build($data, $id);
		return TRUE;
		
	}
	
	public function save_similar($id)
	{
		if($this->input->post('products_similar_checkbox'))
		{
			$this->load->model('catalogue/mproducts');
			if(!$this->mproducts->check_isset_pr($id)) return FALSE;
			$id_similar = $this->input->post('products_similar_checkbox');
			$this->db->trans_start();
			foreach($id_similar as $val)
			{
				$this->sql_add_data(array(self::ID_PR_SIM => $val, self::ID_PR => $id))->sql_save(self::PR_SIM);
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
	
	public function delete_similar($id_parent, $id_similar=false)
	{	
		if($id_similar)
		{
			$this->db->where("`".self::ID_PR_SIM."`",$id_similar)->where("`".self::ID_PR."`",$id_parent )->delete("`".self::PR_SIM."`");
			return TRUE;
		}
		else
		{
			if($id_similar = $this->input->post('get_similar_checkbox'))
			{
				$this->db->where_in("`".self::ID_PR_SIM."`",$id_similar)->where("`".self::ID_PR."`",$id_parent )->delete("`".self::PR_SIM."`");
				return TRUE;
			}
		}
	}
	
	public function get_similar_products($id)
	{
		$query = $this->db
				->select("A.`".self::ID_PR_SIM."` AS ID")
				->from("`".self::PR_SIM."` AS A")
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