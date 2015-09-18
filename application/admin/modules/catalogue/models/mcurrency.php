<?php
class Mcurrency extends AG_Model
{
	const CUR 		= 'm_c_currency';
	const ID_CUR 	= 'id_m_c_currency';
	
	const UCUR 		= 'm_c_users_currency';
	const ID_UCUR 	= 'id_m_c_users_currency';
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function edit()
	{
		$data = array();
		$data['data_currency'] = $this->get_currency();
		$data['users_currency'] = $this->get_user_currency_collection();
		
		$this->load->model('customers/mcustomers_types');
		$data['customers_types'] = $this->mcustomers_types->get_customers_types();
		
		$this->load->helper('catalogue/currency_helper');
		
		helper_currency_form_build($data);
	}
	
	public function save()
	{
		if($POST = $this->input->post('users_currency'))
		{
			//$POST = $_POST['users_currency'];
			$POSTD = $this->input->post('users_currency_desc');
			$DEFAULT = $POSTD['default'];
			$DEFAULT_SELECTED = $POSTD['default_selected'];
			unset($POSTD['default']);
			unset($POSTD['default_selected']);
			
			$currency = $this->get_currency();
			$user_currency = $this->get_user_currency_collection();
			
			$user_currency = $user_currency['users_currency_desc'];
			//$user_def_currency = $user_currency['default'];
			//$user_def_currency_selected = $user_currency['default_selected'];
			unset($user_currency['default']);
			unset($user_currency['default_selected']);
			
			
			$UPD_DEFAULT = FALSE;
			$UPD_DEFAULT_SELECTED = FALSE;
			$this->db->trans_start();
			foreach($POST as $ms)
			{
				if(isset($currency[$ms]))
				{
					$data = $POSTD[$ms]+array('name' => $currency[$ms]['name']);
					unset($data['ID']);
					
					if($DEFAULT == $ms)
					{
						$this->sql_add_data(array('default' => '1'));
						$data['rate'] = 1;
						$UPD_DEFAULT = TRUE;
					}
					else
					{
						$this->sql_add_data(array('default' => '0'));
					}
					
					if($DEFAULT_SELECTED == $ms)
					{
						$this->sql_add_data(array('default_selected' => '1'));
						$UPD_DEFAULT_SELECTED = TRUE;
					}
					else
					{
						$this->sql_add_data(array('default_selected' => '0'));
					}
					
					if(floatval($data['rate'])<=0)
					{
						$data['rate'] = 1;
					}
					
					$data['visible_rules'] = intval($data['visible_rules']);
					if($data['visible_rules'] == 2)
					{
						if(isset($data['m_u_types']) && is_array($data['m_u_types']))
						{
							$m_u_types_temp = $data['m_u_types'];
							unset($data['m_u_types']);
							$data['m_u_types'] = '';
							
							foreach($m_u_types_temp as $tkey => $tms)
							{
								$data['m_u_types'] .= $tms.',';
							}
							$data['m_u_types'] = substr($data['m_u_types'],0,-1);
						}
						else
						{
							$data['visible_rules'] = 1;
							$data['m_u_types'] = NULL;
						}
					}
					else
					{
						$data['m_u_types'] = NULL;
					}
					
					if(isset($user_currency[$ms]))
					{
						$this->sql_add_data($data)->sql_using_user()->sql_save(self::UCUR, $POSTD[$ms]['ID']);
						unset($user_currency[$ms]);
					}
					else
					{
						$this->sql_add_data($data+array(self::ID_CUR => $ms))->sql_using_user()->sql_save(self::UCUR);
					}
				}	
			}
			if(!$UPD_DEFAULT)
			{
				foreach($POST as $ms)
				{
					if(isset($currency[$ms]))
					{
						$this->sql_add_data(array('default' => '1', 'rate' => 1))->sql_using_user()->sql_save(self::UCUR, array(self::ID_CUR => $ms));
						break;
					}	
				}
			}
			if(!$UPD_DEFAULT_SELECTED)
			{
				foreach($POST as $ms)
				{
					if(isset($currency[$ms]))
					{
						$this->sql_add_data(array('default_selected' => '1'))->sql_using_user()->sql_save(self::UCUR, array(self::ID_CUR => $ms));
						break;
					}	
				}
			}
			if(is_array($user_currency))
			{
				foreach($user_currency as $ms)
				{
					$this->db->where(self::ID_UCUR, $ms['ID']);
					$this->db->delete(self::UCUR);
				}
			}
			$this->db->trans_complete();
			if($this->db->trans_status()) 
			{
				return TRUE; 
			}
			return FALSE;
		}
		return FALSE;
	}

	public function get_users_currency_to_select()
	{
		$users_currency = array(array(), FALSE);
		$this->db->select("A.`".self::ID_CUR."` AS CID, A.`name`, A.`default_selected`")
			->from("`".self::UCUR."` AS A")
			->where("A.`".self::ID_USERS."`",$this->id_users);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$users_currency[0][$ms['CID']] = $ms['name'];
			if($ms['default_selected'] == 1)
			{
				$users_currency[1] = $ms['CID'];
			}
		}
		return $users_currency;
	}

	public function get_user_currency_collection($to_select = FALSE)
	{
		$array = array();
		if(!$to_select)
		{
			$query = $this->db->select("A.`".self::ID_UCUR."` AS ID, A.`".self::ID_CUR."` AS CID, A.`rate`, A.`name`, A.`active`, A.`default`, A.`visible_rules`, A.`m_u_types`, A.`default_selected`, A.`default_selected` AS SORT");
		}
		else
		{
			$query = $this->db->select("A.`".self::ID_CUR."` AS CID, A.`name`, A.`default_selected`, A.`default_selected` AS SORT");
		}
		$query	->from("`".self::UCUR."` AS A")
				->where("A.`".self::ID_USERS."`",$this->id_users)->order_by("SORT","DESC");
		$result = $query->get()->result_array();
		if(is_array($result) && count($result)>0)
		{
			if(!$to_select)
			{
				foreach($result as $ms)
				{
					$array['users_currency'][$ms['CID']] = $ms['CID'];
					if($ms['m_u_types'] !== NULL)
					{
						$m_u_types = explode(',', $ms['m_u_types']);
						if(is_array($m_u_types))
						{
							$ms['m_u_types'] = array();
							foreach($m_u_types as $m_u_t)
							{
								$ms['m_u_types'][$m_u_t] = $m_u_t;
							}
						}
					}
					$array['users_currency_desc'][$ms['CID']] = $ms;
					if($ms['default'] == 1)
					{
						$array['users_currency_desc']['default'] = $ms['CID'];
					}
					if($ms['default_selected'] == 1)
					{
						$array['users_currency_desc']['default_selected'] = $ms['CID'];
					}
				}
			}
			else
			{
				foreach($result as $ms)
				{
					$array['currency'][$ms['CID']] = $ms['name'];
					if($ms['default_selected'] == 1)
					{
						$array['default_selected'] = $ms['CID'];
					}	
				}	
			}
		}
		else
		{
			$values = $this->get_currency(TRUE);
			$this->sql_add_data(array(self::ID_CUR => $values['ID'], self::ID_USERS => $this->id_users, 'default' => '1', 'default_selected' => '1', 'name' => $values['name']))->sql_save(self::UCUR);
			return $this->get_user_currency_collection($to_select);
		}
		return $array;
	}
	
	public function get_user_currency($id)
	{
		$array = array();
		
		$query = $this->db->select("A.`".self::ID_UCUR."` AS ID, A.`".self::ID_CUR."` AS CID, A.`rate`, A.`name`, A.`active`, A.`default`, A.`permission`, A.`default_selected`, A.`default_selected` AS SORT");
		$query	->from("`".self::UCUR."` AS A")
				->where("A.`".self::ID_USERS."`",$this->id_users)->where("A.`".self::ID_CUR."`", $id)->order_by("SORT","DESC")->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		return FALSE;
	}
	
	public function get_currency($default = FALSE)
	{
		$array = array();
		$query = $this->db
				->select("A.`".self::ID_CUR."` AS ID, A.`name`")
				->from("`".self::CUR."` AS A")
				->where("A.`active`","1")->order_by("A.`default`", "DESC")->order_by("A.`".self::ID_CUR."`");
		if($default)
		{
			$query->limit(1);
			return $query->get()->row_array();
		}
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$array[$ms['ID']] = $ms;
		}
		return $array;
	}
	
	public function get_default_currency_name()
	{
		$query = $this->db
				->select("B.`name`")
				->from("`".self::UCUR."` AS A")
				->join(	"`".self::CUR."` AS B",
						"B.`".self::ID_CUR."` = A.`".self::ID_CUR."`",
						"inner")
				->where("`".self::ID_USERS."`", $this->id_users)->where("A.`default`", 1)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)==0)
		{
			$values = $this->get_currency(TRUE);
			$this->sql_add_data(array(self::ID_CUR => $values['ID'], self::ID_USERS => $this->id_users, 'default' => '1', 'default_selected' => '1', 'name' => $values['name']))->sql_save(self::UCUR);
			return $values['name'];
		}
		return $result['name'];		
	}
	
	protected function insert_default_users_currency()
	{
		$query = $this->db->select("*")
					->from("`".self::CUR."`")
					->where("`default`", 1)->limit(1);
		$result = $query->get()->row_array();
		$array = $result + array('rate' => '1.0', 'visible_rules' => '0', 'm_u_types' => NULL, 'default_selected' => 1, self::ID_USERS => $this->id_users);
		$this->sql_add_data($array)->sql_save(self::UCUR);
		return $array;
	}
	
	public function get_users_default_currency()
	{
		$this->db->select("*")
			->from("`".self::UCUR."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`default`", 1)->limit(1);
		$result = $this->db->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		else
		{
			return $this->insert_default_users_currency();
		}
	}

	public function get_users_base_currency()
	{
		$this->db->select("*")
				 ->from("`".self::UCUR."` AS A")
				 ->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`default`", 1)->limit(1);
		$result = $this->db->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		else
		{
			return $this->insert_default_users_currency();
		}
	}
	
	public function get_users_currency($id_user_currency)
	{
		$this->db->select("*")
			->from("`".self::UCUR."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_UCUR."`", $id_user_currency)->limit(1);
		$result = $this->db->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		return FALSE;
	}

	public function get_users_currency_by_cid($id_currency)
	{
		$this->db->select("*")
				 ->from("`".self::UCUR."` AS A")
				 ->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_CUR."`", $id_currency)->limit(1);
		$result = $this->db->get()->row_array();
		if(count($result)>0)
		{
			return $result;
		}
		return FALSE;
	}
	
	public function get_users_currency_collection($to_select = FALSE)
	{
		
	}
}
?>