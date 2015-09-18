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
	
	const ORD 		= 'm_orders';
	const ID_ORD 	= 'id_m_orders';
	const INV		= 'm_orders_invoices';
	const ID_INV	= 'id_m_orders_invoices';
	
	protected $default_settings_array = array(
		'2' => array(
			'settings_max_transaction_sum' => 100,
			'settings_max_day_transaction_sum' => 1000,
			'settings_max_month_transaction_sum' => 10000
		)
	);

	public function get_payment_methods_to_select()
	{
		$array = array();
		$query = $this->db->select("A.`".self::ID_U_P_M."` AS ID, B.`name`")
				->from("`".self::U_P_M."` AS A")
				->join("`".self::U_P_M_DESC."` AS B",
						"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("`".self::ID_USERS."`", $this->id_users)->order_by("A.`sort`");
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$array[$ms['ID']] = $ms['name'];
		}
		return $array;
	}
	
	public function get_form_payment_method_data()
	{
		$query = $this->db->select("A.`".self::ID_U_P_M."` AS ID, C.`".self::ID_P_M."` AS PM_ID, B.`name`, B.`description`, C.`alias`, C.`".self::ID_P_M."`")
				->from("`".self::U_P_M."` AS A")
				->join("`".self::P_M."` AS C",
						"C.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
						"INNER")
				->join("`".self::U_P_M_DESC."` AS B",
						"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`active`", 1)->order_by("A.`default`", "DESC")->order_by("A.`sort`")->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			if($this->session->userdata('customer_id'))
			{
				$this->load->model('customers/mcustomers');
				$data['customer_B_address_data'] = $this->mcustomers->get_customer_address($this->session->userdata('customer_id'), 'B');
			}
			$data['payment_methods_select'] = $this->get_payment_methods_to_select();
			$data['payment_methods_select_active'] = $result['ID'];
			$data['payment_methods_data'] = $result;
			return $data;
		}
		else
		{
			$data = array();
			if($this->session->userdata('customer_id'))
			{
				$this->load->model('customers/mcustomers');
				$data['customer_B_address_data'] = $this->mcustomers->get_customer_address($this->session->userdata('customer_id'), 'B');
			}
			return $data;
		}
	}
	
	public function get_payment_method_description($id)
	{
		$query = $this->db->select("A.`".self::ID_U_P_M."` AS ID, C.`".self::ID_P_M."` AS PM_ID, B.`name`, B.`description`, C.`alias`, C.`".self::ID_P_M."`")
					->from("`".self::U_P_M."` AS A")
					->join("`".self::P_M."` AS C",
							"C.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
							"INNER")
					->join("`".self::U_P_M_DESC."` AS B",
							"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_P_M."`", $id)->limit(1);
			$result = $query->get()->row_array();
		return $result['description'];
	}
	
	public function get_payment_method_form($id)
	{
		if(is_int($id))
		{
			$query = $this->db->select("A.`".self::ID_U_P_M."` AS ID, C.`".self::ID_P_M."` AS PM_ID, B.`name`, B.`description`, C.`alias`, C.`".self::ID_P_M."`")
					->from("`".self::U_P_M."` AS A")
					->join("`".self::P_M."` AS C",
							"C.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
							"INNER")
					->join("`".self::U_P_M_DESC."` AS B",
							"B.`".self::ID_U_P_M."` = A.`".self::ID_U_P_M."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_P_M."`", $id)->limit(1);
			$result = $query->get()->row_array();
			if(count($result)>0)
			{
				if($this->session->userdata('customer_id'))
				{
					$this->load->model('customers/mcustomers');
					$data['customer_B_address_data'] = $this->mcustomers->get_customer_address($this->session->userdata('customer_id'), 'B');
				}
				if(($post_address = $this->input->post('order_address')) && isset($post_address['B']))
				{
					foreach($post_address['B'] as $key => $ms)
					{
						$data['customer_B_address_data'][$key] = $ms;
					}
				}
				$data['payment_methods_select'] = $this->get_payment_methods_to_select();
				$data['payment_methods_select_active'] = $result['ID'];
				$data['payment_methods_data'] = $result;
				return $this->build_payment_methods_form($result['alias'], $data);
			}
			else
			{
				return FALSE;
			}
		}
		return FALSE;
	}
	
	public function build_payment_methods_form($alias, $data = array())
	{
		return $this->load->view('sales/orders/payment_methods/default_form', array('payment_method_alias' => $alias)+$data, TRUE);
		/*if($alias == 'default')
		{
			return $this->load->view('sales/orders/payment_methods/default_form', array('payment_method_alias' => $alias)+$data, TRUE);
		}*/
		//$shipping_form_data = array('payment_method_alias' => $alias, 'payment_method_data' => $data);
		//return $this->load->view('sales/payment_methods/default_form', $shipping_form_data, TRUE);
		/*
		$fields_array = $this->get_shipping_methods_field($data['SM_ID']);
		$shipping_form_data = array('alias' => $alias, 'shipping_fields' => $fields_array, 'shipping_data' => $data);
		return $this->load->view('sales/payment_methods/default_form', $shipping_form_data, TRUE);*/
	}
	
	public function get_payment_method_data($id_upm)
	{
		$query = $this->db
				->select("PM.`alias` AS pm_alias, PM.`".self::ID_P_M."` AS PM_ID, A.`".self::ID_U_P_M."` AS UPM_ID, A.`alias` AS upm_alias, A.`active`, A.`default`")
				->from("`".self::U_P_M."` AS A")
				->join("`".self::P_M."` AS PM",
						"PM.`".self::ID_P_M."` = A.`".self::ID_P_M."`",
						"INNER")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_U_P_M."`", $id_upm)->limit(1);
		$pm = $query->get()->row_array();
		if(count($pm) == 0) return FALSE;
		
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
		return array('pm' => $pm, 'pm_settings' => $pm_settings);
	}
	
	public function get_payment_method_data_html($id_upm, $invoice_data)
	{
		if($PM = $this->get_payment_method_data($id_upm))
		{
			$pm = $PM['pm'];
			$pm_settings = $PM['pm_settings'];
			return $this->load->view('sales/payment_methods/letters/'.$this->mlangs->language.'/'.$pm['pm_alias'], $invoice_data + array('pm_settings' => $pm_settings), TRUE);
		}
		return '';
	}
	
	public function check_pb_ekvaring($invoice_number, $order_number, $type, $code)
	{
		$query = $this->db->select("*")
				->from("`".self::INV."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`invoices_number`", $invoice_number)->limit(1);
		if(count($invoice = $query->get()->row_array()) == 0) return FALSE;
		if(md5($invoice[self::ID_ORD].'-'.$invoice[self::ID_INV].'-'.$invoice['create_date']) != $code) return FALSE;
		$query = $this->db->select("*")
				->from("`".self::ORD."`")
				->where("`".self::ID_ORD."`", $invoice[self::ID_ORD])->where("`".self::ID_USERS."`", $this->id_users)->where("`orders_number`", $order_number)->limit(1);
		if(count($order = $query->get()->row_array()) == 0) return FALSE;
		
		if(!$this->input->post('operation_xml') || !$this->input->post('signature')) return FALSE;
		$operation_xml = $this->input->post('operation_xml');
		$signature = $this->input->post('signature');
		
		if(($PM = $this->get_payment_method_data($order[self::ID_U_P_M])) === FALSE) return FALSE;
		$pm = $PM['pm'];
		$pm_settings = $PM['pm_settings'];
		
		if($pm['pm_alias'] != 'privatbank_ekvaring') return FALSE;
		
		$settings_merchant_id = $pm_settings['fields_merchant_id']['value'];
		$settings_signature = $pm_settings['fields_signature']['value'];
		
		$operation_xml_decoded = base64_decode($operation_xml);
		$my_signature = base64_encode(sha1($settings_signature.$operation_xml_decoded.$settings_signature, 1));
		if($my_signature != $signature) return FALSE;
		
		$xml = simplexml_load_string($operation_xml_decoded);
	
		$status = $xml->status;
		if($status == 'success') return 2;
		else if($status == 'wait_secure') return 1;
		else if($status == 'failure') return 0;
	}
	
	public function check_pb_ekvaring_status($invoice_number, $order_number, $type, $code)
	{
		return $this->check_pb_ekvaring($invoice_number, $order_number, $type, $code);
	}
	
	public function check_pb_ekvaring_data($invoice_number, $order_number, $type, $code)
	{
		$status = $this->check_pb_ekvaring($invoice_number, $order_number, $type, $code);
		if($status == 2)
		{
			$this->update_invoice_state($invoice_number, 'C');
			$this->update_order_state($order_number, 'IC');
		}
		else if($status == 1)
		{
			$this->update_invoice_state($invoice_number, 'S');
			$this->update_order_state($order_number, 'IS');
		}
	}
	
	public function update_invoice_state($invoice_number, $state)
	{
		$this->sql_add_data(array('invoices_status' => $state))->sql_using_user()->sql_save(self::INV, array('invoices_number' => $invoice_number));
	}
	
	public function update_order_state($order_number, $state)
	{
		$this->sql_add_data(array('orders_state' => $state))->sql_using_user()->sql_save(self::ORD, array('orders_number' => $order_number));
	}
}
?>