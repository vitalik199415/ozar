<?php
class Categories_products extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Продукты в категории');
		$this->template->add_navigation('Продукты в категории',set_url('*/*/'));
	}
	
	public function index()
	{
		$this->load->model('catalogue/mcategories_products');
		$this->mcategories_products->render_categories_products_grid();
	}
	
	public function action()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
			$this->template->add_js('highslide.min', 'highslide');
			$this->template->add_css('highslide', 'highslide');
			$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
			$this->load->model('catalogue/mcategories_products');
			if(!$this->mcategories_products->render_actions($cat_id))
			{
				$this->messages->add_error_message('Возникли ошибки!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс редактирования не возможен!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function save_changes()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			$this->load->model('catalogue/mcategories_products');
			if($this->mcategories_products->check_isset_categorie($cat_id))
			{
				if($this->mcategories_products->save($cat_id))
				{
					$this->messages->add_success_message('Изменеия сохранены успешно!');
					$this->_redirect(set_url('*/*/action/cat_id/'.$cat_id));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при сохранении!');
					$this->_redirect(set_url('*/*'));
				}
			}
			else
			{
				$this->messages->add_error_message('Категории с указанным ID отсутсвует! Процесс редактирования не возможен!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Процесс редактирования не возможен!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function ajax_categories_products_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			$this->load->model('catalogue/mcategories_products');
			$this->load->model('catalogue/mproducts_settings');
			$settings = $this->mproducts_settings->get_products_settings();
			echo $this->mcategories_products->get_categories_products_grid($cat_id, $settings);
		}
	}
	
	public function export_action()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
			$this->template->add_js('highslide.min', 'highslide');
			$this->template->add_css('highslide', 'highslide');
			$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
			$this->load->model('catalogue/mcategories_products');
			if(!$this->mcategories_products->render_export($cat_id))
			{
				$this->messages->add_error_message('Возникли ошибки!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Експорт не возможен!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function export_cat()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			$this->load->model('catalogue/mcategories_products');
			if($this->mcategories_products->check_isset_categorie($cat_id))
			{
				if($this->mcategories_products->export_cat($cat_id))
				{
					$this->_redirect(set_url('*/*/action/cat_id/'.$cat_id));
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при сохранении!');
					$this->_redirect(set_url('*/*'));
				}
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутсвует! Невозможно експортировать товары!');
			$this->_redirect(set_url('*/*'));
		}
	}
}
?>