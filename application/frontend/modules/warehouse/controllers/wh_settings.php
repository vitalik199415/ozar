<?php
class Wh_settings extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Склад | Настройки');
		$this->template->add_navigation('Склад')->add_navigation('Настройки');
	}
	function index()
	{
		$this->load->model('warehouse/mwh_settings');
		$this->mwh_settings->edit();
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('warehouse/mwh_settings');
			if($this->mwh_settings->save())
			{
				$this->massages->add_success_massage('Настройки успешно сохранены!');
				$this->_redirect(set_url('*/*'));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки при сохранении настроек!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->_redirect(set_url('*/*'));
		}
	}
}
?>