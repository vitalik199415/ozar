<?php
class Mshipping_methods extends AG_Model
{
	const S_M 				= 'm_shipping_methods';
	const ID_S_M 			= 'id_m_shipping_methods';
	const S_M_DESC 			= 'm_shipping_methods_description';
	const S_M_S_FIELDS		= 'm_shipping_methods_fields';
	const ID_S_M_S_FIELDS	= 'id_m_shipping_methods_fields';
	
	const U_S_M 			= 'm_users_shipping_methods';
	const ID_U_S_M 			= 'id_m_users_shipping_methods';
	const U_S_M_DESC 		= 'm_users_shipping_methods_description';
	const ID_U_S_M_DESC 	= 'id_m_users_shipping_methods_description';
	
	public $id_users_sm = FALSE;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_users_shipping_methods_collection()
	{
		$this->load->library('grid');
		$this->grid->_init_grid('users_shipping_methods_grid');
		$this->grid->db
			->select("A.`".self::ID_U_S_M ."` AS ID, A.`alias`, A.`active`, A.`default`, B.`name`")
			->from("`".self::U_S_M."` AS A")
			->join(	"`".self::U_S_M_DESC."` AS B",
					"B.`".self::ID_U_S_M."` = A.`".self::ID_U_S_M."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
			
		$this->load->helper('sales/shipping_methods');
		helper_users_shipping_methods_grid($this->grid);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0'=>'Нет', '1'=>'Да'));
		$this->grid->update_grid_data('default', array('0'=>'Нет', '1'=>'Да'));
		$this->grid->render_grid();	
	}
	
	public function add()
	{
		$data['shipping_methods'] = $this->get_shipping_methods_to_select();
		
		$this->load->helper('sales/shipping_methods');
		helper_users_shipping_methods_add($data);
	}
	
	public function add_method($method_id)
	{
		if($data['shipping_method'] = $this->get_add_shipping_method($method_id))
		{
			//$data['settings']['payment_method_settings'] = $this->get_payment_method_settings_values($method_id);
			
			$this->load->model('langs/mlangs');
			$data['on_langs'] = $this->mlangs->get_active_languages();
			
			$this->load->helper('sales/shipping_methods');
			$this->template->add_navigation($data['shipping_method']['shipping_method_description'][$this->id_langs]['name']);
			//eval('helper_payment_method_'.$data['payment_method']['payment_method']['alias'].'($data);');
			helper_shipping_method_add_edit_method($data);
			
			return TRUE;
		}
		return FALSE;
	}
	
	public function edit_method($id)
	{	
		if($this->isset_users_shipping_method($id))
		{
			$this->load->helper('sales/shipping_methods');
			if($data['shipping_method'] = $this->get_edit_shipping_method($id))
			{
				//$data['settings']['payment_method_settings'] = $this->get_payment_method_settings_values($data['payment_method']['payment_method'][self::ID_P_M], $id);
				
				$this->load->model('langs/mlangs');
				$data['on_langs'] = $this->mlangs->get_active_languages();
				
				$this->load->helper('sales/shipping_methods');
				//eval('helper_payment_method_'.$data['payment_method']['payment_method']['pm_alias'].'($data, $id);');
				helper_shipping_method_add_edit_method($data, $id);
				
				return TRUE;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function get_add_shipping_method($id)
	{
		$id = intval($id);
		$query = $this->db
				->select("A.`".self::ID_S_M."` AS ID, A.`alias`, B.`name`, B.`description`, B.`".self::ID_LANGS."`")
				->from("`".self::S_M."` AS A")
				->join(	"`".self::S_M_DESC."` AS B",
						"B.`".self::ID_S_M."` = A.`".self::ID_S_M."`",
						"LEFT")
				->where("A.`active`", 1)->where("(A.`id_users` = ".$this->id_users." OR A.`system` = 1)", NULL)->where("A.`".self::ID_S_M."`", $id);
		$result = $query->get()->result_array();
		if(count($result)>0)
		{
			$array = array();
			foreach($result as $ms)
			{
				$array['shipping_method'] = array('ID' => $ms['ID'], self::ID_S_M => $ms['ID'] ,'alias' => $ms['alias']);
				$array['shipping_method_description'][$ms[self::ID_LANGS]] = $ms;
			}
			return $array;
		}
		return FALSE;
	}
	
	public function get_edit_shipping_method($id)
	{
		$id = intval($id);
		$query = $this->db
				->select("SM.`alias` AS sm_alias, SM.`".self::ID_S_M."`, A.`".self::ID_U_S_M."` AS ID, A.`".self::ID_S_M."`, A.`alias`, A.`active`, A.`default`, B.`name`, B.`description`, B.`".self::ID_LANGS."`")
				->from("`".self::U_S_M."` AS A")
				->join("`".self::S_M."` AS SM",
						"SM.`".self::ID_S_M."` = A.`".self::ID_S_M."`",
						"INNER")
				->join("`".self::U_S_M_DESC."` AS B",
						"B.`".self::ID_U_S_M."` = A.`".self::ID_U_S_M."`",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_S_M."`", $id);
		$result = $query->get()->result_array();
		if(count($result)>0)
		{
			$array = array();
			foreach($result as $ms)
			{
				$array['shipping_method'] = array('sm_alias' => $ms['sm_alias'], self::ID_S_M => $ms[self::ID_S_M], 'ID' => $ms['ID'] ,'method_id' => $ms[self::ID_S_M], 'alias' => $ms['alias'], 'active' => $ms['active'], 'default' => $ms['default']);
				$array['shipping_method_description'][$ms[self::ID_LANGS]] = array('name' => $ms['name'], 'description' => $ms['description']);
			}
			return $array;
		}
		return FALSE;
	}
	
	public function get_shipping_methods_to_select()
	{
		$query = $this->db
				->select("A.`".self::ID_S_M."` AS ID, B.`name`, B.`description`")
				->from("`".self::S_M."` AS A")
				->join(	"`".self::S_M_DESC."` AS B",
						"B.`".self::ID_S_M."` = A.`".self::ID_S_M."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
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
	
	public function get_users_shipping_methods_to_select()
	{
		$array = array(array(), FALSE);
		$this->db->select("A.`".self::ID_U_S_M."`, B.`name`, A.`default`")
			->from("`".self::U_S_M."` AS A")
			->join(	"`".self::U_S_M_DESC."` AS B",
					"B.`".self::ID_U_S_M."` = A.`".self::ID_U_S_M."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->order_by("A.`sort`");
		$result = $this->db->get()->result_array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$array[0][$ms[self::ID_U_S_M]] = $ms['name'];
				if($ms['default'])
				{
					$array[1] = $ms[self::ID_U_S_M];
				}
			}
		}
		return $array;
	}
	
	public function isset_users_shipping_method($id)
	{
		if(is_int($id))
		{
			$query = $this->db
					->select("COUNT(*) AS COUNT")
					->from(self::U_S_M)
					->where("`".self::ID_USERS."`", $this->id_users)->where(self::ID_U_S_M, $id);
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
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`".self::U_S_M."`")->where("`".self::ID_USERS."`", $this->id_users)->where("`alias`", $alias)->limit(1);
		if($this->id_users_sm)
		{
			$query->where("`".self::ID_U_S_M."` <>", $this->id_users_sm);
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
			if($this->isset_users_shipping_method($id))
			{
				$this->db->trans_start();
				$data = $this->input->post('shipping_method');
				$data_update = $data;
				unset($data_update['id_m_shipping_methods']);
				
				if($data_update['default'] == 1)
				{
					$this->sql_add_data(array('default' => 0))->sql_save(self::U_S_M, array(self::ID_USERS => $this->id_users));
				}
				
				$this->sql_add_data($data_update)->sql_using_user()->sql_save(self::U_S_M, $id);
				
				$desc_array = array();
				$query = $this->db
						->select(self::ID_U_S_M_DESC.", ".self::ID_LANGS)
						->from(self::U_S_M_DESC)
						->where(self::ID_U_S_M, $id);
				$result = $query->get()->result_array();
				foreach($result as $ms)
				{
					$desc_array[$ms[self::ID_LANGS]] = $ms;
				}
				$this->load->model('langs/mlangs');
				$on_langs = $this->mlangs->get_active_languages();
				$data_langs = $this->input->post('shipping_method_description');
				foreach($on_langs as $key => $ms)
				{
					if(isset($data_langs[$key]))
					{
						if(isset($desc_array[$key]))
						{
							$this->sql_add_data($data_langs[$key])->sql_save(self::U_S_M_DESC, $desc_array[$key][self::ID_U_S_M_DESC]);
						}
						else
						{
							$this->sql_add_data($data_langs[$key]+array(self::ID_U_S_M => $id, self::ID_LANGS => $key))->sql_save(self::U_S_M_DESC);
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
			if($data = $this->input->post('shipping_method'))
			{
				$this->db->trans_start();
				if($data['default'] == 1)
				{
					$this->sql_add_data(array('default' => 0))->sql_save(self::U_S_M, array(self::ID_USERS => $this->id_users));
				}
				
				$id = $this->sql_add_data($data)->sql_using_user()->sql_save(self::U_S_M);
				$this->sql_add_data(array('sort' => $id))->sql_save(self::U_S_M, $id);
				
				$desc_array = array();
				$query = $this->db
						->select(self::ID_U_S_M_DESC, self::ID_LANGS)
						->from(self::U_S_M_DESC)
						->where(self::ID_U_S_M, $id);
				
				foreach($query->get()->result_array() as $ms)
				{
					$desc_array[$ms[self::ID_LANGS]] = $ms;
				}
				$this->load->model('langs/mlangs');
				$on_langs = $this->mlangs->get_active_languages();
				$data_langs = $this->input->post('shipping_method_description');
				foreach($on_langs as $key => $ms)
				{
					if(isset($data_langs[$key]))
					{
						if(isset($desc_array[$key]))
						{
							$this->sql_add_data($data_langs[$key])->sql_save(self::U_S_M_DESC, $desc_array[$key][self::ID_U_S_M_DESC]);
						}
						else
						{
							$this->sql_add_data($data_langs[$key]+array(self::ID_U_S_M => $id, self::ID_LANGS => $key))->sql_save(self::U_S_M_DESC);
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
	
	public function delete_shipping_method($id_users_sm)
	{
		if(is_int($id_users_sm))
		{
			$this->db->where(self::ID_USERS, $this->id_users)->where(self::ID_U_S_M, $id_users_sm);
			$this->db->delete(self::U_S_M);
			return TRUE;
		}
		return FALSE;
	}
	
	public function active_shipping_method($id_users_sm, $active = 0)
	{
		if(is_array($id_users_sm))
		{
			if($active == 1)
			{
				$this->db->where_in(self::ID_U_S_M, $id_users_sm)->where(self::ID_USERS, $this->id_users);
				$this->db->update(self::U_S_M, array('active' => 1));
			}
			else
			{
				$this->db->where_in(self::ID_U_S_M, $id_users_sm)->where(self::ID_USERS, $this->id_users);
				$this->db->update(self::U_S_M, array('active' => 0));
			}
			return TRUE;
		}
		return FALSE;
	}
	
	public function get_users_shipping_method_base_data($id_usm)
	{
		$query = $this->db
				->select("SM.`alias` AS sm_alias, SM.`".self::ID_S_M."` AS SM_ID, A.`".self::ID_U_S_M."` AS USM_ID, A.`alias` AS usm_alias, A.`active`, A.`default`")
				->from("`".self::U_S_M."` AS A")
				->join("`".self::S_M."` AS SM",
						"SM.`".self::ID_S_M."` = A.`".self::ID_S_M."`",
						"INNER")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_S_M."`", $id_usm)->limit(1);
		$result = $query->get()->row_array();
		if(count($result) > 0)
		{
			return $result;
		}
		return FALSE;
	}
	
	public function get_users_shipping_method_data($id_usm, $id_lang = FALSE)
	{
		$query = $this->db
				->select("SM.`alias` AS sm_alias, SM.`".self::ID_S_M."` AS SM_ID, A.`".self::ID_U_S_M."` AS USM_ID, A.`alias` AS usm_alias, A.`active`, A.`default`,
						B.`name`, B.`description`, B.`".self::ID_LANGS."`")
				->from("`".self::U_S_M."` AS A")
				->join("`".self::S_M."` AS SM",
						"SM.`".self::ID_S_M."` = A.`".self::ID_S_M."`",
						"INNER")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_S_M."`", $id_usm)->limit(1);
		if($id_lang)
		{
			$query->join("`".self::U_S_M_DESC."` AS B",
						"B.`".self::ID_U_S_M."` = A.`".self::ID_U_S_M."` && B.`".self::ID_LANGS."` = '".$id_lang."'",
						"LEFT");
		}
		else
		{
			$query->join("`".self::U_S_M_DESC."` AS B",
						"B.`".self::ID_U_S_M."` = A.`".self::ID_U_S_M."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						"LEFT");
		}		
		$result = $query->get()->row_array();
		if(count($result) > 0)
		{
			return $result;
		}
		return FALSE;
	}
}