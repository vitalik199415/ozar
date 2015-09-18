<?php
class Wh_settings extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Склад | Настройки');
		$this->template->add_navigation('Склад')->add_navigation('Настройки');
		$user = $this->musers->get_user();
		if($user['warehouse'] == 0)
		{
			$this->template->add_template('warehouse/wh_access', array(), '1');
			$this->template->render(TRUE);
		}
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
				$this->messages->add_success_message('Настройки успешно сохранены!');
				$this->_redirect(set_url('*/*'));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при сохранении настроек!');
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