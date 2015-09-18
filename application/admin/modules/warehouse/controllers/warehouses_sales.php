<?php
class Warehouses_sales extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Склад');
		$this->template->add_navigation('Склад');
		$this->template->add_title(' | Продажи');
		$this->template->add_navigation('Продажи', set_url('*/*'));
		$user = $this->musers->get_user();
		if($user['warehouse'] == 0)
		{
			$this->template->add_template('warehouse/wh_access', array(), '1');
			$this->template->render(TRUE);
		}
	}

	public function index()
	{
		$this->load->model('warehouse/mwarehouses_sales');
		$this->mwarehouses_sales->render_wh_sales_grid();
	}

	public function view_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && isset($URI['sale_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');

			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['sale_id']) && intval($URI['sale_id'])>0)
			{
				$sale_id = intval($URI['sale_id']);
				$this->load->model('mwarehouses_sales');
				if($view_sale_data = $this->mwarehouses_sales->view_sale($wh_id, $sale_id))
				{
					$this->template->add_title(' | '.$view_sale_data['sale']['warehouse_alias'].' | Продажа '.$view_sale_data['sale']['wh_sale_number']);
					$this->template->add_navigation($view_sale_data['sale']['warehouse_alias'])->add_navigation('Продажа '.$view_sale_data['sale']['wh_sale_number']);

					$this->template->add_js('jquery.print_element.min');
					$this->load->helper('warehouses_sales');
					helper_controller_warehouses_sales_form_view_sale($wh_id, $view_sale_data);
				}
				else
				{
					$this->messages->add_error_message('Server error!');
					$this->_redirect(set_url('*/*'));
				}
			}
			else
			{
				$this->messages->add_error_message('Параметры отсутствует! Просмотр невозможен!');
				$this->_redirect(set_url('*/*'));
			}
		}
	}

	public function prepare_add_sale()
	{
		$this->template->add_title(' | Добавить продажу | Выбор склада');
		$this->template->add_navigation('Добавить продажу')->add_navigation('Выбор склада');
		$this->load->model('warehouse/mwarehouses_sales');
		if(!$this->mwarehouses_sales->prepare_add_sale())
		{
			$this->messages->add_error_message('Склады не существует, добавление продажи не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function add_sale_select_wh()
	{
		if($wh_id = $this->input->post('wh_id'))
		{
			$this->load->model('warehouse/mwarehouses');
			$wh = $this->mwarehouses->get_wh_to_select();
			if(isset($wh[$wh_id]))
			{
				$this->_redirect(set_url('*/*/add_sale/wh_id/'.$wh_id));
			}
			else
			{
				$this->messages->add_error_message('Server error!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Server error!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function add_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$this->load->model('warehouse/mwarehouses');
			$wh = $this->mwarehouses->get_wh_to_select();

			if(isset($wh[$wh_id]))
			{
				$this->template->add_title(' | '.$wh[$wh_id].' | Добавить продажу');
				$this->template->add_navigation($wh[$wh_id])->add_navigation('Добавить продажу');

				$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
				$this->template->add_js('jquery.gbc_wh_sales', 'modules_js/warehouse/sales');

				$this->load->model('warehouse/mwarehouses_sales');
				$add_sale_data = $this->mwarehouses_sales->add_sale($wh_id);
				$this->load->helper('warehouses_sales');
				helper_controller_warehouses_sales_form_add_sale($wh_id, $add_sale_data);
			}
		}
	}

	public function save_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$sale_id = 0;
			if(isset($URI['sale_id']))
			{
				$sale_id = intval($URI['sale_id']);
			}
			$this->load->model('warehouse/mwarehouses_sales');
			if($id_sale = $this->mwarehouses_sales->save_sale($wh_id, $sale_id))
			{
				$this->messages->add_success_message('Продажа успешно добавлена!');
				$this->_redirect(set_url('*/*/view_sale/wh_id/'.$wh_id.'/sale_id/'.$id_sale));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при создании продажи!');
				$this->_redirect(set_url('*/*/add_sale/wh_id/'.$wh_id));
			}
		}
	}

	public function ajax_get_customers_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$sale_id = 0;
			if(isset($URI['sale_id']))
			{
				$sale_id = intval($URI['sale_id']);
			}
			$this->load->model('sales/mwarehouses_sales');
			$grid = $this->mwarehouses_sales->render_customers_grid($wh_id, $sale_id);
			echo $grid;
		}
	}

	/*public function ajax_set_sale_customer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cm_id']) && ($cm_id = intval($URI['cm_id'])) > 0)
		{
			$sale_id = 0;
			if(isset($URI['sale_id']))
			{
				$sale_id = intval($URI['sale_id']);
			}
			$this->load->model('sales/mwarehouses_sales');
			if($customer = $this->mwarehouses_sales->set_sale_customer($cm_id, $sale_id))
			{
				$ct_html = $this->load->view('sales/sales/view_customer/sale_customer', array('sale_customer' => $customer['customer'], 'sale_id' => $sale_id), TRUE);
				echo json_encode(array('ct_html' => $ct_html, 'ct_addresses' => $customer['addresses']));
			}
			else
			{
				$ct_html = $this->load->view('sales/sales/view_customer/sale_customer', array('sale_customer' => FALSE, 'sale_id' => $sale_id), TRUE);
				echo json_encode(array('ct_html' => $ct_html));
			}
		}
	}

	public function ajax_unset_sale_customer()
	{
		$sale_id = 0;
		if(isset($URI['sale_id']))
		{
			$sale_id = intval($URI['sale_id']);
		}
		$this->load->model('sales/mwarehouses_sales');
		$this->mwarehouses_sales->unset_sale_customer($sale_id);
		echo $this->load->view('sales/sales/view_customer/sale_customer', array('sale_customer' => FALSE, 'sale_id' => $sale_id));
	}*/

	public function ajax_get_wh_shop_products_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$sale_id = 0;
			if(isset($URI['sale_id']))
			{
				$sale_id = intval($URI['sale_id']);
			}
			$this->load->model('warehouse/mwarehouses_sales');
			$grid = $this->mwarehouses_sales->render_wh_shop_products_grid($wh_id, $sale_id);
			echo $grid;
		}
	}

	public function ajax_unset_products_temp_data()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$sale_id = 0;
			if(isset($URI['sale_id']))
			{
				$sale_id = intval($URI['sale_id']);
			}
			$this->load->model('warehouse/mwarehouses_sales');
			$this->mwarehouses_sales->unset_sale_products_temp($wh_id, $sale_id);
			$products_grid = $this->mwarehouses_sales->render_sale_products_grid($wh_id, $sale_id, TRUE);
			$sale_data = $this->mwarehouses_sales->get_temp_sale_products_sum($wh_id, $sale_id);
			$json = array('success' => 1, 'products' => $products_grid);
			if(is_array($sale_data)) $json += array('sale_data' => $sale_data);
			echo json_encode($json);
		}
	}

	public function ajax_get_view_wh_shop_product()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			if(isset($URI['pr_id']) && ($pr_id = intval($URI['pr_id']))>0)
			{
				$sale_id = 0;
				if(isset($URI['sale_id']))
				{
					$sale_id = intval($URI['sale_id']);
				}
				$this->load->model('warehouse/mwarehouses_sales');
				if($product_array = $this->mwarehouses_sales->get_view_product_data($wh_id, $sale_id, $pr_id))
				{
					echo json_encode(array('success' => 1, 'html' => $this->load->view('warehouse/sales/view_product/products_detail', array('PRD_array' => $product_array, 'PRD_ID' => $pr_id, 'wh_id' => $wh_id, 'sale_id' => $sale_id, 'PRD_block_id' => 'PRD_block'), TRUE).$this->load->view('warehouse/sales/view_product/products_detail_js', array(), TRUE).$this->load->view('catalogue/products/view_product/albums_detail_js', array(), TRUE)));
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
	}

	public function ajax_add_product_to_cart()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$this->load->model('warehouse/mwarehouses_sales');

			$sale_id = $this->input->post('sale_id');
			$data = $this->mwarehouses_sales->add_product_to_sale($wh_id, $sale_id);
			if($data['success'])
			{
				$products_grid = $this->mwarehouses_sales->render_sale_products_grid($wh_id, $sale_id, TRUE);
				$sale_data = $this->mwarehouses_sales->get_temp_sale_products_sum($wh_id, $sale_id);
				$json = array('success' => 1, 'products' => $products_grid, 'message' => '<p>'.$data['message'].'</p>');
				if(is_array($sale_data)) $json += array('sale_data' => $sale_data);
				echo json_encode($json);
			}
			else
			{
				echo json_encode(array('success' => 0, 'message' => '<p>'.$data['message'].'</p>'));
			}
		}
	}

	public function ajax_get_view_edit_product_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			if(isset($URI['sale_id']) && isset($URI['sale_pr_id']))
			{
				$sale_id = intval($URI['sale_id']);
				$sale_pr_id = $URI['sale_pr_id'];
				$this->load->model('warehouse/mwarehouses_sales');
				if($product_data = $this->mwarehouses_sales->get_sale_product_qty($wh_id, $sale_id, $sale_pr_id))
				{
					echo json_encode(array('success' => 1, 'html' => $this->load->view('warehouse/sales/view_sale_edit_product_qty', $product_data, TRUE)));
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
	}

	public function ajax_sale_edit_product_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$sale_id = 0;
			if(isset($URI['sale_id']))
			{
				$sale_id = intval($URI['sale_id']);
			}
			$sale_pr_id = $URI['sale_pr_id'];
			$this->load->model('warehouse/mwarehouses_sales');
			$data = $this->mwarehouses_sales->edit_sale_product_qty($wh_id, $sale_id, $sale_pr_id);
			if($data['success'])
			{
				$sale_data = $this->mwarehouses_sales->get_temp_sale_products_sum($wh_id, $sale_id);
				$products_grid = $this->mwarehouses_sales->render_sale_products_grid($wh_id, $sale_id);
				$data['message'] = $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$data['message'].'</p>'), TRUE);
				$json = array('success' => 1, 'products' => $products_grid, 'message' => $data['message']);
				if(is_array($sale_data)) $json += array('sale_data' => $sale_data);
				echo json_encode($json);
			}
			else
			{
				$data['message'] = $this->load->view('site_messages/site_messages', array('error_message' => '<p>'.$data['message'].'</p>'), TRUE);
				$json = array('success' => 0, 'message' => $data['message']);
				echo json_encode($json);
			}
		}
	}

	public function ajax_detele_product_from_cart()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			if(isset($URI['sale_pr_id']))
			{
				$sale_id = 0;
				if(isset($URI['sale_id']))
				{
					$sale_id = intval($URI['sale_id']);
				}

				$this->load->model('warehouse/mwarehouses_sales');
				$data = $this->mwarehouses_sales->delete_product_from_sale($wh_id, $sale_id, $URI['sale_pr_id']);
				if($data['success'])
				{
					$products_grid = $this->mwarehouses_sales->render_sale_products_grid($wh_id, $sale_id);
					$sale_data = $this->mwarehouses_sales->get_temp_sale_products_sum($wh_id, $sale_id);
					$data['message'] = $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$data['message'].'</p>'), TRUE);
					$json = array('success' => 1, 'products' => $products_grid, 'message' => $data['message']);
					if(is_array($sale_data)) $json += array('sale_data' => $sale_data);
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
	}

	public function ajax_set_sale_discount()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			if($discount = $this->input->post('discount'))
			{
				$sale_id = 0;
				if(isset($URI['sale_id']))
				{
					$sale_id = intval($URI['sale_id']);
				}
				$this->load->model('warehouse/mwarehouses_sales');
				$this->mwarehouses_sales->set_sale_discount($wh_id, $sale_id, $discount);
				$sale_data = $this->mwarehouses_sales->get_temp_sale_products_sum($wh_id, $sale_id);
				$json = array('sale_data' => $sale_data);
				echo json_encode($json);
				exit;
			}
		}
	}

	public function ajax_set_sale_currency()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			if($currency_id = $this->input->post('currency_id'))
			{
				$sale_id = 0;
				if(isset($URI['sale_id']))
				{
					$sale_id = intval($URI['sale_id']);
				}
				$this->load->model('warehouse/mwarehouses_sales');
				$currency = $this->mwarehouses_sales->set_sale_currency($wh_id, $sale_id, $currency_id);
				$sale_data = $this->mwarehouses_sales->get_temp_sale_products_sum($wh_id, $sale_id);
				$json = array('sale_data' => $sale_data);
				if($currency)
				{
					$json['sale_data']['currency_rate'] = $currency['rate'];
				}
				echo json_encode($json);
				exit;
			}
		}
	}

	public function ajax_set_sale_currency_rate()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			if($currency_rate = $this->input->post('currency_rate'))
			{
				$sale_id = 0;
				if(isset($URI['sale_id']))
				{
					$sale_id = intval($URI['sale_id']);
				}
				$this->load->model('warehouse/mwarehouses_sales');
				$currency = $this->mwarehouses_sales->set_sale_currency_rate($wh_id, $sale_id, $currency_rate);
				$sale_data = $this->mwarehouses_sales->get_temp_sale_products_sum($wh_id, $sale_id);
				$json = array('sale_data' => $sale_data);
				if($currency)
				{
					$json['sale_data']['currency_rate'] = $currency['rate'];
				}
				echo json_encode($json);
				exit;
			}
		}
	}
}