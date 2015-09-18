<?php
class Categories extends AG_Controller
{
	protected $settings = FALSE;
	
	function __construct()
	{
		parent::__construct();
		$this->mlangs->load_language_file('modules/products');
	}
	
	public function index()
	{
		$this->template->add_js('jquery.gbc_categories_tree', 'modules_js/catalogue/categories');
		$this->load->model('catalogue/mcategories');
		$view_array = $this->mcategories->get_categories_tree_collection();
		$this->template->add_view_to_template('categories_block', 'catalogue/categories/categories_tree', $view_array + array('settings' => $this->settings));
		$this->template->add_view_to_template('categories_init', 'catalogue/categories/categories_js_init', array());
	}
	
	public function get_products()
	{	
		modules::run('catalogue/products/get_categorie_products');
	}
	
	public function get_categories_ajax()
	{
		if($cat_id = $this->input->post('cat_id'))
		{
			$this->load->model('catalogue/mcategories');
			$view_array = $this->mcategories->get_child_categories_collection($cat_id);
			echo $this->load->view('catalogue/categories/categories_ajax', $view_array, TRUE);
		}
	}
}
?>