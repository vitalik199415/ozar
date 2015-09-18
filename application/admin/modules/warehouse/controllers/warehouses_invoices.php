<?php
class Warehouses_invoices extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Склад');
		$this->template->add_navigation('Склад');
		$user = $this->musers->get_user();
		if($user['warehouse'] == 0)
		{
			$this->template->add_template('warehouse/wh_access', array(), '1');
			$this->template->render(TRUE);
		}
	}

	public function index()
	{
		$this->template->add_title(' | Продажи | Инвойсы');
		$this->template->add_navigation('Продажи', set_url('*/warehouses_sales'))->add_navigation('Инвойсы', set_url('*/warehouses_invoices'));
		$this->load->model('warehouse/mwarehouses_invoices');
		$this->mwarehouses_invoices->render_wh_invoices_grid();
	}
}