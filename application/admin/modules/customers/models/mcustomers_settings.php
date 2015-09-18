<?php
class Mcustomers_settings extends AG_Model
{
	const S_ALIAS 			= 'm_u_customers_settings_alias';
	const ID_S_ALIAS 		= 'id_m_u_customers_settings_alias';
	const S_VALUES 			= 'm_u_customers_settings_values';
	const ID_S_VALUES 		= 'id_m_u_customers_settings_values';
	
	protected $default_settings = array(
		'address_B_' => array(
			'name' => 1,
			'country' => 1,
			'city' => 1,
			'zip' => 0,
			'address' => 0,
			'telephone' => 1,
			'address_email' => 1
		),
		'distribution_' => array(
			'email' => 'support@gbc.net.ua'
		),
		'registration_notice_' => array(
			'on' => 1,
			'email' => 'support@gbc.net.ua'
		)
	);
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('users/musers');
		$user = $this->musers->get_user();
		$this->default_settings['distribution_']['email'] = $user['email'];
		$this->default_settings['registration_notice_']['email'] = $user['email'];
	}
	
	public function edit()
	{
		$this->load->helper('customers/customers_settings_helper');
		$data['settings']['settings'] = $this->get_settings();
		helper_customers_settings_form_build($data);
	}
	
	public function save()
	{
		$settings_alias = $this->default_settings;; 
		$post = $this->input->post('settings');
		$query = $this->db
				->select("A.`".self::ID_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
				->from("`".self::S_ALIAS."` AS A")
				->join("`".self::S_VALUES."` AS B",
						"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."` && B.`".self::ID_USERS."` = ".$this->id_users,
						"LEFT");
		$this->db->trans_start();
		foreach($query->get()->result_array() as $ms)
		{
			if(isset($post[$ms['settings']]))
			{
				$settings_alias[$ms['settings']] = array(self::ID_S_ALIAS => $ms['ID'], 'value' => $post[$ms['settings']]);
				if($ms['value'] == NULL)
				{
					$this->sql_add_data($settings_alias[$ms['settings']])->sql_using_user()->sql_save(self::S_VALUES);
				}
				else
				{
					$this->sql_add_data($settings_alias[$ms['settings']])->sql_using_user()->sql_save(self::S_VALUES, array(self::ID_S_ALIAS => $settings_alias[$ms['settings']][self::ID_S_ALIAS]));
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
	
	public function get_settings($s_key = FALSE)
	{	
		$settings_array = $this->get_default_settings();
		$query = $this->db
				->select("A.`".self::ID_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
				->from("`".self::S_ALIAS."` AS A")
				->join("`".self::S_VALUES."` AS B",
						"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."`",
						"INNER")
				->where("B.`".self::ID_USERS."`", $this->id_users);
		foreach($query->get()->result_array() as $ms)
		{
			$settings_array[$ms['settings']] = $ms['value'];
		}
		return $settings_array;
	}
	
	public function get_default_settings($s_key = FALSE)
	{
		$default_settings = array();
		if($s_key)
		{
			$settings_array = $this->default_settings[$s_key];
			foreach($settings_array as $key => $ms)
			{
				$default_settings[$s_key.$key] = $ms;
			}
			return $default_settings;
		}
		else
		{
			$settings_array = $this->default_settings;
			foreach($settings_array as $key => $ms)
			{
				foreach($ms as $key1 => $ms1)
				{
					$default_settings[$key.$key1] = $ms1;
				}
			}
			return $default_settings;
		}
	}
	
	public function get_biling_settings()
	{
		$s_key = 'address_B_';
		$settings_array = $this->get_default_settings($s_key);
		$query = $this->db
				->select("A.`".self::ID_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
				->from("`".self::S_ALIAS."` AS A")
				->join("`".self::S_VALUES."` AS B",
						"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."`",
						"INNER")
				->where("A.`prefix`", "address_B_")->where("B.`".self::ID_USERS."`", $this->id_users);
		foreach($query->get()->result_array() as $ms)
		{
			$settings_array[$ms['settings']] = $ms['value'];
		}
		return $settings_array;
	}
	
	public function get_distribution_settings()
	{
		$s_key = 'distribution_';
		$settings_array = $this->get_default_settings($s_key); 
		$query = $this->db
				->select("A.`".self::ID_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
				->from("`".self::S_ALIAS."` AS A")
				->join("`".self::S_VALUES."` AS B",
						"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."`",
						"INNER")
				->where("A.`prefix`", "distribution_")->where("B.`".self::ID_USERS."`", $this->id_users);
		foreach($query->get()->result_array() as $ms)
		{
			$settings_array[$ms['settings']] = $ms['value'];
		}
		return $settings_array;
	}
	
}
?>