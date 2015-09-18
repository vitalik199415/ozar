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
		$this->load->model('catalogue/mcategories');
		$per_cat = $this->mcategories->check_permission();
		$customer = $this->session->userdata('CUSTOMER');
		$customer_groups = array();
		$ch = false;
		$this->template->add_template('catalogue_template', array(), 'content_block');

		if($per_cat['permission'] == 0){
			modules::run('catalogue/products/get_category_products');
		}	
		else if($per_cat['permission'] == 1)
		{
			if($customer)
			{
				modules::run('catalogue/products/get_category_products');
			}
			else
			{
				$this->template->add_view_to_template('center_block', 'catalogue/products/permission_template', array());
			}
		}
		else if ($per_cat['permission'] == 2)
		{	
			if($customer)
			{	
				foreach ($customer['m_u_types'] as $id => $value) {
					$customer_groups[] = $value;
				}

				$cat_groups = $this->mcategories->get_category_id_groups($per_cat['ID']);

				foreach ($cat_groups as $ms) {
					foreach ($ms as $key => $value) {
						if(in_array($value, $customer_groups))
						{
							modules::run('catalogue/products/get_category_products');
							$ch = false;
							break(2);
						}
						else $ch = true;
					}
				}


				if($ch == true) $this->template->add_view_to_template('center_block', 'catalogue/products/permission_template', array());
			}
			else
			{
				$this->template->add_view_to_template('center_block', 'catalogue/products/permission_template', array());
			}
		}
		
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