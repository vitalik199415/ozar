<?php
class Mpayment_methods extends AG_Model
{
	const P_M 				= 'm_payment_methods';
	const ID_P_M 			= 'id_m_payment_methods';
	const P_M_DESC 			= 'm_payment_methods_description';
	
	const U_P_M 			= 'm_users_payment_methods';
	const ID_U_P_M 			= 'id_m_users_payment_methods';
	const U_P_M_DESC 		= 'm_users_payment_methods_description';
	const ID_U_P_M_DESC 	= 'id_m_users_payment_methods_description';
	
	const U_P_M_S_ALIAS			= 'm_users_payment_methods_settings_alias';
	const ID_U_P_M_S_ALIAS		= 'id_m_users_payment_methods_settings_alias';
	const U_P_M_S_VALUE			= 'm_users_payment_methods_settings_value';
	const ID_U_P_M_S_VALUE		= 'id_m_users_payment_methods_settings_value';
	
	public $id_users_pm = FALSE;
	
	protected $default_settings_array = array(
		'1'	=> array(),
		'2' => array(),
		'3' => array(),
		'5' => array(),
		'6' => array(),
		'7' => array()
	);
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_users_payment_methods_collection()
	{
		$this->load->library('grid');
		$this->grid->_init_grid('users_payment_methods_grid');
		$this->grid->db
			->select("A.`".self::ID_U_P_M."` AS ID, A.`alias`, A.`active`, A.`default`, B.`name`")
			->from("`".self::U_P_M."` AS A")
			->join(	"`".self::U_P_M_DESC."` AS B",
					"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
			
		$this->load->helper('sales/payment_methods');
		helper_users_payment_methods_grid($this->grid);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0'=>'Нет', '1'=>'Да'));
		$this->grid->update_grid_data('default', array('0'=>'Нет', '1'=>'Да'));
		$this->grid->render_grid();	
	}
	
	public function add()
	{
		$data['payment_methods'] = $this->get_payment_methods_to_select();
		
		$this->load->helper('sales/payment_methods');
		helper_users_payment_methods_add($data);
	}
	
	public function add_method($method_id)
	{
		if($data['payment_method'] = $this->get_add_payment_method($method_id))
		{
			$data['settings']['payment_method_settings'] = $this->get_payment_method_settings_values($method_id);
			
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			
			$this->load->helper('sales/payment_methods');
			$this->template->add_navigation($data['payment_method']['payment_method_description'][$this->id_langs]['name']);
			eval('helper_payment_method_'.$data['payment_method']['payment_method']['alias'].'($data);');
			
			return TRUE;
		}
		return FALSE;
	}
	
	public function edit_method($id)
	{	
		if($this->isset_users_paymen_method($id))
		{
			$this->load->helper('sales/payment_methods');
			if($data['payment_method'] = $this->get_edit_payment_method($id))
			{
				$data['settings']['payment_method_settings'] = $this->get_payment_method_settings_values($data['payment_method']['payment_method'][self::ID_P_M], $id);
				
				$this->load->model('langs/mlangs');
				$data['on_langs'] = $this->mlangs->get_active_languages();
				
				$this->load->helper('sales/payment_methods');
				eval('helper_payment_method_'.$data['payment_method']['payment_method']['pm_alias'].'($data, $id);');
				
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function get_add_payment_method($id)
	{
		$id = intval($id);
		$query = $this->db
				->select("A.`".self::ID_P_M."` AS ID, A.`alias`, B.`name`, B.`description`, B.`".self::ID_LANGS."`")
				->from("`".self::P_M."` AS A")
				->join(	"`".self::P_M_DESC."` AS B",
						"B.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
						"LEFT")
				->where("A.`active`", 1)->where("(A.`id_users` = ".$this->id_users." OR A.`system` = 1)", NULL)->where("A.`".self::ID_P_M."`", $id);
		$result = $query->get()->result_array();
		if(count($result)>0)
		{
			$array = array();
			foreach($result as $ms)
			{
				$array['payment_method'] = array('ID' => $ms['ID'], self::ID_P_M => $ms['ID'] ,'alias' => $ms['alias']);
				$array['payment_method_description'][$ms[self::ID_LANGS]] = $ms;
			}
			return $array;
		}
		return FALSE;
	}
	
	public function get_edit_payment_method($id)
	{
		$id = intval($id);
		$query = $this->db
				->select("PM.`alias` AS pm_alias, PM.`".self::ID_P_M."`, A.`".self::ID_U_P_M."` AS ID, A.`".self::ID_P_M."`, A.`alias`, A.`active`, A.`default`, B.`name`, B.`description`, B.`".self::ID_LANGS."`")
				->from("`".self::U_P_M."` AS A")
				->join("`".self::P_M."` AS PM",
						"PM.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
						"INNER")
				->join("`".self::U_P_M_DESC."` AS B",
						"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."`",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_P_M."`", $id);
		$result = $query->get()->result_array();
		if(count($result)>0)
		{
			$array = array();
			foreach($result as $ms)
			{
				$array['payment_method'] = array('pm_alias' => $ms['pm_alias'], self::ID_P_M => $ms[self::ID_P_M], 'ID' => $ms['ID'] ,'method_id' => $ms[self::ID_P_M], 'alias' => $ms['alias'], 'active' => $ms['active'], 'default' => $ms['default']);
				$array['payment_method_description'][$ms[self::ID_LANGS]] = array('name' => $ms['name'], 'description' => $ms['description']);
			}
			return $array;
		}
		return FALSE;
	}
	
	public function get_payment_method_settings_values($method_id, $id = FALSE)
	{
		$s_array = $this->default_settings_array[$method_id];
		if($id)
		{
			$query = $this->db
					->select("A.`".self::ID_U_P_M_S_ALIAS."` AS ID, A.`".self::ID_P_M."` AS PM_ID, A.`prefix`, A.`alias`, B.`value`")
					->from("`".self::U_P_M_S_ALIAS."` AS A")
					->join("`".self::U_P_M_S_VALUE."` AS B",
							"B.`".self::ID_U_P_M."` = '".$id."' && B.`".self::ID_U_P_M_S_ALIAS."` = A.`".self::ID_U_P_M_S_ALIAS."`",
							"INNER")
					->where("`".self::ID_P_M."`", $method_id);
			$result = $query->get()->result_array();
			if(count($result)>0)
			{
				foreach($result as $ms)
				{
					$s_array[$ms['prefix'].$ms['alias']] = $ms['value'];
				}
			}
		}	
		return $s_array;
	}
	
	public function get_payment_methods_to_select()
	{
		$query = $this->db
				->select("A.`".self::ID_P_M."` AS ID, B.`name`, B.`description`")
				->from("`".self::P_M."` AS A")
				->join(	"`".self::P_M_DESC."` AS B",
						"B.`".self::ID_P_M."` = A.`".self::ID_P_M."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
						"left")
				->where("A.`active`", 1)->where("(A.`id_users` = ".$this->id_users." OR A.`system` = 1)", NULL)->order_by('sort');
		$result = $query->get()->result_array();
		$array = array();
		foreach($result as $ms)
		{
			$array[$ms['ID']] = $ms['name'];
		}
		return $array;
	}
	
	public function get_users_payment_methods_to_select()
	{
		$array = array(array(), FALSE);
		$this->db->select("A.`".self::ID_U_P_M."`, B.`name`, A.`default`")
			->from("`".self::U_P_M."` AS A")
			->join(	"`".self::U_P_M_DESC."` AS B",
					"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->order_by("A.`sort`");
		$result = $this->db->get()->result_array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$array[0][$ms[self::ID_U_P_M]] = $ms['name'];
				if($ms['default'])
				{
					$array[1] = $ms[self::ID_U_P_M];
				}
			}
		}
		return $array;
	}
	
	public function isset_users_paymen_method($id)
	{
		if(is_int($id))
		{
			$query = $this->db
					->select("COUNT(*) AS COUNT")
					->from(self::U_P_M)
					->where("`".self::ID_USERS."`", $this->id_users)->where(self::ID_U_P_M, $id);
			$result = $query->get()->row_array();
			if($result['COUNT'] == 1)
			{
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function check_isset_alias($alias)
	{
		$alias = trim($alias);
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::U_P_M."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`alias`", $alias)->limit(1);
		if($this->id_users_pm)
		{
			$query->where("`".self::ID_U_P_M."` <>", $this->id_users_pm);
		}
		$result = $query->get()->row_array();
		if($result['COUNT'] == 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function save($id = NULL)
	{
		if($id != NULL)
		{
			if($this->isset_users_paymen_method($id))
			{
				$this->db->trans_start();
				$data = $this->input->post('payment_method');
				$data_update = $data;
				unset($data_update['id_m_payment_methods']);
				
				if($data_update['default'] == 1)
				{
					$this->sql_add_data(array('default' => 0))->sql_save(self::U_P_M, array(self::ID_USERS => $this->id_users));
				}
				
				$this->sql_add_data($data_update)->sql_using_user()->sql_save(self::U_P_M, $id);
				
				$desc_array = array();
				$query = $this->db
						->select(self::ID_U_P_M_DESC.", ".self::ID_LANGS)
						->from(self::U_P_M_DESC)
						->where(self::ID_U_P_M, $id);
				$result = $query->get()->result_array();
				foreach($result as $ms)
				{
					$desc_array[$ms[self::ID_LANGS]] = $ms;
				}
				$this->load->model('langs/mlangs');
				$on_langs = $this->mlangs->get_active_languages();
				$data_langs = $this->input->post('payment_method_description');
				foreach($on_langs as $key => $ms)
				{
					if(isset($data_langs[$key]))
					{
						if(isset($desc_array[$key]))
						{
							$this->sql_add_data($data_langs[$key])->sql_save(self::U_P_M_DESC, $desc_array[$key][self::ID_U_P_M_DESC]);
						}
						else
						{
							$this->sql_add_data($data_langs[$key]+array(self::ID_U_P_M => $id, self::ID_LANGS => $key))->sql_save(self::U_P_M_DESC);
						}
					}
				}
				$method_id = $data['id_m_payment_methods'];
				$post_settings = $this->input->post('payment_method_settings');
				if($post_settings && count($post_settings)>0)
				{
					$query = $this->db
								->select("A.`".self::ID_U_P_M_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
								->from("`".self::U_P_M_S_ALIAS."` AS A")
								->join("`".self::U_P_M_S_VALUE."` AS B",
										"B.`".self::ID_U_P_M_S_ALIAS."` = A.`".self::ID_U_P_M_S_ALIAS."` && B.`".self::ID_U_P_M."` = '".$id."'",
										"LEFT")
								->where("A.`".self::ID_P_M."`", $method_id);
					$result = $query->get()->result_array();
					foreach($result as $ms)
					{
						if(isset($post_settings[$ms['settings']]))
						{
							$settings_alias[$ms['settings']] = array(self::ID_U_P_M_S_ALIAS => $ms['ID'], self::ID_U_P_M => $id, 'value' => $post_settings[$ms['settings']]);
							if($ms['value'] == NULL)
							{
								$this->sql_add_data($settings_alias[$ms['settings']])->sql_save(self::U_P_M_S_VALUE);
							}
							else
							{
								$this->sql_add_data($settings_alias[$ms['settings']])->sql_save(self::U_P_M_S_VALUE, array(self::ID_U_P_M => $id, self::ID_U_P_M_S_ALIAS => $settings_alias[$ms['settings']][self::ID_U_P_M_S_ALIAS]));
							}
						}
					}
				}
				
				$this->db->trans_complete();
				if($this->db->trans_status()) 
				{
					return $id;
				}
				return FALSE;
			}
			return FALSE;
		}
		else
		{
			if($data = $this->input->post('payment_method'))
			{
				$this->db->trans_start();
				if($data['default'] == 1)
				{
					$this->sql_add_data(array('default' => 0))->sql_save(self::U_P_M, array(self::ID_USERS => $this->id_users));
				}
				
				$id = $this->sql_add_data($data)->sql_using_user()->sql_save(self::U_P_M);
				$this->sql_add_data(array('sort' => $id))->sql_save(self::U_P_M, $id);
				
				$desc_array = array();
				$query = $this->db
						->select(self::ID_U_P_M_DESC, self::ID_LANGS)
						->from(self::U_P_M_DESC)
						->where(self::ID_U_P_M, $id);
				
				foreach($query->get()->result_array() as $ms)
				{
					$desc_array[$ms[self::ID_LANGS]] = $ms;
				}
				$this->load->model('langs/mlangs');
				$on_langs = $this->mlangs->get_active_languages();
				$data_langs = $this->input->post('payment_method_description');
				foreach($on_langs as $key => $ms)
				{
					if(isset($data_langs[$key]))
					{
						if(isset($desc_array[$key]))
						{
							$this->sql_add_data($data_langs[$key])->sql_save(self::U_P_M_DESC, $desc_array[$key][self::ID_U_P_M_DESC]);
						}
						else
						{
							$this->sql_add_data($data_langs[$key]+array(self::ID_U_P_M => $id, self::ID_LANGS => $key))->sql_save(self::U_P_M_DESC);
						}
					}
				}
				$method_id = $data['id_m_payment_methods'];
				$post_settings = $this->input->post('payment_method_settings');
				if($post_settings && count($post_settings)>0)
				{
					$query = $this->db
								->select("A.`".self::ID_U_P_M_S_ALIAS."` AS ID, A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS settings, B.`value`")
								->from("`".self::U_P_M_S_ALIAS."` AS A")
								->join("`".self::U_P_M_S_VALUE."` AS B",
										"B.`".self::ID_U_P_M_S_ALIAS."` = A.`".self::ID_U_P_M_S_ALIAS."` && B.`".self::ID_U_P_M."` = '".$id."'",
										"LEFT")
								->where("A.`".self::ID_P_M."`", $method_id);
					$result = $query->get()->result_array();
					foreach($result as $ms)
					{
						if(isset($post_settings[$ms['settings']]))
						{
							$settings_alias[$ms['settings']] = array(self::ID_U_P_M_S_ALIAS => $ms['ID'], self::ID_U_P_M => $id, 'value' => $post_settings[$ms['settings']]);
							if($ms['value'] == NULL)
							{
								$this->sql_add_data($settings_alias[$ms['settings']])->sql_save(self::U_P_M_S_VALUE);
							}
							else
							{
								$this->sql_add_data($settings_alias[$ms['settings']])->sql_save(self::U_P_M_S_VALUE, array(self::ID_U_P_M => $id, self::ID_U_P_M_S_ALIAS => $settings_alias[$ms['settings']][self::ID_U_P_M_S_ALIAS]));
							}
						}
					}
				}
				
				$this->db->trans_complete();
				if($this->db->trans_status()) 
				{
					return $id;
				}
				return FALSE;
			}
			return FALSE;
		}
	}
	
	public function delete_payment_method($id_users_pm)
	{
		if(is_int($id_users_pm))
		{
			$this->db->where(self::ID_USERS, $this->id_users)->where(self::ID_U_P_M, $id_users_pm);
			$this->db->delete(self::U_P_M);
			return TRUE;
		}
		return FALSE;
	}
	
	public function active_payment_method($id_users_pm, $active = 0)
	{
		if(is_array($id_users_pm))
		{
			if($active == 1)
			{
				$this->db->where_in(self::ID_U_P_M, $id_users_pm)->where(self::ID_USERS, $this->id_users);
				$this->db->update(self::U_P_M, array('active' => 1));
			}
			else
			{
				$this->db->where_in(self::ID_U_P_M, $id_users_pm)->where(self::ID_USERS, $this->id_users);
				$this->db->update(self::U_P_M, array('active' => 0));
			}
			return TRUE;
		}
		return FALSE;
	}
	
	public function get_users_payment_method_base_data($id_upm)
	{
		$query = $this->db
				->select("PM.`alias` AS pm_alias, PM.`".self::ID_P_M."` AS PM_ID, A.`".self::ID_U_P_M."` AS UPM_ID, A.`alias` AS upm_alias, A.`active`, A.`default`")
				->from("`".self::U_P_M."` AS A")
				->join("`".self::P_M."` AS PM",
						"PM.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
						"INNER")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_P_M."`", $id_upm)->limit(1);
		$result = $query->get()->row_array();
		if(count($result) > 0)
		{
			return $result;
		}
		return FALSE;
	}
	
	public function get_users_payment_method_data($id_upm, $id_lang = FALSE)
	{
		$query = $this->db
				->select("PM.`alias` AS pm_alias, PM.`".self::ID_P_M."` AS PM_ID, A.`".self::ID_U_P_M."` AS UPM_ID, A.`alias` AS upm_alias, A.`active`, A.`default`,
						B.`name`, B.`description`, B.`".self::ID_LANGS."`")
				->from("`".self::U_P_M."` AS A")
				->join("`".self::P_M."` AS PM",
						"PM.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
						"INNER")		
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_P_M."`", $id_upm)->limit(1);
		if($id_lang)
		{
			$query->join("`".self::U_P_M_DESC."` AS B",
						"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."` && B.`".self::ID_LANGS."` = '".$id_lang."'",
						"LEFT");
		}
		else
		{
			$query->join("`".self::U_P_M_DESC."` AS B",
						"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"LEFT");
		}
		$result = $query->get()->row_array();
		if(count($result) > 0)
		{
			return $result;
		}
		return FALSE;
	}
	
	public function get_payment_method_to_invoice_html($id_upm, $invoice_data)
	{
		$query = $this->db
				->select("PM.`alias` AS pm_alias, PM.`".self::ID_P_M."` AS PM_ID, A.`".self::ID_U_P_M."` AS UPM_ID, A.`alias` AS upm_alias, A.`active`, A.`default`")
				->from("`".self::U_P_M."` AS A")
				->join("`".self::P_M."` AS PM",
						"PM.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
						"INNER")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_P_M."`", $id_upm)->limit(1);
		$result = $query->get()->row_array();
		if(count($result) > 0)
		{
			$show_pm_link = 'http://'.$invoice_data['user']['domain'].'/ajax/sales/invoices/show_invoice_payment_method_data/id/'.$invoice_data['invoice']['invoices_number'].'/code/'.md5($invoice_data['invoice']['id_m_orders_invoices'].'-'.$invoice_data['invoice']['id_m_orders'].'-'.$invoice_data['invoice']['create_date']).'/lang-'.$invoice_data['letter_lang']['code'];
			$query = $this->db->select("A.`prefix`, A.`alias`, CONCAT(A.`prefix`, A.`alias`) AS `field`, B.`value`")
					->from("`".self::U_P_M_S_ALIAS."` AS A")
					->join("`".self::U_P_M_S_VALUE."` AS B",
							"B.`".self::ID_U_P_M_S_ALIAS."` = A.`".self::ID_U_P_M_S_ALIAS."` && B.`".self::ID_U_P_M."` = '".$id_upm."'",
							"LEFT");
			$pm_settings_temp = $query->get()->result_array();
			$pm_settings = array();
			foreach($pm_settings_temp as $ms)
			{
				$pm_settings[$ms['field']] = $ms;
			}
			return $this->load->view('sales/payment_methods/letters/'.$invoice_data['letter_lang']['language'].'/'.$result['pm_alias'], $invoice_data + array('pm_settings' => $pm_settings, 'show_pm_link' => $show_pm_link), TRUE);
		}
		return '';
	}
}	