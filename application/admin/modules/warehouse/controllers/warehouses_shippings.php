<?php
class Warehouses_shippings extends AG_Controller
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
		$this->template->add_title(' | Продажи | Отправки');
		$this->template->add_navigation('Продажи', set_url('*/warehouses_sales'))->add_navigation('Отправки', set_url('*/warehouses_shippings'));
		$this->load->model('warehouse/mwarehouses_shippings');
		$this->mwarehouses_shippings->render_wh_shippings_grid();
	}
}