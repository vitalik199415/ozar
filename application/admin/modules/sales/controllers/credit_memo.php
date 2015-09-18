<?php
class Credit_memo extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Продажи - Возвраты');
		$this->template->add_navigation('Продажи')->add_navigation('Возвраты заказов', set_url('*/*'));
	}
	
	public function index()
	{
		$this->load->model('sales/mcredit_memo');
		$this->mcredit_memo->render_credit_memo_grid();
	}
	
	public function view_credit_memo()
	{
		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cm_id']) && ($cm_id = intval($URI['cm_id']))>0)
		{
			$this->template->add_title(' - Просмотр возврата заказа');
			$this->template->add_navigation('Просмотр возврата заказа');
			$this->load->model('sales/mcredit_memo');
			if(!$this->mcredit_memo->view_credit_memo($cm_id))
			{
				$this->messages->add_error_message('Возврата не существует! Просмотр не возможен!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Просмотр не возможен!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function create_credit_memo()
	{
		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->template->add_title(' - Создание возврата заказа');
			$this->template->add_navigation('Создание возврата заказа');
			$this->load->model('sales/mcredit_memo');
			if(!$this->mcredit_memo->create_credit_memo($ord_id))
			{
				$this->messages->add_error_message('Создание возврата заказа невозможно!');
				$this->_redirect(set_url('*/orders/view/ord_id/'.$ord_id));
			}
		}
	}
	
	public function save_credit_memo()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->load->model('sales/mcredit_memo');
			if($cm_id = $this->mcredit_memo->save_credit_memo($ord_id))
			{
				$this->messages->add_success_message('Возврата заказа удачно создан!');
				$this->_redirect(set_url('*/*/view_credit_memo/cm_id/'.$cm_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при создании возврата заказа!');
				$this->_redirect(set_url('*/orders/view/ord_id/'.$ord_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Создание не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
}
?>