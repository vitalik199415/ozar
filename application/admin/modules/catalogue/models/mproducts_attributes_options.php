<?php
class Mproducts_attributes_options extends AG_Model
{
	const PR_ATTR_OPT 					= 'm_c_products_attributes_options';
	const ID_PR_ATTR_OPT 				= 'id_m_c_products_attributes_options';
	const PR_ATTR_OPT_DESC 		= 'm_c_products_attributes_options_description';
	const ID_PR_ATTR_OPT_DESC 	= 'id_m_c_products_attributes_options_description';
	
	const ATTRIBUTES = 'm_c_products_attributes';
	const ID_ATTRIBUTES = 'id_m_c_products_attributes';
	const ATTRIBUTES_DESC = 'm_c_products_attributes_description';

	const NATTRIBUTES 	= 'm_c_productsNattributes';
	
	const PNA = 'm_c_productsNattributes';
	
	public $id_attribute_option = FALSE;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function render_products_attributes_options_grid()
	{
		$this->load->model('catalogue/mproducts_attributes');
		$data['products_attributes'] = $this->mproducts_attributes->get_collection_to_select();
		
		$this->load->library('grid');
		$this->grid->_init_grid('products_attributes_options_grid', array('sort' => self::ID_ATTRIBUTES));
		
		$this->grid->db
				->select("A.`".self :: ID_PR_ATTR_OPT."` AS ID, A.`sort`, A.`alias`, A.`".self::ID_ATTRIBUTES."`, A.`active`, B.`name`, B.`description`")
				->from("`".self :: PR_ATTR_OPT."` AS A")
				->join("`".self :: PR_ATTR_OPT_DESC."` AS B",
						"B.`".self :: ID_PR_ATTR_OPT."` = A.`".self :: ID_PR_ATTR_OPT."` && B.`".self::ID_LANGS."` = ".$this->id_langs, 
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users);
					
				
		$this->load->helper('catalogue/products_attributes_options_helper');
		
		helper_products_attributes_options_grid_build($this->grid, $data);
		$this->grid->add_extra_sort('sort');
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0'=>'Нет', '1'=>'Да'));
		$this->grid->update_grid_data_using_string("sort", "<a class='arrow_down' href='".set_url('*/*/change_position/')."id/$1/type/down' title='Смена позиции: Опустить'></a><a class='arrow_up' href='".set_url('*/*/change_position/')."id/$1/type/up' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
		$this->grid->update_grid_data(self::ID_ATTRIBUTES, $data['products_attributes']);
		$this->grid->render_grid();
	}
	
	public function add()
	{
		$this->load->helper('agform_helper');
		$this->load->helper('catalogue/products_attributes_options_helper');	
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		$this->load->model('catalogue/mproducts_attributes');
		$data['products_attributes'] = $this->mproducts_attributes->get_collection_to_select();
		if(count($data['products_attributes']) == 0)
		{
			$CI = & get_instance();
			$CI->redirect = set_url('*/products_attributes/add');
			$this->messages->add_error_message('Атрибуты продукции отсутствуют, добавление опции атрибута не возможно.<br>Вы переадресированы на страницу добавления атрибутов продукции.');
			return FALSE;
		}
		helper_attributes_options_form_build($data);
	}
	
	public function edit($id)
	{
		$result = $this->_get_edit_query($id);
		$result = $result->get()->result_array();
		$data = array();
		if(count($result) > 0)
		{
			foreach($result as $ms)
			{
				$data['main']['alias'] = $ms['alias'];
				$data['main']['active'] = $ms['active'];
				$data['main'][self::ID_ATTRIBUTES] = $ms[self::ID_ATTRIBUTES];
				$data['desc'][$ms['id_langs']] = $ms;
				unset($data['desc'][$ms['id_langs']]['ID']);
				unset($data['desc'][$ms['id_langs']]['alias']);
				unset($data['desc'][$ms['id_langs']]['active']);
				unset($data['desc'][$ms['id_langs']][self::ID_ATTRIBUTES]);
			}
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			$this->load->model('catalogue/mproducts_attributes');
			$data['products_attributes'] = $this->mproducts_attributes->get_collection_to_select();
			$this->load->helper('agform_helper');
			$this->load->helper('catalogue/products_attributes_options_helper');
			
			helper_attributes_options_form_build($data, '/id/'.$id);
			return TRUE;
		}
		return FALSE;
	}
	
	public function check_isset_products_attribute($id)
	{
		$this->load->model('catalogue/mproducts_attributes');
		return $this->mproducts_attributes->check_isset_attribute($id);
	}
	
	public function check_isset_alias($alias)
	{
		$alias = trim($alias);
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::PR_ATTR_OPT."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`alias`", $alias)->limit(1);
		if($this->id_attribute_option)
		{
			$query->where("`".self::ID_PR_ATTR_OPT."` <>", $this->id_attribute_option);
		}
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function set_validation()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('main[alias]','Индификатор','trim|required|check_isset_alias');
		$this->form_validation->set_message('check_isset_alias', 'Свойство продукции с указанным индификатором уже существует!');
		
		$this->form_validation->set_rules('main['.self::ID_ATTRIBUTES.']','Атрибут продукции','required|is_natural_no_zero|check_isset_products_attribute');
		$this->form_validation->set_message('check_isset_products_attribute', 'Выбраного атрибута продукции не существует!');
	}
	
	public function save($id = false)
	{
		if($this->input->post('main'))
		{
			if($id)
			{
				//$this->id_attribute = $id;
				$query = $this->db->select("B.`".self::ID_PR_ATTR_OPT_DESC."`, B.`".self::ID_LANGS."`")
							->from("`".self::PR_ATTR_OPT."` AS A")
							->join("`".self::PR_ATTR_OPT_DESC."` AS B",
									"B.`".self::ID_PR_ATTR_OPT."` = A.`".self::ID_PR_ATTR_OPT."`",
									"LEFT")
							->where("A.`".self::ID_PR_ATTR_OPT."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users);
				$lang_result_temp = $query->get()->result_array();
				if(count($lang_result_temp)>0)
				{
					$lang_result = array();
					foreach($lang_result_temp as $ms)
					{
						$lang_result[$ms['id_langs']] = $ms;
					}
					
					$POST = $this->input->post('main');
					$MPOST = array('alias' => $POST['alias'], 'active' => $POST['active'], self::ID_ATTRIBUTES => $POST[self::ID_ATTRIBUTES]);
					
					$this->db->trans_start();
					$result = $this->sql_add_data($MPOST)->sql_using_user()->sql_save(self::PR_ATTR_OPT, $id);
					if($result && ($POST = $this->input->post('desc')) != FALSE)
					{
						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->get_active_languages();
						foreach($langs as $key => $ms)
						{
							if(isset($POST[$key]))
							{
								if(isset($lang_result[$key])) 
								{
									$this->sql_add_data($POST[$key])->sql_save(self::PR_ATTR_OPT_DESC, $lang_result[$key][self::ID_PR_ATTR_OPT_DESC]);
								}
								else
								{
									$this->sql_add_data($POST[$key]+array(self::ID_PR_ATTR_OPT => $id, self::ID_LANGS => $key))->sql_save(self::PR_ATTR_OPT_DESC);
								}
							}
						}
						$this->db->trans_complete();
						if($this->db->trans_status()) 
						{
							return TRUE;
						}
					}
					return FALSE;
				}
				return FALSE;
				}
				else
				{
					$POST = $this->input->post('main');
					
					$this->db->trans_start();
					$ID = $this->sql_add_data($POST)->sql_using_user()->sql_save(self :: PR_ATTR_OPT);
					if($ID && $ID > 0 && ($POST = $this->input->post('desc')) != FALSE)
					{
						$this->sql_add_data(array('sort' => $ID))->sql_using_user()->sql_save(self :: PR_ATTR_OPT, $ID);
						$this->load->model('langs/mlangs');
						$langs = $this->mlangs->get_active_languages();
						foreach($langs as $key => $ms)
						{
							if(isset($POST[$key]))
							{
								$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_PR_ATTR_OPT => $ID);
								$this->sql_add_data($data)->sql_save(self :: PR_ATTR_OPT_DESC);
							}
						}
						$this->db->trans_complete();
						if($this->db->trans_status()) 
						{
							return $ID; 
						}
						else
						{
							$this->set_post_to_session();
							return FALSE;
						}
					}
					return FALSE;
				}
				
			}
					
		return FALSE;	
	}
	
	public function set_post_to_session()
	{
		$this->session->set_flashdata('products_attributes_options_add_edit_form', $this->input->post());
		return $this;
	}
	
	private function _get_edit_query($id, $id_langs = FALSE)
	{
		if($id_langs)
		{
			$select = "B.`id_langs`, B.`".self :: ID_PR_ATTR_OPT_DESC."` AS DID";
		}
		else 
		{
			$select = "A.`".self :: ID_PR_ATTR_OPT."` AS id, A.`alias`, A.`active`, B.`name`, B.`description`, B.`id_langs`, B.`".self :: ID_PR_ATTR_OPT_DESC."`, A.`".self::ID_ATTRIBUTES."`";
		}
	   	$result = $this->db -> select($select)
							-> from("`" .self :: PR_ATTR_OPT."` AS A")
							-> join("`" .self :: PR_ATTR_OPT_DESC."` AS B", "B.`" .self :: ID_PR_ATTR_OPT."` = A.`" .self :: ID_PR_ATTR_OPT."`", "left")
							-> where("A.`".self :: ID_PR_ATTR_OPT."`", $id)->where("A.`id_users`", $this->id_users);
				return $result;
	}
	
	public function delete($id)
	{
		if(is_array($id))
		{
			$this->db->where_in(self :: ID_PR_ATTR_OPT, $id)->where("`".self::ID_USERS."`", $this->id_users);
			$this->db->delete(self :: PR_ATTR_OPT);
			return TRUE;
		}
		
		$result = $this->db	->select("count(*) AS COUNT")
							->from("`".self :: PR_ATTR_OPT."` AS A")
							->where("A.`".self :: ID_PR_ATTR_OPT."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $result->get()->row_array();
		if($result['COUNT'] > 0)
		{
			$this->db->where(self :: ID_PR_ATTR_OPT, $id)->where("`".self::ID_USERS."`", $this->id_users);
			if($this->db->delete(self :: PR_ATTR_OPT))
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function activate($id, $active = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $active);
			foreach($id as $ms)
			{
				$this->sql_add_data($data)->sql_using_user()->sql_save(self :: PR_ATTR_OPT, $ms);
			}
			return TRUE;			
		}
		return FALSE;
	}
	
	public function change_position($id, $type)
	{
		switch($type)
		{
			case "up":
				if($c_id = $this->_change_position_query('<=', $id))
				{
					return TRUE;
				}
				return FALSE;
			break;
			case "down":
				if($c_id = $this->_change_position_query('>=', $id))
				{
					return TRUE;
				}
				return FALSE;
			break;
		}
		return FALSE;
	}
	
	private function _change_position_query($type, $id)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$query = $this->db
			->select("DISTINCT(A.`".self::ID_PR_ATTR_OPT."`) AS ID, A.`sort` AS SORT, A.`".self::ID_ATTRIBUTES."`")
			->from("`".self::PR_ATTR_OPT."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users)
			->where("A.`".self::ID_ATTRIBUTES."` <=> (SELECT `".self::ID_ATTRIBUTES."` FROM `".self::PR_ATTR_OPT."` WHERE `".self::ID_PR_ATTR_OPT."` = '".$id."' LIMIT 1) && A.`sort` ".$type." (SELECT `sort` FROM `".self::PR_ATTR_OPT."` WHERE `".self::ID_PR_ATTR_OPT."` = ".$id." LIMIT 1)")
			->order_by('sort', $OB)->limit(2);
		//echo $this->db->_compile_select();					

		$query = $query->get();
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();
			if($result[0][self::ID_ATTRIBUTES] == $result[1][self::ID_ATTRIBUTES])
			{
				$ID = $result[0]['ID'];
				$SORT = $result[0]['SORT'];
				
				$id = $result[1]['ID'];
				$sort = $result[1]['SORT'];

				$this->db->trans_start();
				$this->sql_add_data(array('sort' => $SORT))->sql_save(self::PR_ATTR_OPT, $id);
				$this->sql_add_data(array('sort' => $sort))->sql_save(self::PR_ATTR_OPT, $ID);
				$this->db->trans_complete();
				if($this->db->trans_status()) 
				{
					return TRUE; 
				}
				return FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function get_attributes_N_options()
	{
		$this->load->model('catalogue/mproducts_attributes');
		$data['attributes'] = array();
		$data['attributes_options'] = array();
		if($data['attributes'] = $this->mproducts_attributes->get_collection_to_select())
		{
			$attr_id = array_keys($data['attributes']);
			
			$query = $this->db->select("A.`".self :: ID_PR_ATTR_OPT."` AS ID, A.`alias`, A.`".self::ID_ATTRIBUTES."` AS PID, B.`name`")
							->from("`".self :: PR_ATTR_OPT."` AS A")
							->join(	"`".self :: PR_ATTR_OPT_DESC."` AS B",
									"B.`".self :: ID_PR_ATTR_OPT."` = A.`".self :: ID_PR_ATTR_OPT."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
									"left")
							->where("A.`active`", 1)
							->where_in("A.`".self::ID_ATTRIBUTES."`", $attr_id)->order_by("A.`sort`");
			
			$result = $query->get()->result_array();
			foreach($result as $ms)
			{
				$data['attributes_options'][$ms['PID']][$ms['ID']] = $ms['alias'].' - '.$ms['name'];
			}
		}	
		return $data;				
	}
	
	public function get_products_attributes_and_options($id)
	{
		$query = $this->db
			->select("A.`".self::ID_ATTRIBUTES."` AS ID, B.`".self::ID_PR_ATTR_OPT."` AS ID_OP, C.`name` AS a_name, A.`alias` AS a_alias, D.`name` AS o_name, B.alias AS o_alias")
			->from("`".self::ATTRIBUTES."` AS A");
		if(is_array($id))
		{
			$IN = '';
			foreach($id as $ms)
			{
				$IN .= $ms.',';
			}
			$IN = substr($IN, 0, -1);
			$query	->join(	"`".self::PR_ATTR_OPT."` AS B",
							"B.`".self::ID_PR_ATTR_OPT."` IN(SELECT `".self::ID_PR_ATTR_OPT."` FROM `".self::PNA."` WHERE `id_m_c_products` IN (".$IN.") && `".self::ID_PR_ATTR_OPT."` IS NOT NULL) && B.`".self::ID_ATTRIBUTES."` = A.`".self::ID_ATTRIBUTES."`",
							"inner");
			unset($IN);		
		}
		else if(intval($id)>0)
		{
			$query	->join(	"`".self::PR_ATTR_OPT."` AS B",
							"B.`".self::ID_PR_ATTR_OPT."` IN(SELECT `".self::ID_PR_ATTR_OPT."` FROM `".self::PNA."` WHERE `id_m_c_products` = '".intval($id)."' && `".self::ID_PR_ATTR_OPT."` IS NOT NULL) && B.`".self::ID_ATTRIBUTES."` = A.`".self::ID_ATTRIBUTES."`",
							"inner");
		}
		$query
			->join(	"`".self::ATTRIBUTES_DESC."` AS C",
					"C.`".self::ID_ATTRIBUTES."` = A.`".self::ID_ATTRIBUTES."` && C.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"left")
			->join(	"`".self::PR_ATTR_OPT_DESC."` AS D",
					"D.`".self::ID_PR_ATTR_OPT."` = B.`".self::ID_PR_ATTR_OPT."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"inner")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->result_array();
		$array = array();
		foreach($result as $ms)
		{
			$array[$ms['ID']][$ms['ID_OP']] = $ms;
		}
		unset($result);
		return $array;
	}
}
?>