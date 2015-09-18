<?php
class Orders extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function create_order_form()
	{
		$this->mlangs->load_language_file('modules/cart');
		$this->mlangs->load_language_file('modules/orders_customers');
		$this->mlangs->load_language_file('modules/products');
		$this->load->model('sales/mcart');
		$this->load->model('sales/mpayment_methods');
		$this->load->model('sales/mshipping_methods');
		$this->load->model('sales/msales_settings');
		$data['products'] = $this->mcart->get_cart_products();
		
		$this->load->model('customers/mcustomers');
		$data['order_customer'] = $this->mcustomers->get_order_customer();
		
		$data += $this->mpayment_methods->get_form_payment_method_data();
		$data += $this->mshipping_methods->get_form_shipping_method_data();
		$data['sales_settings'] = $this->msales_settings->get_sales_settings();
		$this->template->add_template_ajax('sales/orders/create_order', array('order_data' => $data));
		$this->template->add_template_ajax('sales/orders/create_order_js', array('order_data' => $data));
		$this->template->add_view_to_template('customers_form', 'sales/orders/create_order_customer', array());
		$this->template->add_view_to_template('payment_methods_form', 'sales/orders/payment_methods/default_form', array());
		$this->template->add_view_to_template('payment_methods_form', 'sales/orders/payment_methods/default_form_js', array());
		$this->template->add_view_to_template('shipping_methods_form', 'sales/orders/shipping_methods/default_form', array());
		$this->template->add_view_to_template('shipping_methods_form', 'sales/orders/shipping_methods/default_form_js', array());
		//$this->template->add_view_to_template('sales_settings_form', 'sales/orders/sales_settings/default_form', array());
		//$this->template->add_view_to_template('sales_settings__form', 'sales/orders/payment_methods/default_form_js', array());
		$this->template->add_view_to_template('order_products', 'sales/orders/create_order_products', array());
		
	}
	
	public function save()
	{
		$this->mlangs->load_language_file('modules/orders_customers');
		$this->mlangs->load_language_file('modules/products');
		if(isset($_POST) && count($_POST)>0)
		{
			$this->load->model('sales/morders');
			$success = $this->morders->save();
			if($success)
			{
				$this->mlangs->load_language_file('modules/cart');
				$this->load->model('sales/mcart');
				$sdata = $this->mcart->get_cart_short();
				$cart_block = $this->load->view('sales/cart/cart_block', $sdata + array('ajax' => true), TRUE);
				$cart_min_html = $this->load->view('sales/cart/cart_min_block', $sdata + array('ajax' => true), TRUE);
				echo json_encode(array('status' => 1, 'success' => $this->lang->line('c_o_success_create_order'), 'cart_html' => $cart_block, 'cart_min_html' => $cart_min_html));
			}
			else
			{
				echo json_encode(array('status' => 0, 'errors' => '<p>Create order error. Try later!</p>'));
			}
		}
	}
	
	public function confirm_order()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		$this->mlangs->load_language_file('modules/orders_customers');
		if(isset($URI['id']) && isset($URI['code']) && strlen($URI['id']) == 8)
		{
			$this->load->model('sales/morders');
			$result = $this->morders->confirm_order($URI['id'], $URI['code']);
			if($result == 2)
			{ 
				echo "<h1 align='center' style='color:#006600'>".$this->lang->line('c_o_success_confirm_order')."</h1>";
				echo "<script>window.setTimeout(\"document.location.href = '".$this->router->build_url('index', array('lang' => $this->mlangs->lang_code))."'\", 4000);</script>";
			}
			else if($result == 1) echo "<h1 align='center' style='color:#990000'>".$this->lang->line('c_o_error_already_confirmed_order')."</h1>";
			else echo "<h1 align='center' style='color:#990000'>".$this->lang->line('c_o_error_confirm_order')."</h1>";
			
		}
		else
		{
			echo "<h1 align='center' style='color:#990000'>Wrong order confirmation data!</h1>";
		}
	}
	
	public function ajax_edit_cart_item()
	{
		$this->mlangs->load_language_file('modules/products');
		$this->mlangs->load_language_file('modules/cart');
		$this->load->model('sales/mcart');
		$this->load->model('langs/mlangs');
		$data = $this->mcart->edit_item();
		
		if($data['success'] == TRUE)
		{
			$cart_edit_data = $this->mcart->get_cart_products();
			if($cart_edit_data && count($cart_edit_data['cart_products']) > 0)
			{
				$cart_edit_html = $this->load->view('sales/orders/create_order_products', array('order_data' => array('products' => $cart_edit_data)), TRUE);
			}
			else
			{
				$cart_edit_html = FALSE;
			}
			
			$cart_data = $this->mcart->get_cart_short();
			
			$cart_html = $this->load->view('sales/cart/cart_block', $cart_data + array('ajax' => true), TRUE);
			$cart_min_html = $this->load->view('sales/cart/cart_min_block', $cart_data + array('ajax' => true), TRUE);
			
			$site_messages = '<p>'.$data['message'].'</p>';
			$delay = 4000;
			
			$json_data = array('success' => 1, 'cart_edit_html' => $cart_edit_html, 'cart_html' => $cart_html, 'cart_min_html' => $cart_min_html, 'site_messages' => $site_messages, 'delay' => $delay);
			echo json_encode($json_data);
		}
		else
		{
			$site_messages = '<p>'.$data['message'].'</p>';
			$delay = $data['delay'];
			$json_data = array('success' => 0, 'site_messages' => $site_messages, 'delay' => $delay);
			if(isset($data['available_qty']))
			{
				$json_data += array('available_qty' => $data['available_qty']);
			}
			echo json_encode($json_data);
		}
	}
	
	public function ajax_delete_cart_item()
	{
		$this->mlangs->load_language_file('modules/products');
		$this->mlangs->load_language_file('modules/cart');
		$this->load->model('sales/mcart');
		$this->load->model('langs/mlangs');
		$data = $this->mcart->delete_item();
		if($data['success'] == TRUE)
		{
			$cart_edit_data = $this->mcart->get_cart_products();
			if($cart_edit_data && count($cart_edit_data['cart_products']) > 0)
			{
				$cart_edit_html = $this->load->view('sales/orders/create_order_products', array('order_data' => array('products' => $cart_edit_data)), TRUE);
			}
			else
			{
				$cart_edit_html = FALSE;
			}
			
			$cart_data = $this->mcart->get_cart_short();
			$cart_html = $this->load->view('sales/cart/cart_block', $cart_data + array('ajax' => true), TRUE);
			$cart_min_html = $this->load->view('sales/cart/cart_min_block', $cart_data + array('ajax' => true), TRUE);
			$site_messages = '<p>'.$data['message'].'</p>';
			$delay = 4000;
			$json_data = array('success' => 1, 'cart_edit_html' => $cart_edit_html, 'cart_html' => $cart_html, 'cart_min_html' => $cart_min_html, 'site_messages' => $site_messages, 'delay' => $delay);
			echo json_encode($json_data);
		}
		else
		{
			$cart_edit_data = $this->mcart->get_cart_products();
			if($cart_edit_data && count($cart_edit_data['cart_products']) > 0)
			{
				$cart_edit_html = $this->load->view('sales/orders/create_order_products', array('order_data' => array('products' => $cart_edit_data)), TRUE);
			}
			else
			{
				$cart_edit_html = FALSE;
			}
			
			$cart_data = $this->mcart->get_cart_short();
			$cart_html = $this->load->view('sales/cart/cart_block', $cart_data + array('ajax' => true), TRUE);
			$cart_min_html = $this->load->view('sales/cart/cart_min_block', $cart_data + array('ajax' => true), TRUE);
			$site_messages = '<p>'.$data['message'].'</p>';
			$delay = 6000;
			$json_data = array('success' => 0, 'cart_edit_html' => $cart_edit_html, 'cart_html' => $cart_html, 'cart_min_html' => $cart_min_html, 'site_messages' => $site_messages, 'delay' => $delay);
			echo json_encode($json_data);
		}
	}
}
?>