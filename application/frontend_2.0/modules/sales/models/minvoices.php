<?php
class Minvoices extends AG_Model
{
	const ORD = 'm_orders';
	const ID_ORD = 'id_m_orders';
	const ORD_ADDR = 'm_orders_address';
	const ORD_PR = 'm_orders_products';
	const ID_ORD_PR = 'id_m_orders_products';
	const INV = 'm_orders_invoices';
	const ID_INV = 'id_m_orders_invoices';
	
	public function confirm_invoice($inv_number, $code)
	{
		$query = $this->db->select("*")
				->from("`".self::INV."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`invoices_number`", $inv_number)->limit(1);
		$invoice = $query->get()->row_array();
		if(count($invoice)>0)
		{
			if($invoice['invoices_status'] == 'CN') return FALSE;
			$md5 = md5($invoice[self::ID_INV].'-'.$invoice[self::ID_ORD].'-'.$invoice['create_date']);
			if($code == $md5 && $invoice['invoices_status'] == 'N')
			{
				$this->db->trans_start();
				
				$this->sql_add_data(array('invoices_status' => 'P'))->sql_update_date()->sql_save(self::INV, $invoice[self::ID_INV]);
				$this->sql_add_data(array('orders_state' => 'I'))->sql_update_date()->sql_save(self::ORD, $invoice[self::ID_ORD]);
				
				$this->db->trans_complete();
				if($this->db->trans_status())
				{	
					return 3;
				}
			}
			else if($code == $md5 && $invoice['invoices_status'] != 'N')
			{
				return 2;
			}
			else
			{
				return 1;
			}
		}
		return FALSE;
	}
	
	public function get_payment_method_html($inv_number)
	{
		$query = $this->db->select("*")
				->from("`".self::INV."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`invoices_number`", $inv_number)->limit(1);
		$invoice = $query->get()->row_array();
		
		$query = $this->db->select("*")
				->from("`".self::ORD."`")
				->where("`".self::ID_ORD."`", $invoice[self::ID_ORD])->limit(1);
		$order = $query->get()->row_array();
		
		$query = $this->db->select("*")
				->from("`".self::ORD_ADDR."`")
				->where("`".self::ID_ORD."`", $invoice[self::ID_ORD])->limit(2);
		$order_addresses_temp = $query->get()->result_array();
		$order_addresses = array();
		foreach($order_addresses_temp as $ms)
		{
			$order_addresses[$ms['type']] = $ms;
		}
		
		$this->load->model('catalogue/mcurrency');
		$currency = $this->mcurrency->get_currency($order['id_m_c_currency']);
		
		$pm_data = array('invoice' => $invoice, 'order' => $order, 'order_addresses' => $order_addresses, 'currency' => $currency);
		
		$this->load->model('sales/mpayment_methods');
		$payment_method_html = $this->mpayment_methods->get_payment_method_data_html($order['id_m_users_payment_methods'], $pm_data);
		
		return $payment_method_html;
	}
}
?>