<?php
class Mwarehouses_invoices extends AG_Model
{
	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';

	const WH_PR 		= 'wh_products';
	const ID_WH_PR 		= 'id_wh_products';

	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';

	const ORD 		= 'm_orders';
	const ID_ORD 	= 'id_m_orders';

	const WH_LOGS		= 'wh_log';
	const ID_WH_LOGS	= 'id_wh_log';
	const WH_SALES			= 'wh_log_sale';
	const ID_WH_SALES		= 'id_wh_log_sale';
	const WH_SALES_ADDR		= 'wh_log_sale_address';
	const WH_SALES_PR		= 'wh_log_sale_products';
	const ID_WH_SALES_PR	= 'id_wh_log_sale_products';
	const WH_SALES_PR_ATTR	= 'wh_log_sale_products_attributes';

	const WH_INV		= 'wh_log_sale_invoice';
	const ID_WH_INV		= 'id_wh_log_sale_invoice';

	public static function get_wh_invoices_state_collection()
	{
		return array('N' => 'Новый', 'P' => 'В процессе(Подтвержден)', 'S' => 'Проверка оплаты', 'C' => 'Оплачен', 'CN' => 'Отменен', 'COD' => 'Наложенный платеж', 'CM' => 'Возврат');
	}

	public static function get_wh_invoices_state_name($key)
	{
		$state_collection = self::get_wh_invoices_state_collection();
		if(isset($state_collection[$key]))
		{
			return $state_collection[$key];
		}
		return FALSE;
	}

	public function render_wh_invoices_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("wh_sales_invoices_grid", array('sort' => self::ID_WH_INV, 'desc' => 'DESC'));

		$this->grid->db
			->select("A.`".self::ID_WH_INV."` AS ID, A.`wh_invoice_number`, A.`wh_invoice_state`, A.`create_date`, A.`update_date`, B.`wh_sale_number`, B.`total_qty`, CONCAT(B.`total`, ' ', B.`base_currency_name`) AS total, C.`alias` as `wh_alias`")
			->from("`".self::WH_INV."` AS A")
			->join(	"`".self::WH_SALES."` AS B",
				"B.`".self::ID_WH_SALES."` = A.`".self::ID_WH_SALES."`",
				"LEFT")
			->join("`".self::WH_LOGS."` AS LOG",
				"LOG.`".self::ID_WH_LOGS."` = B.`".self::ID_WH_LOGS."`",
				"LEFT")
			->join(	"`".self::WH."` AS C",
				"C.`".self::ID_WH."` = LOG.`".self::ID_WH."`",
				"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$this->load->helper("warehouses_invoices");
		helper_wh_invoices_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data("wh_invoice_state", self::get_wh_invoices_state_collection());
		$this->grid->render_grid();
	}

	/*public function view_invoice($inv_id)
	{
		$invoice_data = $this->get_invoice_data($inv_id);
		$this->load->helper("invoices");
		helper_invoices_view($invoice_data);
		return TRUE;
	}

	public function get_invoice_data($inv_id)
	{
		if(!$invoice = $this->get_invoice($inv_id)) return FALSE;
		$this->load->model('sales/morders');
		$order_data = $this->morders->get_order_data($invoice[self::ID_ORD]);
		$invoice_data = $order_data;
		$invoice_data['invoice'] = $invoice;
		return $invoice_data;
	}*/

	public function get_invoice($inv_id)
	{
		$this->db->select("*")
				 ->from("`".self::WH_INV."`")
				 ->where("`".self::ID_WH_INV."`", $inv_id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($invoice = $this->db->get()->row_array()) == 0) return FALSE;
		return $invoice;
	}

	public function get_sale_invoice($sale_id)
	{
		$this->db->select("*")
				 ->from("`".self::WH_INV."`")
				 ->where("`".self::ID_WH_SALES."`", $sale_id)->where("`".self::ID_USERS."`", $this->id_users)
				 ->where("wh_invoice_state <>", 'CN')->limit(1);
		if(count($result = $this->db->get()->row_array()) == 0) return FALSE;
		return $result;
	}

	/*public function create_invoice($ord_id)
	{
		if($this->get_order_invoice($ord_id)) return FALSE;
		$this->load->model('sales/morders');
		if(!$this->morders->check_possibility_creating_invoice($ord_id)) return FALSE;
		$order_data = $this->morders->get_order_data($ord_id);
		$this->load->helper("invoices");
		helper_invoices_create_invoice($ord_id, $order_data);
		return TRUE;
	}*/

	/*public function save_invoice($sale_id)
	{
		$sale_id = intval($sale_id);
		if($sale_id <= 0) return FALSE;
		$this->load->model('warehouse/mwarehouses_sales');
		if(!$this->mwarehouses_sales->isset_sale($sale_id)) return FALSE;

		$IPOST = $this->input->post('invoice');
		if($this->get_sale_invoice($sale_id)) return FALSE;

		$this->db->select("MAX(invoices_number) AS invoices_number")
				 ->from("`".self::INV."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$invoices_number = $this->db->get()->row_array();
		$max = 1;
		if(count($invoices_number)>0)
		{
			$max = intval($invoices_number['invoices_number']) + 1;
		}
		$invoice_number = str_repeat("0", 8-strlen($max)).($max);

		$this->db->trans_start();

		$this->load->model('warehouse/mwarehouses');
		$wh_id = $this->mwarehouses->get_shop_wh();
		if($wh_id)
		{
			$this->load->model('warehouse/mwarehouses_shippings');
			if(!$this->mwarehouses_shippings->create_invoice_wh_shipping($ord_id, $wh_id)) return FALSE;
		}

		$inv_id = $this->sql_add_data(array(self::ID_ORD => $ord_id, 'invoices_number' => $invoice_number, 'invoices_status' => 'N', 'note' => $IPOST['note'], 'admin_note' => $IPOST['admin_note']))->sql_update_date()->sql_using_user()->sql_save(self::INV);
		$this->sql_add_data(array('orders_state' => 'P', 'status' => 1))->sql_update_date()->sql_save(self::ORD, $ord_id);
		$this->db->trans_complete();
		if($this->db->trans_status())
		{
			if(isset($IPOST['send_mail']) && $IPOST['send_mail'] == 1) $this->send_invoice_email($inv_id);
			return $inv_id;
		}
		return FALSE;
	}*/

	public function create_wh_sale_invoice($sale_id, $data = array())
	{
		$sale_id = intval($sale_id);
		if($sale_id <= 0) return FALSE;
		$this->load->model('warehouse/mwarehouses_sales');
		if($this->get_sale_invoice($sale_id)) return FALSE;

		$this->db->select("MAX(wh_invoice_number) AS wh_invoice_number")
				 ->from("`".self::WH_INV."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$invoices_number = $this->db->get()->row_array();
		$max = 1;
		if(count($invoices_number)>0)
		{
			$max = intval($invoices_number['wh_invoice_number']) + 1;
		}
		$invoice_number = str_repeat("0", 8-strlen($max)).($max);

		$inv_data[self::ID_WH_SALES] = $sale_id;
		$inv_data['wh_invoice_number'] = $invoice_number;
		$inv_data['wh_invoice_state'] = 'N';
		if(isset($data['wh_invoice_state'])) $inv_data['wh_invoice_state'] = $data['wh_invoice_state'];

		$inv_id = $this->sql_add_data($inv_data)->sql_update_date()->sql_using_user()->sql_save(self::WH_INV);
		return $inv_id;
	}

	public function change_sale_invoice_state($sale_id, $state)
	{
		if($invoice_data = $this->get_sale_invoice($sale_id))
		{
			$this->sql_add_data(array('wh_invoice_state' => $state))->sql_update_date()->sql_save(self::WH_INV, $invoice_data[self::ID_WH_INV]);
			return TRUE;
		}
		return FALSE;
	}

	/*public function edit_invoice($inv_id)
	{
		if(!$invoice = $this->get_invoice($inv_id)) return FALSE;
		if($invoice['invoices_status'] == 'C' || $invoice['invoices_status'] == 'CN') return FALSE;
		$IPOST = $this->input->post('invoice');
		$invoice_state = $this->get_invoice_state_collection();
		$update_array = array();

		if(isset($IPOST['invoices_status']) && isset($invoice_state[$IPOST['invoices_status']]))
		{
			if($IPOST['invoices_status'] == 'CN') return FALSE;
			$update_array['invoices_status'] = $IPOST['invoices_status'];
		}
		else
		{
			return FALSE;
		}
		if(isset($IPOST['admin_note']))
		{
			$update_array['admin_note'] = $IPOST['admin_note'];
		}

		$this->db->trans_start();
		$this->sql_add_data($update_array)->sql_update_date()->sql_save(self::INV, $inv_id);
		if($update_array['invoices_status'] == 'P')
		{
			$this->sql_add_data(array('orders_state' => 'I'))->sql_update_date()->sql_save(self::ORD, $invoice[self::ID_ORD]);
		}
		if($update_array['invoices_status'] == 'S')
		{
			$this->sql_add_data(array('orders_state' => 'IS'))->sql_update_date()->sql_save(self::ORD, $invoice[self::ID_ORD]);
		}
		if($update_array['invoices_status'] == 'C')
		{
			$this->sql_add_data(array('orders_state' => 'IC'))->sql_update_date()->sql_save(self::ORD, $invoice[self::ID_ORD]);
		}
		if($update_array['invoices_status'] == 'COD')
		{
			$this->sql_add_data(array('orders_state' => 'COD'))->sql_update_date()->sql_save(self::ORD, $invoice[self::ID_ORD]);
		}
		$this->db->trans_complete();

		if($this->db->trans_status())
		{
			return $inv_id;
		}
		return FALSE;
	}*/

	/*public function cancel_invoice($inv_id)
	{
		if(!$invoice = $this->get_invoice($inv_id)) return FALSE;
		if($invoice['invoices_status'] == 'C' || $invoice['invoices_status'] == 'CN') return FALSE;
		$this->load->model('sales/morders');
		if(!$this->morders->check_possibility_cancel_invoice($invoice[self::ID_ORD])) return FALSE;

		$this->db->trans_start();
		$this->load->model('warehouse/mwarehouses');
		$wh_id = $this->mwarehouses->get_shop_wh();
		if($wh_id)
		{
			$this->load->model('warehouse/mwarehouses_shippings');
			if(!$this->mwarehouses_shippings->cancel_invoice_wh_shipping($invoice[self::ID_ORD], $wh_id)) return FALSE;
		}

		$this->sql_add_data(array('invoices_status' => 'CN'))->sql_update_date()->sql_save(self::INV, $inv_id);
		$this->sql_add_data(array('orders_state' => 'N'))->sql_update_date()->sql_save(self::ORD, $invoice[self::ID_ORD]);
		$this->db->trans_complete();

		if($this->db->trans_status())
		{
			return $inv_id;
		}
		return FALSE;
	}*/

	/*public function send_invoice_email($inv_id)
	{
		if(!$invoice = $this->get_invoice($inv_id));

		$ord_id = $invoice[self::ID_ORD];
		$this->load->model('sales/morders');

		$order = $this->morders->get_order($ord_id);
		$order['payment_method'] = '';
		$order['shipping_method'] = '';
		if($order[Morders::ID_UPM] != NULL)
		{
			$order['payment_method'] = $order['pm_name'];
		}
		if($order[Morders::ID_USM] != NULL)
		{
			$order['shipping_method'] = $order['sm_name'];
		}

		$order_addresses = $this->morders->get_order_addresses($ord_id);
		$order_products = $this->morders->get_order_products($ord_id);
		$this->load->model('users/musers');
		$user = $this->musers->get_user();

		$this->load->model('langs/mlangs');
		$letter_lang = $this->mlangs->get_language($order[self::ID_LANGS]);

		$confirm_invoice_link = 'http://'.$user['domain'].'/ajax/sales/invoices/confirm_invoice/id/'.$invoice['invoices_number'].'/code/'.md5($invoice['id_m_orders_invoices'].'-'.$invoice['id_m_orders'].'-'.$invoice['create_date']).'/lang-'.$letter_lang['code'];;

		$letter_data_array = array('order' => $order, 'order_addresses' => $order_addresses, 'order_products' => $order_products, 'invoice' => $invoice, 'user' => $user, 'letter_lang' => $letter_lang, 'confirm_invoice_link' => $confirm_invoice_link);
		$this->load->model('sales/mpayment_methods');
		$payment_method_html = $this->mpayment_methods->get_payment_method_to_invoice_html($order['id_m_users_payment_methods'], $letter_data_array);

		$letter_data_array += array('payment_method_html' => $payment_method_html);

		$letter_html = $this->load->view('sales/invoices/letters/'.$letter_lang['language'].'/invoice', $letter_data_array, TRUE);

		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;
		$send_email = $order_addresses['B']['address_email'];

		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->from('no-reply@'.$user['domain'], $user['domain']);
		$this->email->to($send_email);
		$this->email->subject('New invoice #'.$invoice['invoices_number'].'.');
		$this->email->message($letter_html);
		$this->email->send();
		$this->email->clear();
		return TRUE;
	}*/

	public function isset_wh_invoice($inv_id)
	{
		$inv_id = intval($inv_id);
		$this->db->select("COUNT(*) AS COUNT")
				 ->from("`".self::INV."`")
				 ->where("`".self::ID_INV."`", $inv_id)->where("`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1) return TRUE;
		return FALSE;
	}
}
?>