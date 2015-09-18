<?php
class Catalogue_sort extends AG_Controller
{
	public function __construct()
	{
		$this->mlangs->load_language_file('modules/products');
		parent::__construct();
	}
	
	public function index()
	{	
		$this->load->model('catalogue/mcatalogue_sort');
		$this->mcatalogue_sort->_init();
		$active_sort = $this->mcatalogue_sort->get_active_sort();
		$prod_per_page = $this->mcatalogue_sort->get_limit_array();
		$this->template->add_css('sorting', 'modules/sorting');
		$this->template->add_js('jquery.gbc_sorting', 'modules_js/catalogue/sorting');
		$this->template->add_view_to_template('sorting_block', 'catalogue/catalogue_sort/catalogue_sort_block', array('category_url' => $types_array['category_url'], 'prod_per_page' => $prod_per_page));
		$this->template->add_view_to_template('sorting_block', 'catalogue/catalogue_sort/catalogue_sort_block_js', array('active_sort' => $active_sort, 'prod_per_page' => $prod_per_page));
		$this->template->add_template('products_page', array(), 'content_block');
	}

	public function submit_sort()
	{
		$URI = $this->uri->uri_to_assoc();
		if(isset($URI['category_url']))
		{
			$category_url = $URI['category_url'];
			$this->load->model('catalogue/mcatalogue_sort');
			$this->mcatalogue_sort->submit_sort($category_url);
		}
	}
}