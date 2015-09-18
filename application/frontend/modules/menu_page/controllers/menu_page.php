<?php
class Menu_page extends AG_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->model('menu_page/mmenu_page');
		
		$view_array = $this->mmenu_page->get_menu_collection();
		$this->template->add_view_to_template('menu_block', 'menu_page/menu', $view_array);
		$this->template->add_view_to_template('menu_init', 'menu_page/menu_js_init', array());
	}
	
	public function home()
	{
		$this->load->model('menu_page/mmenu_page');
		$this->mmenu_page->call_home_module_function();
	}
	
	public function menu()
	{
		$this->load->model('menu_page/mmenu_page');
		$this->mmenu_page->call_module_function();
	}
}
?>