<?php
class Msales_settings extends AG_Model
{
	const S_ALIAS 		= 'm_orders_settings_alias';
	const ID_S_ALIAS 	= 'id_m_orders_settings_alias';
	const S_VALUES 		= 'm_orders_settings_value';
	
	protected $default_settings = array(
		'mail_' => array(
			'new_order_email' => 'support@gbc.net.ua',
			'shop_name' => 'SHOP',
			'send_confirmed' => 1
		),
		'settings_' => array(
			'order_processing_type' => 'full'
		),
		'address_B_' => array(
			'country' => 1,
			'city' => 1,
			'zip' => 0,
			'address' => 0,
			'telephone' => 1
		),
		'address_S_' => array(
			'country' => 1,
			'city' => 1,
			'zip' => 0,
			'address' => 1,
			'telephone' => 1
		)
	);
	
	protected $settings = FALSE;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('users/musers');
		$user = $this->musers->get_user();
		$this->default_settings['mail_']['new_order_email'] = $user['email'];
	}
	
	public function get_sales_settings()
	{
		if($this->settings) return $this->settings;
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
		$this->settings = $settings_array;
		return $this->settings;
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