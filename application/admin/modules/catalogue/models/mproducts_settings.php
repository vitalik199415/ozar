<?php
class Mproducts_settings extends AG_Model
{
	const S_ALIAS 			= 'm_c_products_settings_alias';
	const ID_S_ALIAS 		= 'id_m_c_products_settings_alias';
	const S_VALUES 			= 'm_c_products_settings_values';
	const ID_S_VALUES 		= 'id_m_c_products_settings_values';
	
	protected $default_settings = array(
		'products_' => array(
			'sort_type' => 1,
			'count_to_page' => 12
		),
		'img_' => array(
			'width' => 800,
			'height' => 800,
			'width_thumbs' => 200,
			'height_thumbs' => 200,
			'quality' => 90,
			'wm' => 0,
			'wm_text' => 'gbc.net.ua',
			'wm_text_size' => 50,
			'wm_text_color' => '#FFFFFF',
			'wm_text_shadow_color' => '#000000',
			'wm_text_shadow_padding' => 4,
			'wm_valign' => 'M',
			'wm_align' => 'C',
			'wm_opacity' => 30
		),
		'reviews_' => array(
			'on' => '0',
			'count_to_page' => '10',
			'admin_notice' => '1',
			'admin_email' => '',
			'admin_name' => '',
			'publication_immediately' => 1
		),
		'related_' => array(
			'on' => 0,
			'count' => 9,
			'show_count' => 3,
			'random' => 0
		),
		'similar_' => array(
			'on' => 0,
			'count' => 9,
			'show_count' => 3,
			'random' => 0
		)
	);
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('site_settings/msite_settings');
		$admin_data = $this->msite_settings->get_admin_settings();
		$domain_data = $this->msite_settings->get_domain_settings();
		
		$this->default_settings['img_']['wm_text'] = $domain_data['domain'];
		
		$this->default_settings['reviews_']['admin_email'] = $admin_data['email'];
		$this->default_settings['reviews_']['admin_name'] = $admin_data['name'];
	}
	
	public function edit()
	{
		$this->load->helper('catalogue/products_settings_helper');
		$data['settings']['settings'] = $this->get_settings();
		//$data['img']['settings'] = $this->get_imgage_settings();
		helper_products_settings_form_build($data);
	}
	
	public function save()
	{
		$settings_alias = $this->default_settings;
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
	
	public function get_image_settings()
	{
		$s_key = 'img_';
		$settings_array = $this->get_default_settings($s_key);
		$query = $this->db
				->select("A.`".self::ID_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
				->from("`".self::S_ALIAS."` AS A")
				->join("`".self::S_VALUES."` AS B",
						"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."`",
						"INNER")
				->where("A.`prefix`", "img_")->where("B.`".self::ID_USERS."`", $this->id_users);
		foreach($query->get()->result_array() as $ms)
		{
			$settings_array[$ms['settings']] = $ms['value'];
		}
		return $settings_array;
	}
	
	public function get_products_settings()
	{
		$s_key = 'products_';
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
		return $settings_array;
	}
}	