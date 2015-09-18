<?php
class Customers_settings extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Покупатели - Настройки');
		$this->template->add_navigation('Покупатели',site_url('/customers'))->add_navigation('Настройки покупателей');
	}
	function index()
	{
		$this->load->model('customers/mcustomers_settings');
		$this->mcustomers_settings->edit();
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('customers/mcustomers_settings');
			if($this->mcustomers_settings->save())
			{
				$this->messages->add_success_message('Настройки успешно сохранены!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при сохранении настроек!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->_redirect(set_url('*/*/'));
		}
	}
	
	
}
?>