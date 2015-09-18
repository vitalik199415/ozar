<?php
class Index extends AG_Controller
{
	protected $base_js = array(
		'jquery' => 'jquery-1.7.1.min',
		'jquery-ui' => 'jquery-ui.min',
		'jquery.tools' => 'jquery.tools.min',
		'anythingslider' => 'anythingslider/jquery.anythingslider.min',
		'jquery.multi-ddm' => 'jquery.multi-ddm',
		'functions' => 'functions',
		'highslide' => 'highslide/highslide-full.min',
		'highslide.config' => 'highslide/highslide.config',
		'jquery.scrollTo' => 'jquery.scrollTo-1.4.3.1-min',
		'jquery.inputmask' => 'inputmask/jquery.inputmask.bundle.min'
	);
	protected $base_css = array(
		'ui/jquery-ui',
		'base',
		'style',
		'media_320',
		'media_480',
		'media_540',
		'media_768',
		'media_960',
		'additional_base',
		'additional_style',
		'anythingslider/anythingslider',
		'highslide/highslide'
	);
	protected $base_modules = array(
		'overlay/build_overlay_block',
		'langs/index',
		'catalogue/currency/index',
		'sales/favorites/index',
		'sales/cart/index',
		'customers/index',
		'search/index',
		'site_settings/site_description/index',
		'menu_page/index',
		'catalogue/categories/index'
	);
	/*
	'catalogue/products/get_bestseller_products',
		'catalogue/products/get_sale_products'
		*/
	
	protected $additional_js = array();
	protected $additional_js_code = array();
	protected $additional_css = array();
	protected $additional_js_src = array();
	
	protected $aditional_modules = array(
	'catalogue/products/get_bestseller_products',
	'catalogue/products/get_sale_products',
	'catalogue/products/get_new_products'
	);
	
	function __construct()
	{
		parent::__construct();
		$this->_set_base_html_data();
		$this->_run_base_modules();
		$this->_run_aditional_modules();
		$this->_set_aditional_js_css_data();
		$this->_set_aditional_template();
	}
	
	protected function set_lang_pack_js()
	{
		$lang = $this->mlangs->current_lang;
		$this->additional_js['highslide.lang'] = 'highslide/highslide.lang.'.$lang['code'];
	}
	
	protected function _set_base_html_data()
	{	
		$this->set_lang_pack_js();
		$this->_set_base_js();
		$this->_set_base_css();
		$this->_set_base_template();
	}
	private function _set_base_js()
	{
		foreach($this->base_js as $ms)
		{
			$this->template->add_js($ms);
		}
	}
	private function _set_base_css()
	{
		foreach($this->base_css as $ms)
		{
			$this->template->add_css($ms);
		}
	}
	protected function _set_base_template()
	{
		$this->template->add_base_header('overlay', array(), 'overlay_block');
		$this->template->add_header('header',array(),'header_block');
		$this->template->add_template('content', array(), 'content_block');
		$this->template->add_footer('footer',array(),'footer_block');
	}
	
	protected function _set_aditional_js_css_data()
	{
		foreach($this->additional_css as $ms)
		{
			$this->template->add_css($ms);
		}
		foreach($this->additional_js as $ms)
		{
			$this->template->add_js($ms);
		}
		foreach($this->additional_js_src as $ms)
		{
			$this->template->add_js_src($ms);
		}
		foreach($this->additional_js_code as $ms)
		{
			$this->template->add_js_code($ms);
		}
	}
	protected function _set_aditional_template()
	{
		return FALSE;
	}
	
	
	protected function _run_base_modules()
	{	
		foreach($this->base_modules as $ms)
		{
			if(is_array($ms))
			{
				$module = $ms[0];
				echo call_user_func_array('modules::run', array($module)+$ms[1]);
			}
			else
			{
				echo modules::run($ms);
			}	
		}
	}
	protected function _run_aditional_modules()
	{
		foreach($this->aditional_modules as $ms)
		{
			if(is_array($ms))
			{
				$module = $ms[0];
				echo call_user_func_array('modules::run', array($module)+$ms[1]);
			}
			else
			{
				echo modules::run($ms);
			}	
		}
	}

	public function index()
	{
		echo modules::run('menu_page/home');
	}
	
	public function menu()
	{
		echo modules::run('menu_page/menu');
	}
	
	public function category()
	{
		echo modules::run('catalogue/categories/get_products');
	}
	
	public function category_filters_sort()
	{
		echo modules::run('catalogue/products/get_category_products_sort_filtered');
	}
	
	public function catalogue()
	{
		echo "catalogue";
	}
	
	public function product()
	{
		echo modules::run('catalogue/products/get_product');
	}
	
	public function search()
	{
		$method = $this->uri->ruri_to_assoc();
		$method = $method['method'];
		echo modules::run('search/'.$method);
	}
}
?>