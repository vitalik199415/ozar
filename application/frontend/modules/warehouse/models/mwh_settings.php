<?php
class Mwh_settings extends AG_Model
{
	const S_ALIAS 			= 'wh_settings_alias';
	const ID_S_ALIAS 		= 'id_wh_settings_alias';
	const S_VALUES 			= 'wh_settings_value';
	const ID_S_VALUES 		= 'id_wh_settings_value';
	
	protected $wh_settings = FALSE;
	
	protected $default_settings = array(
		'wh_' => array('on' => 0, 'active' => 0)
	);
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_settings()
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
	
	public function get_wh_settings()
	{
		if($this->wh_settings) return $this->wh_settings;
		$s_key = 'wh_';
		$settings_array = $this->get_default_settings($s_key);
		$query = $this->db
				->select("A.`".self::ID_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
				->from("`".self::S_ALIAS."` AS A")
				->join("`".self::S_VALUES."` AS B",
						"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."`",
						"INNER")
				->where("A.`prefix`", $s_key)->where("B.`".self::ID_USERS."`", $this->id_users);
		
		foreach($query->get()->result_array() as $ms)
		{
			$settings_array[$ms['settings']] = $ms['value'];
		}
		$this->wh_settings = $settings_array;
		return $settings_array;
	}
}	