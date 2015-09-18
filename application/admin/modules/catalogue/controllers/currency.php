<?php
class Currency extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Каталог продукции - Валюты каталога');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Валюты каталога', set_url('*/*/'));
	}
	public function index()
	{
		$this->load->model('mcurrency');
		$this->mcurrency->edit();
	}
	public function save()
	{
		if(isset($_POST))
		{
			$this->load->model('mcurrency');
			if($this->mcurrency->save())
			{
				$this->messages->add_success_message('Валюты каталога успешно сохранены!');
				$this->_redirect(set_url('*/*/'));
			}
			else
			{
				$this->messages->add_error_message('Произошли ошибки при сохранение валют каталога!');
				$this->_redirect(set_url('*/*/'));
			}
		}
	}
}	
?>