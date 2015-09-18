<?php
require_once "./application/frontend_2.0/controllers/index.php";
class Users_index extends Index
{
	protected $additional_js = array('jquery.carouFredSel-5.6.1', 'jquery.gbc_dropdown_menu', 'jquery.elevatezoom','jssor.slider.min','jssor','jquery.slicknav','jquery-scrolltofixed','jquery.jbcallme','readmore','jquery.scrollUp','jquery.fancybox-1.2.1.pack','jquery.arcticmodal-0.3.min','jgallery.min','touchswipe.min');
	protected $additional_css = array('style_carousel','font-awesome','slicknav','jquery.jbcallme','jquery.fancybox','jquery.arcticmodal-0.3','jgallery');
	
	protected $aditional_modules = array(array('catalogue/products/get_bestseller_products', array('count' => 9, 'random' => TRUE)), array('catalogue/products/get_new_products', array('count' => 9, 'random' => TRUE)), array('catalogue/products/get_sale_products', array('count' => 9, 'random' => TRUE)), array('catalogue/products/get_rubashki_categories_products', array('count' => 9, 'random' => TRUE)), array('catalogue/products/get_jeans_categories_products', array('count' => 9, 'random' => TRUE)), array('catalogue/products/get_svitera_categories_products', array('count' => 9, 'random' => TRUE)), 'news/last_news', 'news/last_statti','reviews/last_reviews','menu_page/footer_menu');
	
	public function index()
	{
		parent::index();
		$this->template->add_header('header',array(),'header_block');
		echo modules::run('catalogue/categories/home_categories');
	}

	public function menu()
	{
		parent::menu();
		$this->template->add_template('menu_page', array(), 'content_block');
		echo modules::run('catalogue/categories/home_categories');

	}
	
	public function category()
	{
		parent::category();
		$this->template->add_template('products_page', array(), 'content_block');
		//$url = $this->variables->get_url_vars('category_url');
		//  if($url == "jeans"){
		//   $this->template->add_template('jeans_page', array(), 'content_block');
		//  }
		echo modules::run('catalogue/categories/home_categories');
		
		
	}
	
	public function product()
	{
		parent::product();
		$this->template->add_template('products_page', array(), 'content_block');
		echo modules::run('catalogue/categories/home_categories');
		$this->template->add_template('catalogue/products/products_template', array(), 'content_block');
	}
	
		public function search()
	{
		$this->template->add_template('products_page', array(), 'content_block');
		$this->template->add_js('jquery.elevatezoom');
		$this->template->add_css('cloud-zoom', 'cloud-zoom');
		parent::search();
		echo modules::run('catalogue/categories/home_categories');
	}
	public function category_filters_sort()
	{
		echo modules::run('catalogue/products/get_category_products_sort_filtered');
		echo modules::run('catalogue/categories/home_categories');
	}

	public function discount_coupons()
	{
		parent::discount_coupons();
		$this->template->add_template('products_page', array(), 'content_block');
		echo modules::run('catalogue/categories/home_categories');
	}
}
?>