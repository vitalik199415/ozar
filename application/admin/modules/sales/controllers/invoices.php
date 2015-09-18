<?php
class Invoices extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Продажи - Инвойсы');
		$this->template->add_navigation('Продажи')->add_navigation('Инвойсы', set_url('*/*'));
	}
	
	public function index()
	{
		$this->load->model('sales/minvoices');
		$this->minvoices->render_invoice_grid();
	}
	
	public function view_invoice()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['inv_id']) && ($inv_id = intval($URI['inv_id']))>0)
		{
			$this->template->add_title(' - Просмотр инвойса');
			$this->template->add_navigation('Просмотр инвойса');
			$this->load->model('sales/minvoices');
			if(!$this->minvoices->view_invoice($inv_id))
			{
				//$this->messages->add_error_message('Инвойса не существует! Просмотр не возможен!');
				//$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			//$this->messages->add_error_message('Параметр ID отсутствует! Просмотр не возможен!');
			//$this->_redirect(set_url('*/*'));
		}
	}
	
	public function create_invoice()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->template->add_title(' - Создание инвойса');
			$this->template->add_navigation('Создание инвойса');
			$this->load->model('sales/minvoices');
			if(!$this->minvoices->create_invoice($ord_id))
			{
				$this->messages->add_error_message('Создание инвойса невозможно!');
				$this->_redirect(set_url('*/orders/view/ord_id/'.$ord_id));
			}
		}
	}
	
	public function save_invoice()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->load->model('sales/minvoices');
			if($inv_id = $this->minvoices->save_invoice($ord_id))
			{
				$this->messages->add_success_message('Инвойс удачно создан!');
				$this->_redirect(set_url('*/*/view_invoice/inv_id/'.$inv_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при создании инвойса!');
				$this->_redirect(set_url('*/orders/view/ord_id/'.$ord_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Создание не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function edit_invoice()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['inv_id']) && ($inv_id = intval($URI['inv_id']))>0)
		{
			$this->load->model('sales/minvoices');
			if($this->minvoices->edit_invoice($inv_id))
			{
				$this->messages->add_success_message('Инвойс удачно отредактирован!');
				$this->_redirect(set_url('*/*/view_invoice/inv_id/'.$inv_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при редактировании инвойса!');
				$this->_redirect(set_url('*/*/view_invoice/inv_id/'.$inv_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function cancel_invoice()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['inv_id']) && ($inv_id = intval($URI['inv_id']))>0)
		{
			$this->load->model('sales/minvoices');
			if($this->minvoices->cancel_invoice($inv_id))
			{
				$this->messages->add_success_message('Инвойс отменен!');
				$this->_redirect(set_url('*/*/view_invoice/inv_id/'.$inv_id));
			}
			else
			{
				$this->messages->add_error_message('Отмена инвойса не возможна!');
				$this->_redirect(set_url('*/*/view_invoice/inv_id/'.$inv_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function send_invoice_mail()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['inv_id']) && ($inv_id = intval($URI['inv_id']))>0)
		{
			$this->load->model('sales/minvoices');
			if($this->minvoices->send_invoice_email($inv_id))
			{
				$this->messages->add_success_message('Письмо с инвойсом отправлено повторно!');
				$this->_redirect(set_url('*/*/view_invoice/inv_id/'.$inv_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при повторной отправке письма!');
				$this->_redirect(set_url('*/*/view_invoice/inv_id/'.$inv_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
}
?>