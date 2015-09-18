<?php
class Mshippings extends AG_Model
{
	const ORD 		= 'm_orders';
	const ID_ORD 	= 'id_m_orders';
	const ORD_PR 	= 'm_orders_products';
	const ID_ORD_PR = 'id_m_orders_products';
	const INV 		= 'm_orders_invoices';
	const ID_INV 	= 'id_m_orders_invoices';
	const SHP 		= 'm_orders_shippings';
	const ID_SHP 	= 'id_m_orders_shippings';

	public static function get_shipping_state_collection()
	{
		return array('N' => 'Новый', 'C' => 'Завершен', 'CN' => 'Отменен', 'CM' => 'Возврат');
	}

	public static function get_shipping_state_name($key)
	{
		$state_collection = self::get_shipping_state_collection();
		if(isset($state_collection[$key]))
		{
			return $state_collection[$key];
		}
		return FALSE;
	}

	public function render_shipping_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("shippings_grid", array('sort' => 'shippings_number', 'desc' => 'DESC'));
		
		$this->grid->db
				->select("A.`".self::ID_SHP."` AS ID, A.`shippings_number`, A.`shippings_status`, A.`create_date`, A.`update_date`, B.`orders_number`, CONCAT(B.`total`, ' ', B.`base_currency_name`) AS total, C.`invoices_number`")
				->from("`".self::SHP."` AS A")
				->join(	"`".self::ORD."` AS B",
						"B.`".self::ID_ORD."` = A.`".self::ID_ORD."`",
						"LEFT")
				->join(	"`".self::INV."` AS C",
						"C.`".self::ID_ORD."` = A.`".self::ID_ORD."` && C.`invoices_status` <> 'CN'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users);
		$this->load->helper("shippings");
		helper_shippings_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data("shippings_status", self::get_shipping_state_collection());
		$this->grid->render_grid();
	}
	
	public function view_shipping($shp_id)
	{
		$shipping_data = $this->get_shipping_data($shp_id);
		$this->load->helper("shippings");
		helper_shipping_view($shipping_data);
		return TRUE;
	}

	public function get_shipping_data($shp_id)
	{
		if(!$shipping = $this->get_shipping($shp_id)) return FALSE;
		$this->load->model('sales/morders');
		$order_data = $this->morders->get_order_data($shipping[self::ID_ORD]);
		$order_data['order_products_grid'] = $this->morders->render_not_edited_order_products_grid($shipping[self::ID_ORD]);
		$shipping_data = $order_data;
		$shipping_data['shipping'] = $shipping;
		return $shipping_data;
	}

	public function get_shipping($shp_id)
	{
		$this->db->select("*")
				 ->from("`".self::SHP."`")
				 ->where("`".self::ID_SHP."`", $shp_id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($shipping = $this->db->get()->row_array()) == 0) return FALSE;
		return $shipping;
	}

	public function get_order_shipping($id_ord)
	{
		$this->db->select("*")
			->from("`".self::SHP."`")
			->where("`".self::ID_ORD."`", $id_ord)->where("`".self::ID_USERS."`", $this->id_users)
			->where("shippings_status <>", 'CN')->limit(1);
		if(count($result = $this->db->get()->row_array()) == 0) return FALSE;
		return $result;
	}
	
	public function create_shipping($ord_id)
	{
		if($this->get_order_shipping($ord_id)) return FALSE;
		$this->load->model('sales/morders');
		$order_data = $this->morders->get_order_data($ord_id);
		$order_data['order_products_grid'] = $this->morders->render_not_edited_order_products_grid($ord_id);
		if($order_data['invoice'] && ($order_data['invoice']['invoices_status'] == 'C' || $order_data['invoice']['invoices_status'] == 'COD'))
		{
			$this->load->helper("shippings");
			helper_shippings_create_shipping($ord_id, $order_data);
			return TRUE;
		}
		return FALSE;
	}
	
	public function save_shipping($ord_id)
	{
		$ord_id = intval($ord_id);
		if($ord_id <= 0) return FALSE;
		$this->load->model('sales/morders');
		if(!$this->morders->isset_order($ord_id)) return FALSE;
		
		$this->load->model('sales/minvoices');
		if(($data['invoice'] = $this->minvoices->get_order_invoice($ord_id)) === FALSE) return FALSE;
		if($data['invoice']['invoices_status'] == 'C' || $data['invoice']['invoices_status'] == 'COD')
		{
			
			if($this->get_order_shipping($ord_id)) return FALSE;

			$SPOST = $this->input->post('shipping');

			$this->db->select("MAX(shippings_number) AS shippings_number")
				->from("`".self::SHP."`")
				->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
			$shippings_number = $this->db->get()->row_array();
			$max = 1;
			if(count($shippings_number) > 0)
			{
				$max = intval($shippings_number['shippings_number']) + 1;
			}
			$shippings_number = str_repeat("0", 8-strlen($max)).($max);
			
			$this->db->trans_start();
			$shp_id = $this->sql_add_data(array(self::ID_ORD => $ord_id, 'shippings_number' => $shippings_number, 'shippings_status' => 'N', 'admin_note' => $SPOST['admin_note']))->sql_update_date()->sql_using_user()->sql_save(self::SHP);
			$this->load->model('warehouse/mwarehouses_sales');
			$this->mwarehouses_sales->create_order_sale_shipping($ord_id);

			if($data['invoice']['invoices_status'] == 'COD')
			{
				$this->sql_add_data(array('orders_state' => 'COD_S'))->sql_update_date()->sql_save(self::ORD, $ord_id);
				$this->mwarehouses_sales->change_order_sale_state($ord_id, 'COD_S');
			}
			else
			{
				$this->sql_add_data(array('orders_state' => 'S'))->sql_update_date()->sql_save(self::ORD, $ord_id);
				$this->mwarehouses_sales->change_order_sale_state($ord_id, 'S');
			}
			
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return $shp_id;
			}
		}
		return FALSE;
	}
	
	public function edit_shipping($shp_id)
	{
		if(!$shipping = $this->get_shipping($shp_id)) return FALSE;
		if($shipping['shippings_status'] == 'C' || $shipping['shippings_status'] == 'CN') return FALSE;
		$this->load->model('sales/minvoices');
		$invoice = $this->minvoices->get_order_invoice($shipping[self::ID_ORD]);
		$SPOST = $this->input->post('shipping');
		$shipping_state = $this->get_shipping_state_collection();
		$update_array = array();
		if(isset($SPOST['shippings_status']) && isset($shipping_state[$SPOST['shippings_status']]))
		{
			if($SPOST['shippings_status'] == 'CN') return FALSE;
			$update_array['shippings_status'] = $SPOST['shippings_status'];
		}
		else
		{
			return FALSE;
		}
		if(isset($SPOST['admin_note']))
		{
			$update_array['admin_note'] = $SPOST['admin_note'];
		}
		if(isset($SPOST['note']))
		{
			$update_array['note'] = $SPOST['note'];
		}
		
		$this->db->trans_start();
		$this->sql_add_data($update_array)->sql_update_date()->sql_save(self::SHP, $shp_id);
		$this->load->model('warehouse/mwarehouses_sales');
		$this->mwarehouses_sales->change_order_sale_shipping_state($shipping[self::ID_ORD], $update_array['shippings_status']);


		if($update_array['shippings_status'] == 'C' && $invoice['invoices_status'] == 'COD')
		{
			$this->load->model('sales/morders');
			$this->morders->complete_order($shipping[self::ID_ORD], TRUE);

		}
		else if($update_array['shippings_status'] == 'C')
		{
			$this->load->model('sales/morders');
			$this->morders->complete_order($shipping[self::ID_ORD]);
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status())
		{
			if($update_array['shippings_status'] == 'C' && isset($SPOST['send_mail']) && $SPOST['send_mail'] == 1) $this->send_shipping_email($shp_id);
			return $shp_id;
		}
		return FALSE;
	}
	
	public function cancel_shipping($shp_id)
	{
		if(!$shipping = $this->get_shipping($shp_id)) return FALSE;
		if($shipping['shippings_status'] != 'N') return FALSE;
		$this->load->model('sales/morders');
		$order = $this->morders->get_order($shipping[self::ID_ORD]);
		$order_state = $order['orders_state'];
		
		$this->db->trans_start();
		$this->load->model('warehouse/mwarehouses_sales');
		if($order_state == 'COD_S')
		{
			$this->sql_add_data(array('shippings_status' => 'CN'))->sql_update_date()->sql_save(self::SHP, $shp_id);
			$this->sql_add_data(array('orders_state' => 'COD'))->sql_update_date()->sql_save(self::ORD, $shipping[self::ID_ORD]);
			$this->mwarehouses_sales->change_order_sale_shipping_state($shipping[self::ID_ORD], 'CN');
			$this->mwarehouses_sales->change_order_sale_state($shipping[self::ID_ORD], 'COD');
		}
		else
		{
			$this->sql_add_data(array('shippings_status' => 'CN'))->sql_update_date()->sql_save(self::SHP, $shp_id);
			$this->sql_add_data(array('orders_state' => 'IC'))->sql_update_date()->sql_save(self::ORD, $shipping[self::ID_ORD]);
			$this->mwarehouses_sales->change_order_sale_shipping_state($shipping[self::ID_ORD], 'CN');
			$this->mwarehouses_sales->change_order_sale_state($shipping[self::ID_ORD], 'IC');
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status())
		{
			return $shp_id;
		}
		return FALSE;
	}
	
	public function send_shipping_email($shp_id)
	{
		if(!$shipping = $this->get_shipping($shp_id));

		$ord_id = $shipping[self::ID_ORD];
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
		if($order_addresses['S']['address_email'] != '')
		{
			$this->load->model('users/musers');
			$user = $this->musers->get_user();
			
			$this->load->model('langs/mlangs');
			$letter_lang = $this->mlangs->get_language($order[self::ID_LANGS]);
			
			$letter_data_array = array('order' => $order, 'order_addresses' => $order_addresses, 'shipping' => $shipping, 'user' => $user, 'letter_lang' => $letter_lang);
			
			$letter_html = $this->load->view('sales/shippings/letters/'.$letter_lang['language'].'/shipping', $letter_data_array, TRUE);
			
			$config['protocol'] = 'sendmail';
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['priority'] = 1;
			$send_email = $order_addresses['S']['address_email'];
			
			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->from('no-reply@'.$user['domain'], $user['domain']);
			$this->email->to($send_email);
			$this->email->subject('New shipping #'.$shipping['shippings_number'].'.');
			$this->email->message($letter_html);
			$this->email->send();
			$this->email->clear();
		}
		return TRUE;
	}
	
	public function isset_shipping($shp_id)
	{
		$shp_id = intval($shp_id);
		$this->db->select("COUNT(*) AS COUNT")
			->from("`".self::SHP."`")
			->where("`".self::ID_SHP."`", $shp_id)->where("`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1) return TRUE;
		return FALSE;
	}
	
	public function isset_order_shipping($ord_id)
	{
		$ord_id = intval($ord_id);
		$this->db->select("COUNT(*) AS COUNT")
			->from("`".self::SHP."`")
			->where("`".self::ID_ORD."`", $ord_id)->where("`".self::ID_USERS."`", $this->id_users)->where("`shippings_status` <>", 'CN');
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1) return TRUE;
		return FALSE;
	}
}
?>