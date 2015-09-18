<?php
class Musers extends AG_Model
{
	const US = 'users';
	const ID_US = 'id_users';
	
	public $user = FALSE;
	
	public function get_user()
	{
		if($this->user) return $this->user;
		$query = $this->db->select("*")
				->from("`".self::US."`")
				->where("`".self::ID_US."`", $this->id_users)->limit(1);
		$user = $query->get()->row_array();
		if(count($user)>0)
		{
			$this->user = $user;
			$this->session->set_data('USER', $user);
			return $user;
		}
		return FALSE;
	}
}
?>