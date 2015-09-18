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

	public function get_shipping_methods_to_select()
	{
		$array = array();
		$query = $this->db->select("A.`".self::ID_U_S_M."` AS ID, B.`name`")
				->from("`".self::U_S_M."` AS A")
				->join("`".self::U_S_M_DESC."` AS B",
						"B.`".self::ID_U_S_M."` = A.`".self::ID_U_S_M."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->order_by("A.`sort`");
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$array[$ms['ID']] = $ms['name']; 
		}
		return $array;
	}
	
	public function get_form_shipping_method_data($id = FALSE)
	{
		$query = $this->db->select("A.`".self::ID_U_S_M."` AS ID, C.`".self::ID_S_M."` AS SM_ID, B.`name`, B.`description`, C.`alias`, C.`".self::ID_S_M."`")
				->from("`".self::U_S_M."` AS A")
				->join("`".self::S_M."` AS C",
						"C.`".self::ID_S_M."` = A.`".self::ID_S_M."`",
						"INNER")
				->join("`".self::U_S_M_DESC."` AS B",
						"B.`".self::ID_U_S_M."` = A.`".self::ID_U_S_M."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->order_by("A.`default`", "DESC")->order_by("A.`sort`")->limit(1);
		if($id) $query->where("A.`".self::ID_U_S_M."`", $id);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			if($this->session->userdata('customer_id'))
			{
				$this->load->model('customers/mcustomers');
				$data['customer_S_address_data'] = $this->mcustomers->get_customer_address($this->session->userdata('customer_id'), 'S');
			}
			$data['shipping_methods_select'] = $this->get_shipping_methods_to_select();
			$data['shipping_methods_select_active'] = $result['ID'];
			$data['shipping_methods_data'] = $result;
			$data['shipping_method_fields'] = $this->get_shipping_methods_field($result['SM_ID']);
			$data['shipping_method_alias'] = $result['alias'];
			return $data;
		}
		else if(!$id)
		{
			$data = array();
			if($this->session->userdata('customer_id'))
			{
				$this->load->model('customers/mcustomers');
				$data['customer_S_address_data'] = $this->mcustomers->get_customer_address($this->session->userdata('customer_id'), 'S');
			}
			return $data;
		}
		return FALSE;
	}
	
	public function build_shipping_methods_form($alias, $data = array())
	{
		if($alias == 'default')
		{
			return $this->load->view('sales/orders/shipping_methods/default_form', array('shipping_method_alias' => $alias)+$data, TRUE);
		}
		else
		{
			$shipping_form_data = array('shipping_method_alias' => $alias) + $data;
			return $this->load->view('sales/orders/shipping_methods/default_form', $shipping_form_data, TRUE);
		}
	}
	
	protected function get_shipping_methods_field($id)
	{
		$query = $this->db->select("`field`, `required`")
				->from("`".self::S_M_S_FIELDS."`")
				->where("`".self::ID_S_M."`", $id)->order_by("sort");
		$result = $query->get()->result_array();
		return $result;
	}
}
?>