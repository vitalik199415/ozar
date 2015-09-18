<?php
class Msite_settings extends AG_Model
{
	const USERS = 'users';
	
	const S_DESC 		= 'users_site_description';
	const ID_S_DESC 	= 'id_users_site_description';
		
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_site_description()
	{
		$result = array();
		$query = $this->db
				->select("*")
				->from("`".self::S_DESC."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_LANGS."`", $this->mlangs->id_langs);
		$result = $query->get()->row_array();
		if(count($result) > 0)
		{
			$this->variables->set_vars('SEO_first_title', $result['company_title']);
			$this->variables->set_vars('SEO_first_description', $result['company_description']);
			$this->variables->set_vars('SEO_first_TD_separator', $result['TD_separator']);
		}
		return array('site_description' => $result);
	}
	
	public function get_admin_settings()
	{
		$query = $this->db->select("`email`, `name`")
				->from("`".self::USERS."`")
				->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $query->get()->row_array();
		return $result;
	}
	
	public function get_domain_settings()
	{
		$query = $this->db->select("`domain`")
				->from("`".self::USERS."`")
				->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $query->get()->row_array();
		return $result;
	}
}	