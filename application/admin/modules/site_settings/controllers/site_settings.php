<?php
class Site_settings extends AG_Controller
{
	const BACK = '*/';
	//const N = 3;
	
	public function __construct()
	{
		parent::__construct();
		$this->template->add_title('Настройки сайта');
		$this->template->add_navigation('Настройки сайта');
	}
	
	public function index()
	{
		$this->load->model('site_settings/msite_settings');
		$this->msite_settings->edit();
	}
	
	public function save()
	{
		$this->load->model('site_settings/msite_settings');
		if($this->msite_settings->save())
		{
			$this->messages->add_success_message('Настройки успешно сохранены!');
			$this->_redirect(set_url(self::BACK));
		}
		else
		{
			$this->messages->add_error_message('Возникли ошибки при сохранении настроек! Повторите попытку.');
			$this->_redirect(set_url(self::BACK));
		}
	}
}	
?>