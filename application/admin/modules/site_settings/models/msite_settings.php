<?php
class Msite_settings extends AG_Model
{
	const USERS		= 'users';
	const S_DESC 	= 'users_site_description';
	const ID_S_DESC = 'id_users_site_description';
		
	function __construct()
	{
		parent::__construct();
	}
	
	public function edit()
	{
		$data = array();
		$data['site_description'] = array();
		$this->load->model('langs/mlangs');
		$data['on_langs'] = $this->mlangs->get_active_languages();
		
		$query = $this->db
				->select("`company_name`, `work_name`, `work_description`, `company_title`, `company_description`, `TD_separator`, `id_langs`")
				->from("`".self::S_DESC."`")
				->where("`".self::ID_USERS."`", $this->id_users);
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$data['site_description']['site_description'][$ms[self::ID_LANGS]] = $ms;
		}
		
		$query = $this->db->select("`email`, `name`")
				->from("`".self::USERS."`")
				->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$result = $query->get()->row_array();
		$data['site_admin']['site_admin'] = $result;
		
		$this->load->helper('site_settings/site_settings_helper');
		helper_site_settings_form_build($data);
	}
	
	public function save()
	{
		if($POST = $this->input->post('site_admin'))
		{
			$POST = $this->input->post('site_admin');
			$this->sql_add_data(array('email' => $POST['email'], 'name' => $POST['name']))->sql_save(self::USERS, $this->id_users);
			
			$POST = $this->input->post('site_description');
			$this->load->model('langs/mlangs');
			$langs = $this->mlangs->get_active_languages();
			$query = $this->db
					->select("`".self::ID_S_DESC."`, `".self::ID_LANGS."`")
					->from("`".self::S_DESC."`")
					->where("`".self::ID_USERS."`", $this->id_users);
			$result = $query->get()->result_array();
			if(count($result)>0)
			{
				$temp_array = array();
				foreach($result as $ms)
				{
					$temp_array[$ms[self::ID_LANGS]] = $ms;
				}
				$this->db->trans_start();
				foreach($langs as $key => $ms)
				{
					if(isset($POST[$key]))
					{
						$data = $POST[$key];
						if(isset($temp_array[$key]))
						{
							$this->sql_add_data($data)->sql_using_user()->sql_save(self::S_DESC, $temp_array[$key][self::ID_S_DESC]);
						}
						else
						{
							$this->sql_add_data($data + array(self::ID_LANGS => $key))->sql_using_user()->sql_save(self::S_DESC);
						}
					}
				}
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
				foreach($langs as $key => $ms)
				{
					if(isset($POST[$key]))
					{
						$data = $POST[$key];
						$this->sql_add_data($data + array(self::ID_LANGS => $key))->sql_using_user()->sql_save(self::S_DESC);
					}
				}
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
?>