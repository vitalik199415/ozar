<?php
class Mphoto_gallery_settings extends AG_Model
{
	const MID				= '3';
	const U_MOD				= 'users_modules';						
	const ID_U_MOD			= 'id_users_modules';					
	
	const S_ALIAS = 'users_modules_settings_alias';
	const ID_S_ALIAS = 'id_users_modules_settings_alias';
	const S_VALUE = 'users_modules_settings_value';
	
	const MODULE = 'm_photo_gallery';
	
	protected $DEFAULT_SETTINGS = array(
		'img_width' 		=> 800,
		'img_height' 		=> 800,
		'img_width_thumbs' 	=> 200,
		'img_height_thumbs' => 150,
		'img_quality' 		=> 85,
		'img_wm' 			=> 0,
		'img_wm_text' 		=> 'gbc.net.ua',
		'img_wm_text_size' 	=> 50,
		'img_wm_text_color' => '#FFFFFF',
		'img_wm_text_shadow_color' 		=> '#000000',
		'img_wm_text_shadow_padding' 	=> 3,
		'img_wm_valign' 	=> 'M',
		'img_wm_align' 		=> 'C',
		'img_wm_opacity' 	=> 35
	);
	
	protected $segment = FALSE;
	function __construct()
	{
		parent::__construct();
		$this->segment = (int) $this->uri->segment(self::MID);
	}
	
	public function edit()
	{
		$this->load->helper('photo_gallery/photo_settings_helper');
		$data['img_settings']['img_settings'] = $this->get_photo_gallery_settings();
		helper_photo_settings_form_build($data);
	}
	
	public function get_photo_gallery_settings($return_key_value = FALSE, $prefix_key = FALSE)
	{
		$query = $this->db
				->select("A.`".self::ID_S_ALIAS."` AS ID, CONCAT(A.`prefix`, A.`alias`) AS name, A.`alias`, A.`prefix`, B.`value`")
				->from("`".self::S_ALIAS."` AS A")
				->join("`".self::S_VALUE."` AS B",
						"B.`".self::ID_S_ALIAS."` = A.`".self::ID_S_ALIAS."` && B.`".self::ID_U_MOD."` = ".$this->segment,
						"left")
				->where("A.`module`", self::MODULE)->where("A.`input_output`", 0)->order_by("A.`sort`");
		
		$result = $query->get()->result_array();
		$settings = array();
		if($prefix_key)
		{
			foreach($result as $key => $ms)
			{
				$settings[$ms['prefix']][$ms['alias']] = $ms;
				if($ms['value'] == NULL)
				{
					$this->sql_add_data(array(self::ID_S_ALIAS => $ms['ID'], self::ID_U_MOD => $this->segment, 'value' => $this->DEFAULT_SETTINGS[$ms['name']]))->sql_using_user()->sql_save(self::S_VALUE);
					$settings[$ms['prefix']][$ms['alias']]['value'] = $this->DEFAULT_SETTINGS[$ms['name']];
				}
			}
		}
		else
		{
			foreach($result as $key => $ms)
			{
				$settings[$ms['name']] = $ms;
				if($ms['value'] == NULL)
				{
					$this->sql_add_data(array(self::ID_S_ALIAS => $ms['ID'], self::ID_U_MOD => $this->segment, 'value' => $this->DEFAULT_SETTINGS[$ms['name']]))->sql_using_user()->sql_save(self::S_VALUE);
					$settings[$ms['name']]['value'] = $this->DEFAULT_SETTINGS[$ms['name']];
				}
			}
		}
		if($return_key_value)
		{
			foreach($settings as $key => $ms)
			{
				$settings[$key] = $ms['value'];
			}
		}
		return $settings;
	}
	
	public function save()
	{
		$data['img_settings'] = $this->input->post('img_settings');
			
		$settings = $this->get_photo_gallery_settings();
		$this->db->trans_start();
		foreach($data['img_settings'] as $key => $ms)
		{
			if(isset($settings[$key]) && $settings[$key] != $ms)
			{
				$this->sql_add_data(array('value' => $ms))->sql_save(self::S_VALUE, array(self::ID_S_ALIAS => $settings[$key]['ID'], 'id_users_modules' => $this->segment, self::ID_USERS => $this->id_users));
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
?>