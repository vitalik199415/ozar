<?php
class AG_Model extends CI_Model
{
	const CREATE_DATE_FIELD = 'create_date';
	const UPDATE_DATE_FIELD = 'update_date';
	const USER_FIELD 		= 'id_users';
	
	private $data = FALSE;
	private $update_date = FALSE;
	private $using_user = FALSE;
	
	public $id_users	= 0;
	public $id_admin	= 0;
	const USERS 		= 'users';
	const ID_USERS 		= 'id_users';
	
	public $id_langs	= 0;
	
	const LANGS 		= 'langs';
	const ID_LANGS 		= 'id_langs';
	
	const USERS_LANGS 	= 'users_langs';
	
	function __construct()
	{
		parent::__construct();
		$this->id_users = $this->session->get_data('id_users');
		$this->id_admin = $this->session->get_data('id_admin');
		$this->id_langs = $this->_get_current_lang();
	}
	
	private function _get_current_lang()
	{
		if($this->id_users == 0)
		{
			return FALSE;
		}
		if($lang = $this->session->get_data('id_langs'))
		{
			return $lang;
		}
		$result = $this->db->select("A.`".self :: ID_LANGS."` AS ID")
				->from("`".self :: LANGS."` AS A")
				->join(	"`".self :: USERS_LANGS."` AS B",
						"B.`".self :: ID_LANGS."` = A.`".self :: ID_LANGS."` && B.`id_users` = '".$this->id_users."' && B.`on` = '1' && B.`default` = '1'",
						"INNER")
				->where("A.`active`", 1)->limit(1);
		$result = $result->get()->row_array();
		if(count($result)>0)
		{
			$this->session->set_data('id_langs', $result['ID']);
			return $result['ID'];
		}
		else
		{
			$result = $this->db->select("A.`".self :: ID_LANGS."` AS ID")
					->from("`".self :: LANGS."` AS A")
					->where("A.`active`", 1)->where("A.`default`", 1)->limit(1);
			$result = $result->get()->row_array();
			$insert_array = array(self::ID_USERS => $this->id_users, self::ID_LANGS => $result['ID'], 'default' => 1, 'on' => 1, 'active' => 1);
			$id = $this->sql_add_data($insert_array)->sql_save(self::USERS_LANGS);
			$this->sql_add_data(array('sort' => $id))->sql_save(self::USERS_LANGS, $id);
			$this->session->set_data('id_langs', $result['ID']);
			return $result['ID'];
		}
	}
	
	public function sql_add_data($data)
	{
		if(is_array($data))
		{
			if($this->data)
			{
				$this->data = $this->data + $data;
			}
			else
			{
				$this->data = $data;
			}
		}
		return $this;
	}
	public function _addData($data)
	{
		return $this->sql_add_data($data);
	}
	
	public function _updateDate()
	{
		$this->update_date = TRUE;
		return $this;
	}
	public function sql_update_date()
	{
		$this->update_date = TRUE;
		return $this;
	}
	
	private function _add_updateDate()
	{
		if($this->update_date)
		{
			$date = date("Y-m-d H:i:s", mktime());
			$this->_addData(array(self :: UPDATE_DATE_FIELD => $date));
		}	
	}
	private function _add_createDate()
	{
		if($this->update_date)
		{
			$date = date("Y-m-d H:i:s", mktime());
			$this->_addData(array(self :: CREATE_DATE_FIELD => $date));
		}	
	}
	public function _usingUser()
	{
		$this->using_user = TRUE;
		return $this;
	}
	public function sql_using_user()
	{
		$this->using_user = TRUE;
		return $this;
	}
	private function _insertUser()
	{
		if($this->using_user)
		{
			$this->_addData(array(self :: USER_FIELD => $this->id_users));
		}
	}
	private function _whereUser()
	{
		if($this->using_user)
		{
			$this->db->where(self :: USER_FIELD, $this->id_users);
		}
	}
	public function _save($table, $id = FALSE)
	{
		return $this->sql_save($table, $id);
	}
	public function sql_save($table, $id = FALSE)
	{
		if(!$this->db->table_exists($table)) return FALSE;
		$type = '';
		if($id == FALSE)
		{
			$type = 'insert';
			$field = FALSE;
			$value = FALSE;
			
			$this->_add_createDate();
			$this->_add_updateDate();
			
		}
		else
		{
			$type = 'update';
			if(is_array($id))
			{
				foreach($id as $key => $ms)
				{
					$field[] = $key;
					$value[] = $ms;
				}
			}
			else
			{
				$field[] = 'id_'.$table;
				$value[] = $id;
			}
			$this->_add_updateDate();
			foreach($field as $F)
			{
				if(!$this->db->field_exists($F, $table))	return FALSE;
			}	
		}
		return $this->_toDB($type, $table, $field, $value);
	}
	private function _toDB($type, $table, $field = FALSE, $value = FALSE)
	{
		if($this->data)
		{
			if($type == 'insert')
			{
				$this->_insertUser();
				if($this->db->insert($table, $this->data))
				{
					$this->data = FALSE;
					$this->update_date = FALSE;
					$this->using_user = FALSE;
					return $this->db->insert_id();
				}
				$this->data = FALSE;
				$this->update_date = FALSE;
				$this->using_user = FALSE;
				return FALSE;				
			}
			if($type = 'update')
			{
				$this->_whereUser();
				foreach($field as $key => $ms)
				{
					$this->db->where($field[$key], $value[$key]);
				}
				if($this->db->update($table, $this->data))
				{
					$this->data = FALSE;
					$this->update_date = FALSE;
					$this->using_user = FALSE;
					return TRUE;
				}
				$this->data = FALSE;
				$this->update_date = FALSE;
				$this->using_user = FALSE;
				return false;
			}
		}
		return false;		
	}
	
	public function is_0_or_1($value)
	{
		if($value == 1 || $value == 0) return TRUE;
		return FALSE;
	}
}
?>