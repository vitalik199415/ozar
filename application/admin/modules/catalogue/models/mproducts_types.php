<?php 
class Mproducts_types extends AG_Model
{
	const PR_TYPES			= 'm_c_products_types';
	const ID_PR_TYPES		= 'id_m_c_products_types';
	const PR_TYPES_DESC		= 'm_c_products_types_description';
	const ID_PR_TYPES_DESC 	= 'id_m_c_products_types_description';

	const PR_PROP			= 'm_c_products_properties';
	const ID_PR_PROP		= 'id_m_c_products_properties';
	const PR_PROP_DESC		= 'm_c_products_properties_description';
	const ID_PR_PROP_DESC	= 'id_m_c_products_properties_description';

	const IMG_FOLDER = '/media/catalogue/products_properties/';

	private $img_load_path = FALSE;
	private $img_path = FALSE;

	public $id_type = FALSE;
	public $id_prop = FALSE;

	function __construct()
	{
		parent::__construct();
		$this->img_load_path = BASE_PATH.'users/'.$this->id_users.self::IMG_FOLDER;
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
	}

	public static function get_filters_types()
	{
		return array('checkbox' => 'Чекбоксы', 'color' => 'Цвет', 'color_name' => 'Цвет + Текст', 'image' => 'Изображения');
	}

	public function get_collection_to_select()
	{
		return $this->get_types_collection_to_select();
	}

	public function get_types_collection_to_select()
	{
		$types = array();
		$this->db ->select("A.`".self :: ID_PR_TYPES."` AS ID, A.`alias`, B.`name`")
			->from("`".self :: PR_TYPES."` AS A")
			->join( "`".self :: PR_TYPES_DESC."` AS B",
					"B.`".self :: ID_PR_TYPES."` = A.`".self :: ID_PR_TYPES."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->order_by("A.`sort`");
	  
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$types[$ms['ID']] = $ms['alias'].' - '.$ms['name'];
		}
		return $types;
	}

	public function get_products_type($id)
	{
		$this->db->select("A.`".self::ID_PR_TYPES."`, A.`alias`, A.`type_kind`, A.`active`, B.`name`")
			->from("`".self::PR_TYPES."` AS A")
			->join("`".self::PR_TYPES_DESC."` AS B",
				"B.`".self::ID_PR_TYPES."` = A.`".self::ID_PR_TYPES."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
				"LEFT")
			->where("A.`".self::ID_PR_TYPES."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($result = $this->db->get()->row_array()) > 0) return $result;
		return FALSE;
	}
	
	public function render_types_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("products_types_grid", array(), TRUE);
		
		$this->grid->db	
			->select("A.`".self::ID_PR_TYPES."` AS ID, A.`show_id`, A.`type_kind`, A.`sort`, A.`active`, A.`alias`, B.`name`")
			->from("`".self::PR_TYPES."` AS A")
			->join("`".self::PR_TYPES_DESC."` AS B", 
				   "B.`".self::ID_PR_TYPES."` = A.`".self::ID_PR_TYPES."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
				   "LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('products_types');
		helper_products_types_grid_build($this->grid);
		$this->grid->add_extra_sort('sort');
		$this->grid->create_grid_data();
		$this->grid->update_grid_data_using_string("sort", "<a class='arrow_down' href='".set_url('*/*/change_position/')."id/$1/type/down' title='Смена позиции: Опустить'></a><a class='arrow_up' href='".set_url('*/*/change_position/')."id/$1/type/up' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));

		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('type_kind', self::get_filters_types());
		$this->grid->render_grid();
	}
	
	public function add()
	{
		$this->load->helper('products_types');
		
		$this->load->model('langs/mlangs'); 
		$data['on_langs'] = $this->mlangs->get_active_languages();
		helper_products_types_form_build($data);
	}

	public function edit($id)
	{
		$this->db ->select("A.`alias`, A.`type_kind`, A.`active`, B.`name`, B.`description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`, B.`id_langs`")
			->from("`".self::PR_TYPES."` AS A")
			->join("`".self::PR_TYPES_DESC."` AS B",
				"B.`".self::ID_PR_TYPES."` = A.`".self::ID_PR_TYPES."`",
				"LEFT")
			->where("A.`".self::ID_PR_TYPES."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users);

		$result = $this->db->get()->result_array();
		$data = array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$data['main'] = $ms;
				$data['desc'][$ms['id_langs']] = $ms;
			}
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			$this->load->helper('products_types');
			helper_products_types_form_build($data,'/id/'.$id);
			return TRUE;
		}
		return FALSE;
	}
	
	public function check_isset_type($id)
	{
		$id = intval($id);
		$this->db->select("COUNT(*) AS COUNT")->from("`".self::PR_TYPES."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_PR_TYPES."`", $id)->limit(1);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function check_isset_alias($alias)
	{
		$alias = trim($alias);
		if($alias == '') return TRUE;
		$this->db->select("COUNT(*) AS COUNT")->from("`".self::PR_TYPES."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`alias`", $alias)->limit(1);
		if($this->id_type)
		{
			$this->db->where("`".self::ID_PR_TYPES."` <>", $this->id_type);
		}
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function set_validation($id = FALSE)
	{
		if($id) $this->id_type = $id;

		$this->load->library('form_validation');
		$this->form_validation->add_callback_function_class('check_isset_alias', 'mproducts_types');
		$this->form_validation->set_rules('main[alias]', 'Идентификатор', 'trim|callback_check_isset_alias');
		$this->form_validation->set_message('check_isset_alias', 'Группа с указанным идентификатором уже существует!');

		if(!$this->form_validation->run()) { $this->messages->add_error_message(validation_errors()); return FALSE; }

		return TRUE;
	}
	
	public function save($id = FALSE)
	{
		if(!$this->set_validation($id)) return FALSE;
		if($id)
		{
			if(!$this->check_isset_type($id)) return FALSE;
			$this->db->select("B.`".self::ID_PR_TYPES_DESC."`, B.`".self::ID_LANGS."`")
				->from("`".self::PR_TYPES."` AS A")
				->join("`".self::PR_TYPES_DESC."` AS B",
						"B.`".self::ID_PR_TYPES."` = A.`".self::ID_PR_TYPES."`",
						"LEFT")
				->where("A.`".self::ID_PR_TYPES."`", $id)->where("A.`".self::ID_USERS."`", $this->id_users);
			$lang_result_temp = $this->db->get()->result_array();

			$lang_result = array();
			foreach($lang_result_temp as $ms)
			{
				$lang_result[$ms[self::ID_LANGS]] = $ms;
			}

			$POST = $this->input->post('main');

			$this->db->trans_start();
			$result = $this->sql_add_data($POST)->sql_using_user()->sql_save(self::PR_TYPES, $id);
			if($result)
			{
				$POST = $this->input->post('desc');
				$this->load->model('langs/mlangs');
				$langs = $this->mlangs->get_active_languages();
				foreach($langs as $key => $ms)
				{
					if(isset($POST[$key]))
					{
						if(trim($POST[$key]['seo_title']) == '') $POST[$key]['seo_title'] = $POST[$key]['name'];
						if(trim($POST[$key]['seo_description']) == '') $POST[$key]['seo_description'] = $POST[$key]['name'];
						if(trim($POST[$key]['seo_keywords']) == '') $POST[$key]['seo_keywords'] = $POST[$key]['name'];

						if(isset($lang_result[$key]))
						{
							$this->sql_add_data($POST[$key])->sql_save(self::PR_TYPES_DESC, $lang_result[$key][self::ID_PR_TYPES_DESC]);
						}
						else
						{
							$this->sql_add_data($POST[$key]+array(self::ID_PR_TYPES => $id, self::ID_LANGS => $key))->sql_save(self::PR_TYPES_DESC);
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
		else
		{
			$POST = $this->input->post('main');

			$this->db->select("MAX(`show_id`) AS show_id")
				->from("`".self::PR_TYPES."`")
				->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
			$MAX = $this->db->get()->row_array();
			$max = 1;
			if(count($MAX)>0)
			{
				$max = $MAX['show_id'] + 1;
			}

			$this->db->trans_start();
			$ID = $this->sql_add_data($POST + array('show_id' => $max, 'sort' => $max))->sql_using_user()->sql_save(self :: PR_TYPES);
			if($ID > 0)
			{
				$POST = $this->input->post('desc');
				$this->load->model('langs/mlangs');
				$langs = $this->mlangs->get_active_languages();
				foreach($langs as $key => $ms)
				{
					if(isset($POST[$key]))
					{
						if(trim($POST[$key]['seo_title']) == '') $POST[$key]['seo_title'] = $POST[$key]['name'];
						if(trim($POST[$key]['seo_description']) == '') $POST[$key]['seo_description'] = $POST[$key]['name'];
						if(trim($POST[$key]['seo_keywords']) == '') $POST[$key]['seo_keywords'] = $POST[$key]['name'];

						$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_PR_TYPES => $ID);
						$this->sql_add_data($data)->sql_save(self :: PR_TYPES_DESC);
					}
				}
				$this->db->trans_complete();
				if($this->db->trans_status())
				{
					return $ID;
				}
			}
			return FALSE;
		}
	}

	public function delete($id)
	{
		$this->load->helper('agfiles_helper');
		if(is_array($id))
		{
			$this->db->where_in(self::ID_PR_TYPES, $id)->where("`".self::ID_USERS."`", $this->id_users);
			$this->db->delete(self::PR_TYPES);

			foreach($id as $ms)
			{
				$path = $this->img_load_path.$ms;
				remove_dir($path);
			}
			return TRUE;
		}
		$this->db->select("count(*) AS COUNT")
			->from("`".self :: PR_TYPES."` AS A")
			->where("A.`".self :: ID_PR_TYPES."`", $id)->where("`A.`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] > 0)
		{

			$this->db->where(self::ID_PR_TYPES, $id)->where("`".self::ID_USERS."`", $this->id_users);
			if($this->db->delete(self::PR_TYPES))
			{
				$path = $this->img_load_path.$id;
				remove_dir($path);
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}

	public function change_position($id, $type)
	{
		switch($type)
		{
			case "up":
				return $c_id = $this->_change_position_query('<=', $id);
				break;
			case "down":
				return $c_id = $this->_change_position_query('>=', $id);
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
		$this->db
			->select("DISTINCT(A.`".self::ID_PR_TYPES."`) AS ID, A.`sort` AS SORT ")
			->from("`".self::PR_TYPES."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users)
			->where("`sort` ".$type." (SELECT `sort` FROM `".self::PR_TYPES."` WHERE `".self::ID_PR_TYPES."` = '".$id."' LIMIT 1)")
			->order_by('sort', $OB)->limit(2);

		$query = $this->db->get();
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();

			$ID = $result[0]['ID'];
			$SORT = $result[0]['SORT'];

			$id = $result[1]['ID'];
			$sort = $result[1]['SORT'];

			$this->db->trans_start();
			$this->sql_add_data(array('sort' => $SORT))->sql_save(self::PR_TYPES, $id);
			$this->sql_add_data(array('sort' => $sort))->sql_save(self::PR_TYPES, $ID);
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}

	public function activate($id, $activate = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $activate);
			foreach($id as $ms)
			{
				$this->sql_add_data($data)->sql_using_user()->sql_save(self::PR_TYPES, $ms);
			}
			return TRUE;
		}
		return FALSE;
	}



	//PROPERTIES




	public function type_properties($type_id)
	{
		$filter_group = $this->get_products_type($type_id);
		$this->template->add_title(" | ".$filter_group['name']." (".$filter_group['alias'].")");
		$this->template->add_navigation($filter_group['name']." (".$filter_group['alias'].")");

		$this->load->library('grid');
		$this->grid->_init_grid('products_types_properties_grid_'.$type_id, array(), TRUE);

		$this->grid->db
			->select("A.`".self::ID_PR_PROP."` AS ID, A.`sort`, A.`show_id`, A.`alias`, A.`".self::ID_PR_TYPES."`, A.`active`, B.`name`")
			->from("`".self::PR_PROP."` AS A")
			->join("`".self::PR_PROP_DESC."` AS B",
				"B.`".self::ID_PR_PROP."` = A.`".self::ID_PR_PROP."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
				"LEFT")
			->where("A.`".self::ID_PR_TYPES."`", $type_id)
			->where("A.`".self::ID_USERS."`", $this->id_users);

		$this->load->helper('products_types');

		helper_type_properties_grid_build($this->grid, $type_id);
		$this->grid->add_extra_sort('sort');
		$this->grid->create_grid_data();
		$this->grid->update_grid_data_using_string("sort", "<a class='arrow_down' href='".set_url('*/*/change_property_position/')."type_id/".$type_id."/prop_id/$1/type/down' title='Смена позиции: Опустить'></a><a class='arrow_up' href='".set_url('*/*/change_property_position/')."type_id/".$type_id."/prop_id/$1/type/up' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));

		$this->grid->render_grid();
		return true;
	}

	public function add_property($type_id)
	{
		if(!$filter_group = $this->get_products_type($type_id)) return FALSE;
		$this->template->add_title(" | ".$filter_group['name']." (".$filter_group['alias'].")");
		$this->template->add_title(" | Добавить свойство");

		$this->template->add_navigation($filter_group['name']." (".$filter_group['alias'].")", set_url('*/*/type_properties/type_id/'.$type_id));
		$this->template->add_navigation("Добавить свойство");

		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		$data['type_id'] = $type_id;
		$data['type_kind'] = $filter_group['type_kind'];

		$this->load->helper('products_types');
		helper_add_edit_property_form_build($data);
		return TRUE;
	}

	public function edit_property($type_id, $prop_id)
	{
		if(!$filter_group = $this->get_products_type($type_id)) return FALSE;
		$this->template->add_title(" | ".$filter_group['name']." (".$filter_group['alias'].")");
		$this->template->add_title(" | Редактировать свойство");

		$this->template->add_navigation($filter_group['name']." (".$filter_group['alias'].")", set_url('*/*/type_properties/type_id/'.$type_id));
		$this->template->add_navigation("Редактировать свойство");

		$this->db->select("A.`".self::ID_PR_PROP."`, A.`alias`, A.`active`, A.`id_color`, A.`image`,
					B.`name`, B.`description`, B.`seo_title`, B.`seo_description`, B.`seo_keywords`, B.`id_langs`")
			->from("`".self::PR_PROP."` AS A")
			->join("`".self::PR_PROP_DESC."` AS B",
				"B.`".self::ID_PR_PROP."` = A.`".self::ID_PR_PROP."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
				"LEFT")
			->where("A.`".self::ID_PR_PROP."`", $prop_id)
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->result_array();

		$data = array();
		if(count($result) > 0)
		{
			foreach($result as $ms)
			{
				$data['main'] = $ms;
				$data['desc'][$ms['id_langs']] = $ms;
			}
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			$data['type_id'] = $type_id;
			$data['type_kind'] = $filter_group['type_kind'];
			if($data['type_kind'] == 'image')
			{
				if($data['main']['image'] != '')
				{
					$data['property_image'] = $this->img_path.$type_id.'/'.$prop_id.'/'.$data['main']['image'];
				}
			}

			$this->load->helper('products_types');
			helper_add_edit_property_form_build($data,'/prop_id/'.$prop_id);
			return TRUE;
		}
		return FALSE;
	}

	public function check_isset_property($prop_id)
	{
		$id = intval($prop_id);
		$this->db->select("COUNT(*) AS COUNT")->from("`".self::PR_PROP."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_PR_PROP."`", $id)->limit(1);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function check_isset_property_alias($alias)
	{
		$alias = trim($alias);
		if($alias == '') return TRUE;
		$this->db->select("COUNT(*) AS COUNT")->from("`".self::PR_PROP."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`alias`", $alias)->limit(1);
		if($this->id_prop)
		{
			$this->db->where("`".self::ID_PR_PROP."` <>", $this->id_prop);
		}
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function set_property_validation($prop_id = FALSE)
	{
		if($prop_id) $this->id_prop = $prop_id;

		$this->load->library('form_validation');
		$this->form_validation->add_callback_function_class('check_isset_property_alias', 'mproducts_types');
		$this->form_validation->set_rules('main[alias]', 'Идентификатор', 'trim|callback_check_isset_property_alias');
		$this->form_validation->set_message('check_isset_property_alias', 'Свойство с указанным идентификатором уже существует!');

		if(!$this->form_validation->run()) { $this->messages->add_error_message(validation_errors()); return FALSE; }

		return TRUE;
	}

	public function save_property($type_id, $prop_id = FALSE)
	{
		if(!$this->check_isset_type($type_id)) return FALSE;
		if(!$this->set_property_validation($prop_id)) return FALSE;

		if($prop_id)
		{
			if(!$this->check_isset_property($prop_id)) return FALSE;
			$this->db->select("B.`".self::ID_PR_PROP_DESC."`, B.`".self::ID_LANGS."`")
				->from("`".self::PR_PROP."` AS A")
				->join("`".self::PR_PROP_DESC."` AS B",
					"B.`".self::ID_PR_PROP."` = A.`".self::ID_PR_PROP."`",
					"LEFT")
				->where("A.`".self::ID_PR_PROP."`", $prop_id)->where("A.`".self::ID_USERS."`", $this->id_users);
			$lang_result_temp = $this->db->get()->result_array();

			$lang_result = array();
			foreach($lang_result_temp as $ms)
			{
				$lang_result[$ms['id_langs']] = $ms;
			}

			$POST = $this->input->post('main');
			if(isset($_FILES['property_image']))
			{
				foreach (glob($this->img_load_path.$type_id.'/'.$prop_id.'/*') as $file)
				{
					unlink($file);
				}
				if($file_data = $this->upload_property_image($type_id, $prop_id))
				{
					$POST['image'] = $file_data['file_name'];
				}
			}

			$this->db->trans_start();
			$result = $this->sql_add_data($POST)->sql_using_user()->sql_save(self::PR_PROP, $prop_id);
			if($result)
			{
				$POST = $this->input->post('desc');
				$this->load->model('langs/mlangs');
				$langs = $this->mlangs->get_active_languages();

				foreach($langs as $key => $ms)
				{
					if(isset($POST[$key]))
					{
						if(trim($POST[$key]['seo_title']) == '') $POST[$key]['seo_title'] = $POST[$key]['name'];
						if(trim($POST[$key]['seo_description']) == '') $POST[$key]['seo_description'] = $POST[$key]['name'];
						if(trim($POST[$key]['seo_keywords']) == '') $POST[$key]['seo_keywords'] = $POST[$key]['name'];

						if(isset($lang_result[$key]))
						{
							$this->sql_add_data($POST[$key])->sql_save(self::PR_PROP_DESC, $lang_result[$key][self::ID_PR_PROP_DESC]);
						}
						else
						{
							$this->sql_add_data($POST[$key]+array(self::ID_PR_PROP => $prop_id, self::ID_LANGS => $key))->sql_save(self::PR_PROP_DESC);
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
		else
		{
			$POST = $this->input->post('main');
			$this->db->select("MAX(`show_id`) AS show_id")
					 ->from("`".self::PR_PROP."`")
					 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
			$MAX = $this->db->get()->row_array();
			$max = 1;
			if(count($MAX)>0)
			{
				$max = $MAX['show_id'] + 1;
			}

			$this->db->trans_start();
			$prop_id = $this->sql_add_data($POST + array(self::ID_PR_TYPES => $type_id, 'show_id' => $max, 'sort' => $max))->sql_using_user()->sql_save(self :: PR_PROP);
			if($prop_id > 0)
			{
				if(isset($_FILES['property_image']))
				{
					if($file_data = $this->upload_property_image($type_id, $prop_id))
					{
						$this->sql_add_data(array('image' => $file_data['file_name']))->sql_save(self :: PR_PROP, $prop_id);
					}
				}

				$POST = $this->input->post('desc');
				$this->load->model('langs/mlangs');
				$langs = $this->mlangs->get_active_languages();
				foreach($langs as $key => $ms)
				{
					if(isset($POST[$key]))
					{
						if(trim($POST[$key]['seo_title']) == '') $POST[$key]['seo_title'] = $POST[$key]['name'];
						if(trim($POST[$key]['seo_description']) == '') $POST[$key]['seo_description'] = $POST[$key]['name'];
						if(trim($POST[$key]['seo_keywords']) == '') $POST[$key]['seo_keywords'] = $POST[$key]['name'];

						$data = $POST[$key] + array('id_langs' => $key) + array(self :: ID_PR_PROP => $prop_id);
						$this->sql_add_data($data)->sql_save(self :: PR_PROP_DESC);
					}
				}

				$this->db->trans_complete();
				if($this->db->trans_status())
				{
					return $prop_id;
				}
				return FALSE;
			}
			return FALSE;
		}
	}

	public function upload_property_image($type_id, $prop_id)
	{
		$dir = $this->img_load_path.$type_id.'/'.$prop_id;
		if(!is_dir($dir))
		{
			$this->load->helper('agfiles_helper');
			create_dir($dir, 2);
		}

		$config['upload_path'] = $this->img_load_path.$type_id.'/'.$prop_id.'/';
		$config['allowed_types'] = 'jpg|jpeg';
		$config['max_size']	= '11000';
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);
		if($this->upload->do_upload('property_image'))
		{
			$file_data = $this->upload->data();
			$conf_array['source_image'] = $config['upload_path'].$file_data['file_name'];
			$conf_array['width'] = 600;
			$conf_array['height'] = 600;
			$conf_array['create_thumb'] = FALSE;
			$conf_array['quality'] = 95;
			$this->load->library('image_lib', $conf_array);
			$this->image_lib->resize();
			$this->image_lib->clear();
			return $file_data;
		}
		else
		{
			return FALSE;
		}
	}

	public function delete_property($type_id, $prop_id)
	{
		$this->load->helper('agfiles_helper');
		if(is_array($prop_id))
		{
			$this->db->where_in(self::ID_PR_PROP, $prop_id)->where("`".self::ID_USERS."`", $this->id_users);
			$this->db->delete(self::PR_PROP);

			foreach($prop_id as $prop)
			{
				$path = $this->img_load_path.$type_id.'/'.$prop;
				remove_dir($path);
			}
		}

		$this->db->where(self::ID_PR_PROP, $prop_id)->where("`".self::ID_USERS."`", $this->id_users);
		if($this->db->delete(self::PR_PROP))
		{
			$path = $this->img_load_path.$type_id.'/'.$prop_id;
			remove_dir($path);
			return TRUE;
		}
		return FALSE;
	}

	public function activate_property($id, $activate = 1)
	{
		if(is_array($id))
		{
			$data = array('active' => $activate);
			foreach($id as $ms)
			{
				$this->sql_add_data($data)->sql_using_user()->sql_save(self::PR_PROP, $ms);
			}
			return TRUE;
		}
		return FALSE;
	}

	public function change_property_position($id, $type)
	{
		switch($type)
		{
			case "up":
				return $c_id = $this->_change_property_position_query('<=', $id);
				break;
			case "down":
				return $c_id = $this->_change_property_position_query('>=', $id);
				break;
		}
		return FALSE;
	}

	private function _change_property_position_query($type, $id)
	{
		$OB = '';
		if($type == '<=')
		{
			$OB = 'DESC';
		}
		$this->db
			->select("DISTINCT(A.`".self::ID_PR_PROP."`) AS ID, A.`sort` AS SORT ")
			->from("`".self::PR_PROP."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users)
			->where("A.`".self::ID_PR_TYPES."` <=> (SELECT `".self::ID_PR_TYPES."` FROM `".self::PR_PROP."` WHERE `".self::ID_PR_PROP."` = '".$id."' LIMIT 1) && A.`sort` ".$type." (SELECT `sort` FROM `".self::PR_PROP."` WHERE `".self::ID_PR_PROP."` = ".$id." LIMIT 1)")
			->order_by('sort', $OB)->limit(2);


		$query = $this->db->get();
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();

			$ID = $result[0]['ID'];
			$SORT = $result[0]['SORT'];

			$id = $result[1]['ID'];
			$sort = $result[1]['SORT'];

			$this->db->trans_start();
			$this->sql_add_data(array('sort' => $SORT))->sql_save(self::PR_PROP, $id);
			$this->sql_add_data(array('sort' => $sort))->sql_save(self::PR_PROP, $ID);
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
}
?>