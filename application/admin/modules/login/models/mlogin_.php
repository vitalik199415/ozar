<?php
class Mlogin extends AG_Model
{
	const USERS = 'users';
	
	function __construct()
		{
			parent::__construct();
		}
		
	public function isAutorize()
		{
			if($this->session->get_data('id_users') && $this->session->get_data('id_users')>0)
			{
				return true;
			}
			return false;		
		}
	public function autorize($data)
		{
			$query = $this->db->get_where(self::USERS,$data);
			$result = $query->result_array();
			if(count($result)==1)
				{
					$this->session->set_data('id_users',$result[0]['id_users']);
					$this->session->set_data('rang',$result[0]['rang']);
					
					return true;
				}
			return false;	
		}		
}
?>