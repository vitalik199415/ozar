<?php
require_once "./application/frontend_2.0/modules/catalogue/controllers/categories.php";
class Users_categories extends Categories
{	
	public function home_categories()
	{
		$this->load->model('catalogue/mcategories');
		$view_array = $this->mcategories->get_categories_tree_collection_lvl2();
		$this->template->add_view_to_template('home_categories_block', 'catalogue/categories/home_categories_tree', $view_array + array('settings' => $this->settings));
	}
}
?>