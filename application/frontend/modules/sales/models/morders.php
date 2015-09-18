<?php
class Morders extends AG_Model
{
	const ORD 				= 'm_orders';
	const ID_ORD 			= 'id_m_orders';
	const ORD_ADDR 			= 'm_orders_address';
	const ID_ORD_ADDR 		= 'id_m_orders_address';
	
	const ORD_PR 			= 'm_orders_products';
	const ID_ORD_PR 		= 'id_m_orders_products';
	const ORD_PR_ATTR 		= 'm_orders_products_attributes';
	const ID_ORD_PR_ATTR 	= 'id_m_orders_products_attributes';
	
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	const ID_PR_DESC 	= 'id_m_c_products_description';
	const PR_IMG 		= 'm_c_products_images';
	const ID_PR_IMG 	= 'id_m_c_products_images';
	
	const PR_PRICE 			= 'm_c_products_price';
	const ID_PR_PRICE 		= 'id_m_c_products_price';
	const PR_PRICE_DESC 	= 'm_c_products_price_description';
	const ID_PR_PRICE_DESC 	= 'id_m_c_products_price_description';
	
	const CUR 		= 'm_c_currency';
	const ID_CUR 	= 'id_m_c_currency';
	const UCUR 		= 'm_c_users_currency';
	
	const NATTRIBUTES 	= 'm_c_productsNattributes';
	
	const PR_ATTR 		= 'm_c_products_attributes';
	const ID_PR_ATTR 	= 'id_m_c_products_attributes';
	const PR_ATTR_DESC 	= 'm_c_products_attributes_description';
	
	const PR_ATTR_OPT 		= 'm_c_products_attributes_options';
	const ID_PR_ATTR_OPT 	= 'id_m_c_products_attributes_options';
	const PR_ATTR_OPT_DESC 	= 'm_c_products_attributes_options_description';
	
	const PM 		= 'm_payment_methods';
	const ID_PM 	= 'id_m_payment_methods';
	const UPM 		= 'm_users_payment_methods';
	const ID_UPM 	= 'id_m_users_payment_methods';
	const UPM_DESC 	= 'm_users_payment_methods_description';
	
	const SM 		= 'm_shipping_methods';
	const ID_SM 	= 'id_m_shipping_methods';
	const USM 		= 'm_users_shipping_methods';
	const ID_USM 	= 'id_m_users_shipping_methods';
	const USM_DESC	= 'm_users_shipping_methods_description';
	const SM_FIELDS		= 'm_shipping_methods_fields';
	const ID_SM_FIELDS	= 'id_m_shipping_methods_fields';
	
	const S_ALIAS 		= 'm_orders_settings_alias';
	const ID_S_ALIAS 	= 'id_m_orders_settings_alias';
	const S_VALUE 		= 'm_orders_settings_value';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function save($id = FALSE)
	{
		$this->load->model('catalogue/mcurrency');	
		$currency = $this->mcurrency->get_current_currency();
		$products_data_array = $this->get_order_products();
		if($products_data_array === FALSE) return FALSE;
		$products_data_array = $products_data_array['cart_products'];
		if(count($products_data_array) == 0) return FALSE;
		
		$this->db->trans_start();
		$order_data = $this->save_order_data($products_data_array);
		
		$letter_data = array();
		$letter_data['link'] = $this->router->build_url('ajax_lang', array('ajax' => 'sales/orders/confirm_order/id/'.$order_data['data']['orders_number'].'/code/'.md5($order_data['id'].'-'.$order_data['data']['orders_number'].'-'.$order_data['data']['total_qty']), 'lang' => $this->mlangs->lang_code));
		$letter_data['orders_number'] = $order_data['data']['orders_number'];
		$letter_data['total_qty'] = $order_data['data']['total_qty'];
		$letter_data['total'] = $order_data['data']['total'] * $currency['rate'].' '.$currency['name'];
		$letter_data['note'] = $order_data['data']['note'];
		
		$letter_data += $this->save_order_addresses_data($order_data);
		
		$letter_data += $this->save_order_products_data($order_data, $products_data_array);

		$this->db->trans_complete();
		if($this->db->trans_status())
		{	
			$this->cart->destroy();
			$this->send_customer_email_with_order($letter_data);
			$this->load->model('sales/msales_settings');
			$settings = $this->msales_settings->get_sales_settings();
			if($settings['mail_send_confirmed'] == 0) $this->send_admin_confirm_order_email($letter_data);
			
			return $order_data['id'];
		}
		return FALSE;
	}
	
	protected function save_order_data($products)
	{
		$currency = $this->mcurrency->get_current_currency();
		$base_currency = $this->mcurrency->get_base_currency();
		
			$query = $this->db->select("MAX(orders_number) AS orders_number")
					->from("`".self::ORD."`")
					->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
			$orders_number = $query->get()->row_array();
			$max = 1;
			if(count($orders_number)>0)
			{
				$max = intval($orders_number['orders_number']) + 1;
			}
			$max = str_repeat("0", 8-strlen($max)).($max);
		
		$total_price = 0;
		$total_qty = 0;
		foreach($products as $ms)
		{
			$total_price += $ms['products_prices']['original_total_price'];
			$total_qty += $ms['cart']['qty'] * $ms['products_prices']['real_qty'];
		}
		
		$data = array('orders_number' => $max, 'total_qty' => $total_qty, 'subtotal' => $total_price, 'total' => $total_price, 'base_id_m_c_currency' => $base_currency['id_m_c_currency'], 'base_currency_name' => $base_currency['name'], 'id_m_c_currency' => $currency['ID'], 'currency_name' => $currency['name'], 'currency_rate' => $currency['rate'], self::ID_LANGS => $this->mlangs->id_langs);
		$no_save_data = array();
		if($payment_method = $this->input->post('order_payment_method_select'))
		{
			$payment_method = intval($payment_method);
			$query = $this->db->select("A.`".self::ID_PM."`, A.`".self::ID_UPM."`, A.`alias` AS payment_method_alias, B.`name` AS payment_method_name")
					->from("`".self::UPM."` AS A")
					->join(	"`".self::UPM_DESC."` AS B",
							"B.`".self::ID_UPM."` = A.`".self::ID_UPM."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					->where("`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_UPM."`", $payment_method)->limit(1);
			$result = $query->get()->row_array();
			if(count($result)>0)
			{
				$data += array(self::ID_UPM => $result[self::ID_UPM], 'payment_method_alias' => $result['payment_method_alias']);
				$no_save_data['payment_method_name'] = $result['payment_method_name'];
				$no_save_data[self::ID_PM] = $result[self::ID_PM];
			}
		}
		
		if($shipping_method = $this->input->post('order_shipping_method_select'))
		{
			$shipping_method = intval($shipping_method);
			$query = $this->db->select("A.`".self::ID_SM."`, A.`".self::ID_USM."`, A.`alias` AS shipping_method_alias, B.`name` AS shipping_method_name")
					->from("`".self::USM."` AS A")
					->join(	"`".self::USM_DESC."` AS B",
							"B.`".self::ID_USM."` = A.`".self::ID_USM."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					->where("`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_USM."`", $shipping_method)->limit(1);
			$result = $query->get()->row_array();
			if(count($result)>0)
			{
				$data += array(self::ID_USM => $result[self::ID_USM], 'shipping_method_alias' => $result['shipping_method_alias']);
				$no_save_data['shipping_method_name'] = $result['shipping_method_name'];
				$no_save_data[self::ID_SM] = $result[self::ID_SM];
			}
		}
		
		$POST = $this->input->post('order');
		if(isset($POST['note']))
		{
			$data += array('note' => $POST['note']);
		}
		
		if($this->session->userdata('customer_id'))
		{
			$data += array('id_m_u_customers' => $this->session->userdata('customer_id'));
		}
		else
		{
			$C = $this->input->post('order_address');
			$C = $C['B']['address_email'];
			$this->load->model('customers/mcustomers');
			if($CS = $this->mcustomers->get_customer_by_email($C))
			{
				$data += array('id_m_u_customers' => $CS['id_m_u_customers']);
			}
		}

		$id = $this->sql_add_data($data)->sql_using_user()->sql_update_date()->sql_save(self::ORD);
		
		return array('id' => $id, 'data' => $data+$no_save_data);
	}
	
	protected function save_order_addresses_data($order_data)
	{
		$customer_addresses = $this->input->post('order_address');
		if(!is_array($customer_addresses)) return FALSE;
		$letter_data = array();
		$letter_data['payment_html'] = '';
		$letter_data['shipping_html'] = '';
		
		$letter_data['name'] = '';
		$letter_data['address_email'] = '';
		$letter_data['customer_email'] = '';
		
		if(isset($customer_addresses['B']))
		{
			$ms = $customer_addresses['B'];
			
			$data = $ms+array('type' => 'B', self::ID_ORD => $order_data['id']);
			$this->sql_add_data($data)->sql_save(self::ORD_ADDR);
			
			if(isset($order_data['data'][self::ID_UPM]))
			{
				$order_payment_method = $order_data['data']['payment_method_name'];
				$payment_address_data = $ms;
				$letter_data['payment_html'] = $this->load->view('sales/orders/letters/order_payment_data', array('order_payment_method' => $order_payment_method, 'payment_address_data' => $payment_address_data), TRUE);
			}
			else
			{
				$payment_address_data = $ms;
				$letter_data['payment_html'] = $this->load->view('sales/orders/letters/order_payment_data', array('payment_address_data' => $payment_address_data), TRUE);
			}
			$letter_data['name'] = $ms['name'];
			$letter_data['address_email'] = $ms['address_email'];
			$letter_data['customer_email'] = $ms['address_email'];			
		}
		else
		{
			$data = array('type' => 'B', self::ID_ORD => $order_data['id']);
			$this->sql_add_data($data)->sql_save(self::ORD_ADDR);
		}
		
		if(isset($customer_addresses['S']))
		{
			$ms = $customer_addresses['S'];
			
			$data = $ms+array('type' => 'S', self::ID_ORD => $order_data['id']);
			$this->sql_add_data($data)->sql_save(self::ORD_ADDR);
						
			if(isset($order_data['data'][self::ID_USM]))
			{
				$query = $this->db->select("A.`alias`, B.`field`")
						->from("`".self::SM."` AS A")
						->join(	"`".self::SM_FIELDS."` AS B",
								"B.`".self::ID_SM."` = A.`".self::ID_SM."`",
								"LEFT")
						->where("A.`".self::ID_SM."`", $order_data['data'][self::ID_SM])->order_by("B.`sort`");
				$result = $query->get()->result_array();
				$shipping_method_fields = $result;
				$order_shipping_method = $order_data['data']['shipping_method_name'];
				$shipping_address_data = $ms;
				$letter_data['shipping_html'] = $this->load->view('sales/orders/letters/order_shipping_data', array('order_shipping_method' => $order_shipping_method, 'shipping_method_fields' => $shipping_method_fields, 'shipping_address_data' => $shipping_address_data), TRUE);
			}
			else
			{
				$shipping_address_data = $ms;
				$letter_data['shipping_html'] = $this->load->view('sales/orders/letters/order_shipping_data', array('shipping_address_data' => $shipping_address_data), TRUE);
			}
		}
		else
		{
			$data = array('type' => 'S', self::ID_ORD => $order_data['id']);
			$this->sql_add_data($data)->sql_save(self::ORD_ADDR);
		}
		return $letter_data;
	}
	
	protected function save_order_products_data($order_data, $products)
	{
		foreach($products as $ms)
		{
			$data = array('id_m_orders' => $order_data['id'], 'id_m_c_products' => $ms['products']['ID'], 'sku' => $ms['products']['sku'], 'name' => $ms['products']['name'], 'price' => $ms['products_prices']['cart_price'], 'qty' => $ms['cart']['qty'], 'real_qty' => $ms['products_prices']['real_qty'], 'subtotal' => $ms['products_prices']['original_total_price'], 'total' => $ms['products_prices']['original_total_price'], 'price_alias' => $ms['products_prices']['price_alias'], 'price_name' => $ms['products_prices']['price_name']);
			$orp_id = $this->sql_add_data($data)->sql_save(self::ORD_PR);
			if(isset($ms['products_attributes']))
			{
				foreach($ms['products_attributes'] as $at)
				{
					$data = array('id_m_orders' => $order_data['id'], 'id_m_orders_products' => $orp_id, 'attributes_alias' => $at['a_alias'], 'attributes_name' => $at['a_name'], 'attributes_options_alias' => $at['o_alias'], 'attributes_options_name' => $at['o_name']);
					$this->sql_add_data($data)->sql_save(self::ORD_PR_ATTR);
				}
			}
			$letter_data['products'][] = $ms;
		}
		return $letter_data;
	}
	
	protected function get_order_products()
	{
		$this->load->model('sales/mcart');
		return $this->mcart->get_cart_products();
	}
	
	protected function send_customer_email_with_order($data)
	{
		if(trim($data['address_email']) != '')
		{
			$config['protocol'] = 'sendmail';
			$config['wordwrap'] = FALSE;
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['priority'] = 1;
			
			$data['site'] = $_SERVER['SERVER_NAME'];
			
			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->from('no-reply@'.$data['site'], $data['site']);
			$this->email->to($data['address_email']);
			$this->email->subject('You ORDER #'.$data['orders_number'].'!');
			$this->email->message($this->load->view('sales/orders/letters/'.$this->mlangs->language.'/order', array('data' => $data), TRUE));
			$this->email->send();
			$this->email->clear();
		}
	}
	
	protected function customer_confirm_order($id)
	{
		if(($order_data = $this->get_confirm_order_data($id)) != FALSE)
		{
			$order_data['products'] = $this->get_confirm_order_products($order_data);
			$this->sql_add_data(array('status' => 1))->sql_update_date()->sql_save(self::ORD, $id);
			$this->send_admin_confirm_order_email($order_data);
		}
		return FALSE;
	}
	
	protected function get_confirm_order_data($id)
	{
		$order_data = array();
		$query = $this->db->select("A.`".self::ID_ORD."`, A.`orders_number`, A.`".self::ID_USM."`, A.`".self::ID_UPM."`, A.`total_qty`, A.`total`, A.`currency_name`, A.`currency_rate`, A.`note` AS `onote`, B.*")
				->from("`".self::ORD."` AS A")
				->join("`".self::ORD_ADDR."` AS B",
						"B.`".self::ID_ORD."` = A.`".self::ID_ORD."`",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_ORD."`", $id)->limit(2);
		$result = $query->get()->result_array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				if(!isset($order_data[self::ID_ORD]))
				{
					$order_data = array(self::ID_ORD => $ms[self::ID_ORD], 'orders_number' => $ms['orders_number'], self::ID_USM => $ms[self::ID_USM], self::ID_UPM => $ms[self::ID_UPM], 'total_qty' => $ms['total_qty'], 'total' => $ms['total']*$ms['currency_rate'].' '.$ms['currency_name'], 'currency_name' => $ms['currency_name'], 'currency_rate' => $ms['currency_rate'], 'note' => $ms['onote']);
				}
				$order_data['address'][$ms['type']] = $ms;
			}
			$order_data = $this->get_confirm_order_payment_data($order_data);
			$order_data = $this->get_confirm_order_shipping_data($order_data);
			return $order_data;
		}
		return FALSE;
	}
	
	protected function get_confirm_order_products($order_data)
	{
		$products = array();
		$query = $this->db->select("A.`".self::ID_ORD_PR."` AS OR_PR_ID, A.`sku`, A.`name`, A.`price`, A.`qty`, A.`total`, A.`price_alias`, A.`price_name`,
				P.`".self::ID_PR."` AS ID, P.`url_key`,
				B.`attributes_alias`, B.`attributes_name`, B.`attributes_options_alias`, B.`attributes_options_name`")
				->from("`".self::ORD_PR."` AS A")
				->join("`".self::PR."` AS P",
						"P.`".self::ID_PR."` = A.`".self::ID_PR."`",
						"LEFT")	
				->join("`".self::ORD_PR_ATTR."` AS B",
						"B.`".self::ID_ORD_PR."` = A.`".self::ID_ORD_PR."`",
						"LEFT")
				->where("A.`".self::ID_ORD."`", $order_data[self::ID_ORD]);
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			if(!isset($products[$ms['OR_PR_ID']]))
			{
				$ms['products']['detail_url'] = $this->build_product_detail_url($ms);
				$ms['products']['name'] = $ms['name'];
				$ms['products']['sku'] = $ms['sku'];
				$ms['products_prices']['price_name'] = $ms['price_name'].' ';
				$ms['products_prices']['cart_price_rate_string'] = $ms['price']*$order_data['currency_rate'];
				$ms['cart']['qty'] = $ms['qty'];
				$ms['products_prices']['total_price_string'] = $ms['total']*$order_data['currency_rate'].' '.$order_data['currency_name'];
				$products[$ms['OR_PR_ID']] = $ms;
			}
			if($ms['attributes_alias'] != NULL)
			{
				$products[$ms['OR_PR_ID']]['products_attributes'][] = array('a_name' => $ms['attributes_name'], 'o_name' => $ms['attributes_options_name']);
			}
			else
			{
				$products[$ms['OR_PR_ID']]['products_attributes'] = array();
			}
		}
		return $products;
	}
	
	protected function build_product_detail_url($ms)
	{
		$product_url = $ms['ID'];
		if(strlen($ms['url_key']) > 2)
		{
			$product_url = trim($ms['url_key']);
		}
		$ms['detail_url'] = $this->router->build_url('product_lang', array('product_url' => $product_url, 'lang' => $this->mlangs->lang_code));
		return $ms['detail_url'];
	}
	
	protected function get_confirm_order_payment_data($order_data)
	{
		$letter_data['payment_html'] = '';
		if(isset($order_data[self::ID_UPM]))
		{
			$query = $this->db->select("A.`".self::ID_PM."`, A.`".self::ID_UPM."`, B.`name`")
					->from("`".self::UPM."` AS A")
					->join(	"`".self::UPM_DESC."` AS B",
							"B.`".self::ID_UPM."` = A.`".self::ID_UPM."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					->where("A.`".self::ID_UPM."`", $order_data[self::ID_UPM])->limit(1);
			$result = $query->get()->row_array();
			if(count($result) > 0)
			{
				$order_data['customer_email'] = $order_data['address']['B']['address_email'];
				$order_payment_method = $result['name'];
				$payment_address_data = $order_data['address']['B'];
				$order_data['payment_html'] = $this->load->view('sales/orders/letters/order_payment_data', array('order_payment_method' => $order_payment_method, 'payment_address_data' => $payment_address_data), TRUE);
			}
			else
			{
				$order_data['customer_email'] = $order_data['address']['B']['address_email'];
				$payment_address_data = $order_data['address']['B'];
				$order_data['payment_html'] = $this->load->view('sales/orders/letters/order_payment_data', array('payment_address_data' => $payment_address_data), TRUE);
			}
		}
		else
		{
			$order_data['customer_email'] = $order_data['address']['B']['address_email'];
			$payment_address_data = $order_data['address']['B'];
			$order_data['payment_html'] = $this->load->view('sales/orders/letters/order_payment_data', array('payment_address_data' => $payment_address_data), TRUE);
		}
		return $order_data;
	}
	
	protected function get_confirm_order_shipping_data($order_data)
	{
		if(isset($order_data[self::ID_USM]))
		{
			$query = $this->db->select("A.`".self::ID_SM."`, A.`".self::ID_USM."`, B.`name`")
					->from("`".self::USM."` AS A")
					->join(	"`".self::USM_DESC."` AS B",
							"B.`".self::ID_USM."` = A.`".self::ID_USM."` && B.`".self::ID_LANGS."` = '".$this->mlangs->id_langs."'",
							"LEFT")
					->where("A.`".self::ID_USM."`", $order_data[self::ID_USM])->limit(1);
			$result = $query->get()->row_array();
			if(count($result)>0)
			{
				$query = $this->db->select("A.`alias`, B.`field`")
						->from("`".self::SM."` AS A")
						->join(	"`".self::SM_FIELDS."` AS B",
								"B.`".self::ID_SM."` = A.`".self::ID_SM."`",
								"LEFT")
						->where("A.`".self::ID_SM."`", $result[self::ID_SM])->order_by("B.`sort`");
				$result_f = $query->get()->result_array();
				$shipping_method_fields = $result_f;
				$order_shipping_method = $result['name'];
				$shipping_address_data = $order_data['address']['S'];
				$order_data['shipping_html'] = $this->load->view('sales/orders/letters/order_shipping_data', array('order_shipping_method' => $order_shipping_method, 'shipping_method_fields' => $shipping_method_fields, 'shipping_address_data' => $shipping_address_data), TRUE);
			}
			else
			{
				$shipping_address_data = $order_data['address']['S'];
				$order_data['shipping_html'] = $this->load->view('sales/orders/letters/order_shipping_data', array('shipping_address_data' => $shipping_address_data), TRUE);
			}
		}
		else
		{
			$shipping_address_data = $order_data['address']['S'];
			$order_data['shipping_html'] = $this->load->view('sales/orders/letters/order_shipping_data', array('shipping_address_data' => $shipping_address_data), TRUE);
		}
		return $order_data;
	}
	
	public function confirm_order($number, $code)
	{
		$query = $this->db->select("`".self::ID_ORD."` AS ID, `orders_number`, `status`, `total`, `total_qty`")
				->from("`".self::ORD."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`orders_number`", $number)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			if($result['status'] == 1) return 1;
			if(md5($result['ID'].'-'.$result['orders_number'].'-'.$result['total_qty']) == $code)
			{
				$this->load->model('sales/msales_settings');
				$settings = $this->msales_settings->get_sales_settings();
				if($settings['mail_send_confirmed'] == 1)
				{
					$this->customer_confirm_order($result['ID']);
				}
				return 2;
			}
			else
			{
				return 0;
			}
		}
		return 0;
	}
	
	protected function send_customer_confirm_order_email($data)
	{
		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;
		
		$data['site'] = $_SERVER['SERVER_NAME'];
		
		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->from('no-reply@'.$data['site'], $data['site']);
		$this->email->to($data['customer_email']);
		$this->email->subject($data['site'].' - Order is confirmed!');
		$this->email->message($this->load->view('sales/orders/letters/'.$this->mlangs->language.'/confirm_order', $data['order'] + array('site' => $data['site']), TRUE));
		$this->email->send();
		$this->email->clear();
	}
	
	protected function send_admin_confirm_order_email($data)
	{
		$config['protocol'] = 'sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['priority'] = 1;
		
		$this->load->model('sales/msales_settings');
		$settings = $this->msales_settings->get_sales_settings();
		$data['admin_email'] = $settings['mail_new_order_email'];
		
		$data['site'] = $_SERVER['SERVER_NAME'];
		
		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->from($data['customer_email'], $data['site']);
		$this->email->to($data['admin_email']);
		$this->email->subject($data['site'].' - New order '.$data['orders_number'].'!');
		$this->email->message($this->load->view('sales/orders/letters/'.$this->mlangs->language.'/confirm_admin_order', array('data' => $data), TRUE));
		$this->email->send();
		$this->email->clear();
	}
}
?>