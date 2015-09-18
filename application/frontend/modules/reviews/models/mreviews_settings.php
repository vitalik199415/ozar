<?php
class Mreviews_settings extends AG_Model
{
	const MID			= '3';
	const U_MOD			= 'users_modules';
	const ID_U_MOD		= 'id_users_modules';
	
	const S_ALIAS 		= 'users_modules_settings_alias';
	const ID_S_ALIAS 	= 'id_users_modules_settings_alias';
	const S_VALUE 		= 'users_modules_settings_value';
	
	const MODULE = 'reviews';
	
	private $default_settings = array(
		'reviews_' => array(
			'count_to_page' => 10,
			'admin_email' 	=> '',
			'admin_name' 	=> '',
			'publication_immediately' 	=> 1,
			'admin_notice'	=> 1
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->load->model('site_settings/msite_settings');
		$admin_data = $this->msite_settings->get_admin_settings();

		$this->default_settings['reviews_']['admin_email'] = $admin_data['email'];
		$this->default_settings['reviews_']['admin_name'] = $admin_data['name'];
	}

	public function get_settings($module_id)
	{
		$settings_array = $this->get_default_settings();
		$this->db
			->select("A.`".self::ID_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
			->from("`".self::S_ALIAS."` AS A")
			->join("`".self::S_VALUE."` AS B",
				"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."` && B.`".self::ID_U_MOD."` = ".$module_id,
				"INNER")
			->where("A.`module`", self::MODULE)->order_by("A.`sort`");
		foreach($this->db->get()->result_array() as $ms)
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
}
?>