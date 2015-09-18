<?php
class Catalogue_mass_edit_price extends AG_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->template->add_title('Каталог | Изменение цены');
		$this->template->add_navigation('Каталог')->add_navigation('Изменение цены',set_url('*/*'));
	}
	
	public function index()
	{
		$this->load->model('catalogue/mcatalogue_mass_edit_price');
		$this->mcatalogue_mass_edit_price->render_categories_grid();
	}
	
	public function actions()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
			$this->template->add_js('highslide.min', 'highslide');
			$this->template->add_css('highslide', 'highslide');
			$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
			$this->load->model('catalogue/mcatalogue_mass_edit_price');
			if(!$this->mcatalogue_mass_edit_price->render_actions($cat_id))
			{
				$this->massages->add_error_massage('Категории с указанным ID не существует!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр отсутствует!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function get_ajax_categories_products()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			$this->load->model('catalogue/mcatalogue_mass_edit_price');
			echo $this->mcatalogue_mass_edit_price->get_categories_products_grid($cat_id);
		}
	}
	
	public function save_changes()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['cat_id']) && ($cat_id = intval($URI['cat_id']))>0)
		{
			$this->load->model('catalogue/mcatalogue_mass_edit_price');
			$this->mcatalogue_mass_edit_price->save_changes();
			$this->_redirect(set_url('*/*/actions/cat_id/'.$cat_id));
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют!');
			$this->_redirect(set_url('*/*'));
		}
	}
}
?>