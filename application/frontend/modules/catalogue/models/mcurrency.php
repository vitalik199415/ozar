<?php
class Mcurrency extends AG_Model
{
	const CUR 		= 'm_c_currency';
	const ID_CUR 	= 'id_m_c_currency';
	
	const UCUR 		= 'm_c_users_currency';
	const ID_UCUR 	= 'id_m_c_users_currency';
	
	public $id_currency = FALSE;
	public $currency = FALSE;
	
	function __construct()
	{
		parent::__construct();
		$this->get_current_currency();
	}
	
	public function get_current_currency()
	{
		if($this->currency)
		{
			return $this->currency;
		}
		if($id_currency = $this->session->userdata('CURRENCY_ID'))
		{
			$id_currency = intval($id_currency);
			$query = $this->db
				->select("B.`name`, B.`".self::ID_CUR."` AS ID, A.`rate`, A.`visible_rules`, A.`m_u_types`")
				->from("`".self::UCUR."` AS A")
				->join(	"`".self::CUR."` AS B",
						"B.`".self::ID_CUR."` = A.`".self::ID_CUR."`",
						"INNER")
				->where("`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_CUR."`", $id_currency)->limit(1);
			$result = $query->get()->row_array();
			if(count($result)>0)
			{
				$visible = FALSE;
				if($this->check_currency_visible($result['visible_rules'], $result['m_u_types'])) $visible = TRUE;
				if($visible)
				{
					$this->id_currency = $result['ID'];
					$this->currency = $result;
					return $result;
				}
				return $this->get_default_currency();
			}
			return $this->get_default_currency();
		}
		else
		{
			return $this->get_default_currency();
		}
	}
	
	public function get_default_currency()
	{
		$query = $this->db
			->select("B.`name`, B.`".self::ID_CUR."` AS ID, A.`rate`, A.`visible_rules`, A.`m_u_types`")
			->from("`".self::UCUR."` AS A")
			->join(	"`".self::CUR."` AS B",
					"B.`".self::ID_CUR."` = A.`".self::ID_CUR."`",
					"INNER")
			->where("`".self::ID_USERS."`", $this->id_users)->order_by("A.`default_selected`", "DESC");
		$result = $query->get()->result_array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$visible = FALSE;
				if($this->check_currency_visible($ms['visible_rules'], $ms['m_u_types'])) $visible = TRUE;
				if($visible)
				{
					$this->id_currency = $ms['ID'];
					$this->currency = $ms;
					$this->session->set_userdata('CURRENCY_ID', $ms['ID']);
					return $ms;
				}
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function get_currency_to_select()
	{
		$currency_array = array();
		$query = $this->db->select("`".self::ID_CUR."` AS CUR_ID, `name`, `visible_rules`, `m_u_types`")
				->from("`".self::UCUR."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1)->order_by("`default_selected`", "DESC");
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			if($this->check_currency_visible($ms['visible_rules'], $ms['m_u_types']))
			{
				$currency_array[$ms['CUR_ID']] = $ms['name'];
			}
		}
		$selected_cur = $this->get_current_currency();
		return array('currency_array' => $currency_array, 'selected_currency' => $selected_cur);
	}
	
	public function change_currency($currency_id)
	{
		if(!$this->check_isset_currency($currency_id)) return FALSE;
		$this->session->set_userdata('CURRENCY_ID', $currency_id);
		return TRUE;
	}
	
	public function check_isset_currency($currency_id)
	{
		$query = $this->db->select("COUNT(*) AS COUNT")
				->from("`".self::UCUR."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_CUR."`", $currency_id)->where("`active`", 1);
		$result = $query->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function check_currency_visible($visible_rules, $m_u_types)
	{
		$visible = TRUE;
		if($visible_rules > 0)
		{
			if($visible_rules == 1)
			{
				if(($customer = $this->session->userdata('customer_id')) == FALSE) $visible = FALSE;
			}
			else if($visible_rules == 2)
			{
				if(($customer = $this->session->userdata('customer_id')) == FALSE)
				{
					$result['visible'] = FALSE;
				}
				else
				{
					$customer = $this->session->userdata('CUSTOMER');
					$visible = FALSE;
					$t_m_u_types = explode(',', $m_u_types);
					$customer_m_u_types = $customer['m_u_types'];
					foreach($t_m_u_types as $ms1)
					{
						if(isset($customer_m_u_types[$ms1]))
						{
							$visible = TRUE;
							break;
						}
					}
				}
			}
		}
		return $visible;
	}
	/*public function get_user_currency()
	{
		$array = array();
		$query = $this->db->select("A.`".self::ID_UCUR."` AS ID, A.`".self::ID_CUR."` AS CID, A.`rate`, A.`name`, A.`active`, A.`default`, A.`default_selected`, A.`visible_rules`, A.`m_u_types`");
		$query	->from("`".self::UCUR."` AS A")
				->where("A.`".self::ID_USERS."`",$this->id_users)->order_by("A.`default_selected`", "DESC");
		$result = $query->get()->result_array();
		if(is_array($result) && count($result)>0)
		{
			foreach($result as $ms)
			{
				$array['currency'][$ms['CID']] = $ms['name'];
				
				if($ms['default_selected'] == 1)
				{
					$array['default_selected'] = $ms['CID'];
				}
				
				if($ms['default'] == 1)
				{
					$array['default'] = $ms['CID'];
				}				
			}
		}
		else
		{
			$values = $this->get_currency(TRUE);
			$this->sql_add_data(array(self::ID_CUR => $values['ID'], self::ID_USERS => $this->id_users, 'default' => '1', 'default_selected' => '1', 'name' => $values['name']))->sql_save(self::UCUR);
			return $this->get_user_currency_collection();
		}
		return $array;
	}*/
	
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
	
	public function get_currency($id = FALSE)
	{
		$query = $this->db
				->select("A.`".self::ID_CUR."` AS ID, A.`name`, A.`code`")
				->from("`".self::CUR."` AS A")
				->where("A.`active`", 1);
		if($id > 0)
		{
			$query->where("A.`".self::ID_CUR."`", $id);
			$query->limit(1);
			return $query->get()->row_array();
		}
		else
		{
			$query->order_by("A.`default`", "DESC");
			$query->limit(1);
			return $query->get()->row_array();
		}
	}
	
	public function get_base_currency()
	{
		$query = $this->db
				->select("A.*")
				->from("`".self::UCUR."` AS A")
				->join(	"`".self::CUR."` AS B",
						"B.`".self::ID_CUR."` = A.`".self::ID_CUR."`",
						"INNER")
				->where("`".self::ID_USERS."`", $this->id_users)->where("A.`default`","1")->limit(1);
		$result = $query->get()->row_array();
		if(count($result)==0)
		{
			$values = $this->get_currency(TRUE);
			$this->sql_add_data(array(self::ID_CUR => $values['ID'], self::ID_USERS => $this->id_users, 'default' => '1', 'default_selected' => '1', 'name' => $values['name']))->sql_save(self::UCUR);
			return $this->get_base_currency();
		}
		return $result;
	}
	
	public function get_default_currency_name()
	{
		$query = $this->db
				->select("B.`name`")
				->from("`".self::UCUR."` AS A")
				->join(	"`".self::CUR."` AS B",
						"B.`".self::ID_CUR."` = A.`".self::ID_CUR."`",
						"inner")
				->where("`".self::ID_USERS."`", $this->id_users)->where("A.`default`","1")->limit(1);
		$result = $query->get()->row_array();
		if(count($result)==0)
		{
			$values = $this->get_currency(TRUE);
			$this->sql_add_data(array(self::ID_CUR => $values['ID'], self::ID_USERS => $this->id_users, 'default' => '1', 'default_selected' => '1', 'name' => $values['name']))->sql_save(self::UCUR);
			return $values['name'];
		}
		return $result['name'];
	}
}
?>