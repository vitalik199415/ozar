<?php
class Products_excel_export extends AG_Controller{

	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Експорт продуктов');
		$this->template->add_navigation('Продукты каталога', set_url('catalogue/products'))->add_navigation('Експорт продуктов');
	}

	public function index()
	{
		$this->load->model('catalogue/mproducts_excel_export');

		$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
		$this->template->add_js('highslide.min', 'highslide');
		$this->template->add_css('highslide', 'highslide');
		$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
		$this->mproducts_excel_export->render_product_form();
		if(!$this->input->post('ajax')) $this->template->add_template('catalogue/products/products_grid_js', array());
	}

	public function export()
	{
		$this->load->model('catalogue/mproducts_excel_export');
		$this->mproducts_excel_export->export();

	}

	public function get_ajax_products_grid()
	{
		$this->load->model('catalogue/mproducts_excel_export');
		echo $this->mproducts_excel_export->render_product_grid();
	}

}

?>