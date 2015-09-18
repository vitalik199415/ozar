<?php
class Mlangs extends AG_Model
{
	const LANGS = 'langs';
	const ID_LANGS = 'id_langs';
	
	const USERS_LANGS = 'users_langs';
	const ID_USERS_LANGS = 'id_users_langs';
	
	private $active_langs = FALSE;
	
	public $id_langs = 1;
	function __construct()
	{
		parent::__construct();
	}
	
	public function render_langs()
	{
		$this->load->library('grid');
		$this->grid->_init_grid("langs_grid");

		$this->grid->db->select("B.code, B.language, B.name, B.short_name, A.default, A.active, A.on, A.sort, A.`".self::ID_USERS_LANGS."` as ID, A.`".self::ID_USERS_LANGS."` AS ID_UL")
				->from("`".self::USERS_LANGS."` AS A")
				->join("`".self::LANGS."` AS B",
						"B.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`",
						"INNER")
				->where("A.`".self::ID_USERS."`", $this->id_users);
				
		$this->load->helper('langs/langs_helper');
		
		helper_langs_grid_build($this->grid);
		$this->grid->add_extra_sort('sort');
		$this->grid->create_grid_data();
		$this->grid->update_grid_data_using_string("sort", "<a class='arrow_down' href='".set_url('*/change_position/')."id/$1/type/down' title='Смена позиции: Опустить'></a><a class='arrow_up' href='".set_url('*/change_position/')."id/$1/type/up' title='Смена позиции: Поднять'></a>", array('$1' => 'ID'));
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('on', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('default', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->render_grid();
	}
	
	public function add()
	{
		$this->load->helper('langs/langs_helper');
		
		$query = $this->db->select("A.`".self::ID_LANGS."` AS ID,  A.code, A.language, A.name, A.short_name, A.default ")
							->from("`".self::LANGS."` AS A")
							->where("A.`".self::ID_LANGS."` NOT IN (SELECT `".self::ID_LANGS."` FROM `".self::USERS_LANGS."` WHERE `".self::ID_USERS."` = '".$this->id_users."')");	
		$result = $query->get()->result_array();
		$data = array();
		if(count($result) > 0)
		{
			foreach($result as $ms)
			{
				$data['select_langs'][$ms['ID']] = $ms['name'];
			}
			$this->load->helper('langs/langs_helper');
			helper_langs_form_build($data);
			return TRUE;
		}
		return FALSE;
	}
	
	public function get_users_langs()
	{
		$query = $this->db->select("B.code, B.language, B.name, B.short_name, B.default, A.active, A.on, A.`".self::ID_LANGS."` as ID")
				->from("`".self::USERS_LANGS."` AS A")
				->join("`".self::LANGS."` AS B",
						"B.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`",
						"INNER")
				->where("A.`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->result_array();
		$users_langs = array();
		foreach($result as $ms)
		{
			$users_langs[$ms['ID']] = $ms;
		}
		return $users_langs;
	}
	
	public function edit($id)
	{	$this->load->helper('langs/langs_helper');
	
		if($id > 0)
		{
			$query = $this->db->select("A.`".self::ID_USERS_LANGS."` as ID, A.active, A.on, A.default, B.name")
						->from("`".self::USERS_LANGS."` AS A")
						->join("`".self::LANGS."` AS B",
								"B.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`",
								"INNER")
						->where("A.`".self::ID_USERS_LANGS."` ", $id)
						->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		
			$result = $query->get()->row_array();
			$data = array();
			if(count($result) > 0)
			{			
				$data['select_langs']['ID'] = $result['name'];
				$data['select_langs']['active'] = $result['active'];
				$data['select_langs']['on'] = $result['on'];
				$data['select_langs']['default'] = $result['default'];
								
				$this->load->helper('langs/langs_helper');
				helper_langs_edit_form_build($data, '/id/'.$id);
				return TRUE;
			}
			return FALSE;
		}
	}
	
	public function save($id = false)
	{
		if($id > 0)
		{
			if($this->input->post('users_langs'))
			{  
				$POST = $this->input->post('users_langs');
				$this->db->trans_start();
				if($POST['default'] == 1)
				{  
					if($POST['on'] == 1)
					{
						$this->db->where(self::ID_USERS, $this->id_users);
						$this->db->update(self::USERS_LANGS, array('default' => '0'));
					}
					else
					{
						$POST['default'] = 0;
					}
				}
				$ID = $this->sql_add_data($POST)->sql_using_user()->sql_save(self :: USERS_LANGS, $id);
				$this->db->trans_complete();
				if($this->db->trans_status())
				{
					return $ID; 
				}
				return FALSE;
			}
		}
		else
		{
			if($this->input->post('users_langs'))
			{
				$POST = $this->input->post('users_langs');
				if(!isset($POST['id_langs'])) return FALSE;
				$users_langs = $this->get_users_langs();
				if(isset($users_langs[$POST['id_langs']])) return FALSE;
				
				$this->db->trans_start();
				
				if($POST['default'] == 1)
				{
					if($POST['on'] == 1)
					{
						$this->db->where(self::ID_USERS, $this->id_users);
						$this->db->update(self::USERS_LANGS, array('default' => '0'));
					}
					else
					{
						$POST['default'] = 0;
					}
				}
				
				$ID = $this->sql_add_data($POST)->sql_using_user()->sql_save(self :: USERS_LANGS);
				
				if($ID && $ID > 0)
				{	
					$this->sql_add_data(array('sort' => $ID))->sql_using_user()->sql_save(self :: USERS_LANGS, $ID);
					$this->db->trans_complete();
					if($this->db->trans_status()) 
					{
						return $ID; 
					}
					return FALSE;
				}
				return FALSE;
			}
			return FALSE;
		}
	}
	
	public function delete($id)
	{
		if($id > 0)
		{ 
			$query = $this->db->select("A.`".self::ID_LANGS."` , A.`default`")
						->from("`".self::USERS_LANGS."` AS A")
						->where("A.`".self::ID_USERS_LANGS."` ", $id)
						->where("A.`".self::ID_USERS."`", $this->id_users);
			$result = $query->get()->row_array();
						
			if($result['default'] == 1)
			{	
				$this->db->trans_start();				
				$this->db->where(self::ID_USERS_LANGS, $id)->where(self::ID_USERS, $this->id_users);
				$this->db->delete(self::USERS_LANGS);
				
				$this->db->where(self::ID_USERS, $this->id_users)->order_by(self::ID_LANGS)->limit(1);
				$this->db->update(self::USERS_LANGS, array('default' => '1')); 
				$this->db->trans_complete();
				if($this->db->trans_status()) 
				{ 	
					return TRUE; 
				}
				return FALSE;
			}
			else
			{
				$this->db->trans_start();
				$this->db->where(self::ID_USERS_LANGS, $id)->where(self::ID_USERS, $this->id_users);
				$this->db->delete(self::USERS_LANGS);
				$this->db->trans_complete();
				if($this->db->trans_status()) 
				{
					return TRUE; 
				}
				return FALSE;
			}
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
				$this->sql_add_data($data)->sql_using_user()->sql_save(self::USERS_LANGS, $ms);
			}
			return TRUE;			
		}
		return FALSE;
	}
	
	public function on_site($id, $on = 1)
	{
		if(is_array($id))
		{
			$data = array('on' => $on);
			foreach($id as $ms)
			{
				$this->sql_add_data($data)->sql_using_user()->sql_save(self ::USERS_LANGS, $ms);
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
							
		$query = $this->db
			->select("`".self::ID_USERS_LANGS."` AS ID, `sort` AS SORT")
			->from("`".self::USERS_LANGS."`")
			->where("`sort` ".$type." (SELECT `sort` FROM `".self::USERS_LANGS."` WHERE `".self::ID_USERS_LANGS."` = '".$id."' LIMIT 1)")
			->where("`".self::ID_USERS."`", $this->id_users)
			->order_by('sort', $OB)->limit(2);

		$query = $query->get();
		if($query->num_rows() == 2)
		{
			$result = $query->result_array();
			$ID = $result[0]['ID'];
			$SORT = $result[0]['SORT'];
							
			$id = $result[1]['ID'];
			$sort = $result[1]['SORT'];
			
			$this->db->trans_start();
			$this->sql_add_data(array('sort' => $SORT))->sql_save(self::USERS_LANGS, $id);
			$this->sql_add_data(array('sort' => $sort))->sql_save(self::USERS_LANGS, $ID);
			$this->db->trans_complete();
			if($this->db->trans_status()) 
			{
				return TRUE; 
			}
			return FALSE;
		}
		return FALSE;
	}
	
		
	public function getLanguages()
	{
	
	}
	public function getUsersLanguages($short = false)
	{
		$this->get_active_languages($short);
	}

	public function get_users_languages_to_select()
	{
		$array = array(array(), FALSE);
		$this->db->select("A.`".self::ID_LANGS."`, A.`name`, B.`default`")
		   ->from("`".self :: LANGS."` AS A")
		   ->join(	"`".self :: USERS_LANGS."` AS B",
					"B.`".self :: ID_LANGS."` = A.`".self :: ID_LANGS."` && B.`id_users` = '".$this->id_users."' && B.`active` = '1'",
					"INNER")
		   ->where("A.`active`", 1)->order_by("B.`sort`");
		$result = $this->db->get()->result_array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$array[0][$ms[self::ID_LANGS]] = $ms['name'];
				if($ms['default'])
				{
					$array[1] = $ms[self::ID_LANGS];
				}
			}
		}
		return $array;
	}

	public function get_active_languages($full_array = FALSE)
	{
		if($full_array)
		{
			if(isset($this->active_langs['full'])) return $this->active_langs['full'];
		}
		else
		{
			if(isset($this->active_langs['short'])) return $this->active_langs['short'];
		}
		$result = $this->db->select("A.`".self :: ID_LANGS."` AS ID, A.`code`, A.`name`")
				->from("`".self :: LANGS."` AS A")
				->join(	"`".self :: USERS_LANGS."` AS B",
						"B.`".self :: ID_LANGS."` = A.`".self :: ID_LANGS."` && B.`id_users` = '".$this->id_users."' && B.`on` = '1'",
						"INNER")
				->where("A.`active`", 1)
				->order_by("B.`sort`");
		$result = $result->get()->result_array();
		if(!$full_array)
		{
			$data = array();
			foreach($result as $ms)
			{
				$data[$ms['ID']] = $ms['name'];
			}
			$this->active_langs['short'] = $data;
			return $data;
		}
		$this->active_langs['full'] = $result;
		return $result;
	}
	
	public function get_language($id_lang)
	{
		$query = $this->db->select("*")
				->from("`".self::LANGS."`")
				->where("`".self::ID_LANGS."`", $id_lang)->limit(1);
		$lang = $query->get()->row_array();
		if(count($lang)>0)
		{
			return $lang;
		}
		return FALSE;
	}
}
?>