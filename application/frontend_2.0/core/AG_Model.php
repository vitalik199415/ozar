<?php
class AG_Model extends CI_Model
{
	const CREATE_DATE_FIELD = 'create_date';
	const UPDATE_DATE_FIELD = 'update_date';
	const USER_FIELD 		= 'id_users';
	const ID_USERS = 'id_users';
	const ID_LANGS = 'id_langs';
	
	private $data = FALSE;
	private $update_date = FALSE;
	private $using_user = FALSE;
	
	public $id_users	= 0;
	
	function __construct()
	{
		$this->id_users = ID_USERS;
		//$this->load->model('langs/mlangs');
			
		parent::__construct();
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
			$this->sql_add_data(array(self :: UPDATE_DATE_FIELD => $date));
		}	
	}
	private function _add_createDate()
	{
		if($this->update_date)
		{
			$date = date("Y-m-d H:i:s", mktime());
			$this->sql_add_data(array(self :: CREATE_DATE_FIELD => $date));
		}	
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
			$this->sql_add_data(array(self :: USER_FIELD => $this->id_users));
		}
	}
	
	private function _whereUser()
	{
		if($this->using_user)
		{
			$this->db->where(self :: USER_FIELD, $this->id_users);
		}
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
}
?>