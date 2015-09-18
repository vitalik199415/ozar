<?php
class Sales_settings extends AG_Controller
{
	public function index()
	{
		$this->template->add_navigation('Продажи')->add_navigation('Настройки');
		$this->load->model('sales/msales_settings');
		$this->msales_settings->edit();
	}
	
	public function save()
	{
		if(isset($_POST))
		{	
			$this->load->model('sales/msales_settings');
			if($this->msales_settings->save())
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