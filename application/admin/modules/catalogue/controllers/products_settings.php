<?php
class Products_settings extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Каталог продукции - Продукты каталога - Настройки');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Продукты каталога',site_url('/catalogue/products'))->add_navigation('Настройки');
	}
	function index()
	{
		$this->load->model('catalogue/mproducts_settings');
		$this->mproducts_settings->edit();
		$this->template->add_js('iColorPicker');
	}
	
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('catalogue/mproducts_settings');
			if($this->mproducts_settings->save())
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