<?php
class Warehouses_credit_memo extends AG_Controller
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
		$this->template->add_title(' | Продажи | Возвраты');
		$this->template->add_navigation('Продажи', set_url('*/warehouses_sales'))->add_navigation('Возвраты', set_url('*/warehouses_credit_memo'));
		$this->load->model('warehouse/mwarehouses_credit_memo');
		$this->mwarehouses_credit_memo->render_wh_credit_memo_grid();
	}
}