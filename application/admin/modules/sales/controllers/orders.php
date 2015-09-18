<?php
class Orders extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Продажи | Заказы');
		$this->template->add_navigation('Продажи')->add_navigation('Заказы',set_url('*/*/'));
		$this->load->library('cart');
	}
	
	public function index()
	{
		$this->load->model('morders');
		$this->morders->render_orders_collection_grid();
	}
	
	public function view()
	{
		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
		
		$this->template->add_title(' | Просмотр заказа');
		$this->template->add_navigation('Просмотр заказа');
		
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && intval($URI['ord_id'])>0)
		{
			$ord_id = intval($URI['ord_id']);
			$this->load->model('morders');
			if($this->morders->isset_order($ord_id))
			{
				$this->template->add_js('jquery.print_element.min');
				$this->template->add_js('jquery.gbc_orders', 'modules_js/sales');
				$this->morders->view_order($ord_id);
			}
			else
			{
				$this->messages->add_error_message('Данного заказа не существует! Просмотр невозможно!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Просмотр невозможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	function add()
	{
		$this->template->add_title(' | Добавление заказа');
		$this->template->add_navigation('Добавление заказа');

		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
		$this->template->add_js('jquery.gbc_orders', 'modules_js/sales');

		$this->load->model('sales/morders');
		$this->morders->add_order();
	}

	public function ajax_get_customers_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		$ord_id = 0;
		if(isset($URI['ord_id']))
		{
			$ord_id = intval($URI['ord_id']);
		}
		$this->load->model('sales/morders');
		$grid = $this->morders->render_customers_grid($ord_id);
		echo $grid;
	}

	public function ajax_set_order_customer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cm_id']) && ($cm_id = intval($URI['cm_id'])) > 0)
		{
			$ord_id = 0;
			if(isset($URI['ord_id']))
			{
				$ord_id = intval($URI['ord_id']);
			}
			$this->load->model('sales/morders');
			if($customer = $this->morders->set_order_customer($cm_id, $ord_id))
			{
				$ct_html = $this->load->view('sales/orders/view_customer/order_customer', array('order_customer' => $customer['customer'], 'ord_id' => $ord_id), TRUE);
				echo json_encode(array('ct_html' => $ct_html, 'ct_addresses' => $customer['addresses']));
			}
			else
			{
				$ct_html = $this->load->view('sales/orders/view_customer/order_customer', array('order_customer' => FALSE, 'ord_id' => $ord_id), TRUE);
				echo json_encode(array('ct_html' => $ct_html));
			}
		}
	}

	public function ajax_unset_order_customer()
	{
		$ord_id = 0;
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']))
		{
			$ord_id = intval($URI['ord_id']);
		}
		$this->load->model('sales/morders');
		$this->morders->unset_order_customer($ord_id);
		echo $this->load->view('sales/orders/view_customer/order_customer', array('order_customer' => FALSE, 'ord_id' => $ord_id));
	}

	public function ajax_get_shop_products_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		$ord_id = 0;
		if(isset($URI['ord_id']))
		{
			$ord_id = intval($URI['ord_id']);
		}
		$this->load->model('sales/morders');
		$grid = $this->morders->render_shop_products_grid($ord_id);
		echo $grid;
	}

	public function ajax_unset_products_temp_data()
	{
		$URI = $this->uri->uri_to_assoc(4);
		$ord_id = 0;
		if(isset($URI['ord_id']))
		{
			$ord_id = intval($URI['ord_id']);
		}
		$this->load->model('sales/morders');
		$this->morders->unset_order_products_temp($ord_id);
		$products_grid = $this->morders->render_order_products_grid($ord_id, TRUE);
		$order_data = $this->morders->get_temp_order_products_sum($ord_id);
		$json = array('success' => 1, 'products' => $products_grid);
		if(is_array($order_data)) $json += array('order_data' => $order_data);
		echo json_encode($json);
	}

	public function ajax_get_view_shop_product()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['pr_id']) && ($pr_id = intval($URI['pr_id']))>0)
		{
			$ord_id = 0;
			if(isset($URI['ord_id']))
			{
				$ord_id = intval($URI['ord_id']);
			}
			$this->load->model('sales/morders');
			if($product_array = $this->morders->get_view_product_data($pr_id, $ord_id))
			{
				echo json_encode(array('success' => 1, 'html' => $this->load->view('sales/orders/view_product/products_detail', array('PRD_array' => $product_array, 'PRD_ID' => $pr_id, 'ORD_ID' => $ord_id, 'PRD_block_id' => 'PRD_block'), TRUE).$this->load->view('sales/orders/view_product/products_detail_js', array(), TRUE).$this->load->view('catalogue/products/view_product/albums_detail_js', array(), TRUE)));
			}
			else
			{
				echo json_encode(array('success' => 0));
			}
		}
		else
		{
			echo json_encode(array('success' => 0));
		}
	}

	public function ajax_add_product_to_cart()
	{
		$URI = $this->uri->uri_to_assoc(4);
		$this->load->model('sales/morders');

		$ord_id = $this->input->post('order_id');
		$data = $this->morders->add_product_to_order($ord_id);
		if($data['success'])
		{
			$products_grid = $this->morders->render_order_products_grid($ord_id, TRUE);
			$order_data = $this->morders->get_temp_order_products_sum($ord_id);
			$json = array('success' => 1, 'products' => $products_grid, 'message' => '<p>'.$data['message'].'</p>');
			if(is_array($order_data)) $json += array('order_data' => $order_data);
			echo json_encode($json);
		}
		else
		{
			echo json_encode(array('success' => 0, 'message' => '<p>'.$data['message'].'</p>'));
		}
	}

	public function ajax_get_view_edit_product_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && isset($URI['ord_pr_id']))
		{
			$ord_id = intval($URI['ord_id']);
			$ord_pr_id = $URI['ord_pr_id'];
			$this->load->model('sales/morders');
			if($product_data = $this->morders->get_order_product_qty($ord_id, $ord_pr_id))
			{
				echo json_encode(array('success' => 1, 'html' => $this->load->view('sales/orders/view_order_edit_product_qty', $product_data, TRUE)));
			}
			else
			{
				echo json_encode(array('success' => 0));
			}
		}
		else
		{
			echo json_encode(array('success' => 0));
		}
	}
	
	public function ajax_order_edit_product_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		$ord_id = 0;
		if(isset($URI['ord_id']))
		{
			$ord_id = intval($URI['ord_id']);
		}
		$ord_pr_id = $URI['ord_pr_id'];
		$this->load->model('sales/morders');
		$data = $this->morders->edit_order_product_qty($ord_pr_id, $ord_id);
		if($data['success'])
		{
			$order_data = $this->morders->get_temp_order_products_sum($ord_id);
			$products_grid = $this->morders->render_order_products_grid($ord_id, TRUE);
			$data['message'] = $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$data['message'].'</p>'), TRUE);
			$json = array('success' => 1, 'products' => $products_grid, 'message' => $data['message']);
			if(is_array($order_data)) $json += array('order_data' => $order_data);
			echo json_encode($json);
		}
		else
		{
			$data['message'] = $this->load->view('site_messages/site_messages', array('error_message' => '<p>'.$data['message'].'</p>'), TRUE);
			$json = array('success' => 0, 'message' => $data['message']);
			echo json_encode($json);
		}
	}
	
	public function ajax_detele_product_from_cart()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_pr_id']))
		{
			$ord_id = 0;
			if(isset($URI['ord_id']))
			{
				$ord_id = intval($URI['ord_id']);
			}
			
			$this->load->model('sales/morders');
			$data = $this->morders->delete_product_from_order($URI['ord_pr_id'], $ord_id);
			if($data['success'])
			{
				$products_grid = $this->morders->render_order_products_grid($ord_id, TRUE);
				$order_data = $this->morders->get_temp_order_products_sum($ord_id);
				$data['message'] = $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$data['message'].'</p>'), TRUE);
				$json = array('success' => 1, 'products' => $products_grid, 'message' => $data['message']);
				if(is_array($order_data)) $json += array('order_data' => $order_data);
				echo json_encode($json);
				exit;
			}
			else
			{
				$data['message'] = $this->load->view('site_messages/site_messages', array('error_message' => '<p>'.$data['message'].'</p>'), TRUE);
				echo json_encode(array('success' => 0, 'message' => $data['message']));
			}
		}
		else
		{
			$data['message'] = $this->load->view('site_messages/site_messages', array('error_message' => '<p>Server error! Try later!</p>'), TRUE);
			echo json_encode(array('success' => 0, 'message' => $data['message']));
		}
	}

	public function ajax_set_order_discount()
	{
		if($discount = $this->input->post('discount'))
		{
			$URI = $this->uri->uri_to_assoc(4);
			$ord_id = 0;
			if(isset($URI['ord_id']))
			{
				$ord_id = intval($URI['ord_id']);
			}
			$this->load->model('sales/morders');
			$this->morders->set_order_discount($discount, $ord_id);
			$order_data = $this->morders->get_temp_order_products_sum($ord_id);
			$json = array('order_data' => $order_data);
			echo json_encode($json);
			exit;
		}
	}

	public function ajax_set_order_currency()
	{
		if($currency_id = $this->input->post('currency_id'))
		{
			$URI = $this->uri->uri_to_assoc(4);
			$ord_id = 0;
			if(isset($URI['ord_id']))
			{
				$ord_id = intval($URI['ord_id']);
			}
			$this->load->model('sales/morders');
			$currency = $this->morders->set_order_currency($currency_id, $ord_id);
			$order_data = $this->morders->get_temp_order_products_sum($ord_id);
			$json = array('order_data' => $order_data);
			if($currency)
			{
				$json['order_data']['currency_rate'] = $currency['rate'];
			}
			echo json_encode($json);
			exit;
		}
	}

	public function ajax_set_order_currency_rate()
	{
		if($currency_rate = $this->input->post('currency_rate'))
		{
			$URI = $this->uri->uri_to_assoc(4);
			$ord_id = 0;
			if(isset($URI['ord_id']))
			{
				$ord_id = intval($URI['ord_id']);
			}
			$this->load->model('sales/morders');
			$currency = $this->morders->set_order_currency_rate($currency_rate, $ord_id);
			$order_data = $this->morders->get_temp_order_products_sum($ord_id);
			$json = array('order_data' => $order_data);
			if($currency)
			{
				$json['order_data']['currency_rate'] = $currency['rate'];
			}
			echo json_encode($json);
			exit;
		}
	}

	public function get_products_grid()
	{
		$this->load->model('morders');
		$grid_html = $this->morders->get_products_grid();
		$this->template->is_ajax(TRUE);
		$this->template->add_template('orders/orders_products_add_form', array('html' => $grid_html), 'orders_add_products_grid');
	}
	public function show_product_to_cart()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			$this->load->model('morders');
			$this->morders->get_view_products_to_cart($ID);
		}
	}
	
	public function change_currency()
	{
		if($id_m_c_currency = $this->input->post('id_m_c_currency'))
		{
			$id_m_c_currency = intval($id_m_c_currency);
			if($id_m_c_currency>0)
			{
				$this->load->model('morders');
				$this->morders->change_currency($id_m_c_currency);
			}	
		}
	}
	
	public function view_save()
	{
		if(isset($_POST))
		{
			$this->load->model('morders');
			$URI = $this->uri->uri_to_assoc(4);
			if (isset($URI['ord_id']) && ($ID = intval($URI['ord_id']))>0)
			{
				if($this->morders->save($ID))
				{
					$this->messages->add_success_message('Заказ успешно отредактирован!');
					$this->_redirect(set_url('*/*/view/ord_id/'.$ID));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании заказа!');
					//$this->_redirect(set_url('*/*/'));
				}
				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/view/ord_id/'.$ID));
				}
			}
		}
	}
	
	public function cancel_order()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->load->model('sales/morders');
			if($this->morders->cancel_order($ord_id))
			{
				$this->messages->add_success_message('Заказ успешно отменен!');
				$this->_redirect(set_url('*/*/view/ord_id/'.$ord_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при отмене заказа!');
				$this->_redirect(set_url('*/*/view/ord_id/'.$ord_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Парамерт ID отсудствует. Действие не возмоно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function order_COD_paid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->load->model('sales/morders');
			if($this->morders->order_COD_paid($ord_id))
			{
				$this->messages->add_success_message('Заказ успешно получил статус завершенного!');
				$this->_redirect(set_url('*/*/view/ord_id/'.$ord_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки!');
				$this->_redirect(set_url('*/*/view/ord_id/'.$ord_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Парамерт ID отсудствует. Действие не возмоно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function save()
	{
		$this->load->model('morders');
		if($ord_id = $this->morders->save())
		{
			$this->messages->add_success_message('Заказ успешно добавлен!');
			$this->_redirect(set_url('*/*/view/ord_id/'.$ord_id));
		}
		else
		{
			$this->_redirect(set_url('*/*/add'));
		}
	}
	
	public function add_product_to_catr()
	{
		$this->load->model('morders');
		if($this->morders->add_product_to_catr())
		{
			echo "Продукт добавлен в корзину!";
		}
	}
	
	public function delete_cart_products()
	{
		$POST = $this->input->post('cart_products');
		if($POST)
		{
			$this->load->model('morders');
			echo $this->morders->delete_cart_products($POST);
		}	
	}
	
	public function get_customers_addresses()
	{
		$id = $this->input->post('id');
		if($id && ($id = intval($id)) > 0)
		{
			$this->load->model('morders');
			echo $this->morders->get_customers_addresses($id);
		}
	}
	
	public function get_ajax_orders_products()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']))
		{
			$this->load->model('sales/morders');
			echo $this->morders->get_order_products_grid($URI['ord_id']);
		}
	}

	public function print_order()
	{
		$this->template->add_title(' - Печать заказа');
		$this->template->add_navigation('Печать заказа');

		$this->template->add_js('jquery.print_element.min');

		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->load->model('morders');
			if($data = $this->morders->get_print_order_data($ord_id))
			{
				$this->template->add_template('sales/orders/view_order_print', $data);
			}
			else
			{
				$this->messages->add_error_message('Заказ с данным ID отсутствует! Просмотр невозможен!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Просмотр невозможен!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	public function ajax_show_products_with_photo()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->load->model('sales/morders');
			if($this->morders->isset_order($ord_id))
			{
				$prod_array = $this->morders->get_order_products_with_photo($ord_id);
				if(count($prod_array) > 0)
				{
					echo json_encode(array('success' => 1, 'html' => $this->load->view('sales/orders/view_products_with_photo', array('prod_array' => $prod_array), TRUE)));
				}
				else
				{
					json_encode(array('success' => 0));
				}
			}
			else
			{
				json_encode(array('success' => 0));
			}
		}
		else echo json_encode(array('success' => 0));
	}
}
?>