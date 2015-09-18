<?php
class Company extends AG_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->template->add_title('Каталог продукции - Продукты каталога');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Продукты каталога', set_url('*/*/'));
	}
}