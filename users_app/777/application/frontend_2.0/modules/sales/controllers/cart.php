<?php
class Cart extends AG_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->mlangs->load_language_file('modules/cart');
		$this->template->add_js('jquery.gbc_cart', 'modules_js/sales');
		$this->template->add_js('jquery.gbc_orders', 'modules_js/sales');
		$this->template->add_js('jquery.gbc_edit_cart', 'modules_js/sales');
		$this->template->add_js('jquery.form', 'form');
		$this->template->add_js('jquery.validate.1.9.min', 'form');
		$this->template->add_js('messages_'.$this->mlangs->current_lang['code'], 'form/messages');
	}
	
	public function index()
	{
		$this->load->model('sales/mcart');
		$data = $this->mcart->get_cart_short();
		$this->build_cart_template_blocks($data);
	}
	
	protected function build_cart_template_blocks($data)
	{
		$this->template->add_view_to_template('cart_block', 'sales/cart/cart_outside_block', $data + array('cart_block_id' => 'cart_block', 'ajax' => true));
		$this->template->add_view_to_template('cart_inside_block', 'sales/cart/cart_block', array());
		$this->template->add_view_to_template('cart_block', 'sales/cart/cart_outside_block_js', array());
		
		$this->template->add_view_to_template('cart_min_block', 'sales/cart/cart_outside_min_block', $data + array('cart_min_block_id' => 'cart_min_block', 'ajax' => true));
		$this->template->add_view_to_template('cart_inside_min_block', 'sales/cart/cart_min_block', array());
		$this->template->add_view_to_template('cart_min_block', 'sales/cart/cart_outside_min_block_js', array());
	}
	
	public function ajax_add_item()
	{
		$this->load->model('sales/mcart');
		$this->load->model('langs/mlangs');
		$data = $this->mcart->add_item();
		if($data['success'] == TRUE)
		{
			$cart_data = $this->mcart->get_cart_short();
			$cart_html = $this->load->view('sales/cart/cart_block', $cart_data + array('ajax' => true), TRUE);
			$cart_min_html = $this->load->view('sales/cart/cart_min_block', $cart_data + array('ajax' => true), TRUE);
			$site_messages = $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$data['message'].'</p>'), TRUE);
			$json_data = array('success' => 1, 'cart_html' => $cart_html, 'cart_min_html' => $cart_min_html, 'site_messages' => $site_messages);
			echo json_encode($json_data);
		}
		else
		{
			$site_messages = $this->load->view('site_messages/site_messages', array('error_message' => '<p>'.$data['message'].'</p>'), TRUE);
			$json_data = array('success' => 0, 'site_messages' => $site_messages);
			if(isset($data['available_qty']))
			{
				$json_data += array('available_qty' => $data['available_qty']);
			}
			echo json_encode($json_data);
		}
	}
	
	public function edit_cart_form()
	{
		$this->mlangs->load_language_file('modules/products');
		$this->load->model('sales/mcart');
		$this->load->model('langs/mlangs');
		$data = $this->mcart->get_cart_products();
		$this->template->add_template_ajax('sales/cart/cart_edit', array('cart_edit' => $data, 'cart_edit_block_id' => 'cart_edit_block'));
		$this->template->add_template_ajax('sales/cart/cart_edit_js', array());
	}
	
	public function ajax_edit_cart_item()
	{
		$this->mlangs->load_language_file('modules/products');
		$this->load->model('sales/mcart');
		$this->load->model('langs/mlangs');
		$data = $this->mcart->edit_item();
		if($data['success'] == TRUE)
		{
			$cart_edit_data = $this->mcart->get_cart_products();
			$cart_edit_html = $this->load->view('sales/cart/cart_edit', array('cart_edit' => $cart_edit_data, 'cart_edit_block_id' => 'cart_edit_block'), TRUE);
			$cart_edit_html .= $this->load->view('sales/cart/cart_edit_js', array(), TRUE);
			
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
		$this->load->model('sales/mcart');
		$this->load->model('langs/mlangs');
		$data = $this->mcart->delete_item();
		if($data['success'] == TRUE)
		{
			$cart_edit_data = $this->mcart->get_cart_products();
			$cart_edit_html = $this->load->view('sales/cart/cart_edit', array('cart_edit' => $cart_edit_data, 'cart_edit_block_id' => 'cart_edit_block'), TRUE);
			$cart_edit_html .= $this->load->view('sales/cart/cart_edit_js', array(), TRUE);
			
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
			$cart_edit_html = $this->load->view('sales/cart/cart_edit', array('cart_edit' => $cart_edit_data, 'cart_edit_block_id' => 'cart_edit_block'), TRUE);
			$cart_edit_html .= $this->load->view('sales/cart/cart_edit_js', array(), TRUE);
			
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