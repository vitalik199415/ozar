<?php
class Products_related extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Сопутствующие продукты');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Сопутствующие продукты', set_url('*/*'));
		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
	}
	
	public function index()
	{
		$this->load->model('catalogue/mproducts_related');
		$this->template->add_js('highslide.min', 'highslide');
		$this->template->add_css('highslide', 'highslide');
		$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
		$this->mproducts_related->render_product_related_grid();
		if(!$this->input->post('ajax')) $this->template->add_template('catalogue/products/show_product_js', array('show_products_block_id' => 'products_related_grid'));
	}
	
	public function get_related()
	{
		$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
		$this->template->add_css('overlay','jquery_tools/overlay');
		$this->template->add_js('jquery.gbc_related_form','modules_js/catalogue');
		$this->load->model('catalogue/mproducts_related');
		
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			
			$relared_products = $this->mproducts_related->get_related_products($ID);
			if(count($relared_products) == 0) 
			{
				$this->_redirect(set_url('*/*/add_related/id/'.$ID));
			}
			else
			{
				if(!$this->mproducts_related->get_related($ID))
				{
					$this->messages->add_error_message('Возникли ошибки генерации добавления товаров!');
					$this->_redirect(set_url('*/*'));
				}
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Добавление невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	
	}
	
	public function add_related()
	{
		/*$this->template->add_title(' Добавление сопутствующих товаров');
		$this->template->add_navigation('Добавление сопутствующих товаров');*/
		$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
		$this->template->add_css('overlay','jquery_tools/overlay');
		$this->template->add_js('jquery.gbc_related_form','modules_js/catalogue');
		$this->load->model('catalogue/mproducts_related');
		
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && intval($URI['id'])>0)
		{
			$ID = intval($URI['id']);
			
			if(!$this->mproducts_related->add_related($ID))
			{
				$this->messages->add_error_message('Возникли ошибки генерации добавления товаров!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Добавление невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	
	}
	
	public function save_related()
	{
		if($this->input->post('products_related_checkbox'))
		{
			$this->load->model('catalogue/mproducts_related');
			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['id']) && intval($URI['id'])>0)
			{
				$id = intval($URI['id']);
				if($this->mproducts_related->save_related($id))
				{
					$this->messages->add_success_message('Продукты успешно добавлены в сопутствующие!');
					$this->_redirect(set_url('*/*/get_related/id/'.$id)); 
				}
				else
				{
					$this->messages->add_error_message('Возникли ошыбки добавления продуктов!');
					$this->_redirect(set_url('*/*/add_related/id/'.$id));
				}
			}
		}
		else
		{
			$this->_redirect(set_url('*/*'));		
		}
	}
	
	public function ajax_view_short()
	{	
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('mproducts');
			if($product = $this->mproducts->get_view_product($ID))
			{ 	
				echo json_encode(array('success' => 1, 'html' => $this->load->view('catalogue/products/view_shor_product', $product, TRUE)));
			}
			else
			{
				echo json_encode(array('success' => 0));
			}
		}
		else
		{
			echo json_encode(array('success' => 0));
		}
	}
	
	public function delete_related()
	{ 	
		$this->load->model('catalogue/mproducts_save');
		$this->load->model('catalogue/mproducts_related');
	
		$parent = $this->uri->uri_to_assoc(4);
		$related = $this->uri->uri_to_assoc(6);
		
		if (isset($parent['id']) && ($id_parent = intval($parent['id']))>0 )
		{ 
			if (isset($related['pr_id']) && ($id_related = intval($related['pr_id']))>0)
			{
				if($this->mproducts_save->check_isset_pr($id_related))
				{
					$this->mproducts_related->delete_related($id_parent, $id_related);
					$this->messages->add_success_message('Продукт успешно удален из сопутствующих!');
					$this->_redirect(set_url('*/*/get_related/id/'.$id_parent));
					//$this->_redirect(set_url('*/*/edit/id/'.$id_parent));
				}
				else
				{
					$this->messages->add_error_message('Продукта не существует, удаление невозможно!');
					$this->_redirect(set_url('*/*'));
				}
			}
			else
			{
				$this->mproducts_related->delete_related($id_parent);
				$this->messages->add_success_message('Продукт успешно удален из сопутствующих!');
				$this->_redirect(set_url('*/*/get_related/id/'.$id_parent));	
			}
			
			
		}
		else
		{
			$this->messages->add_error_message('Отсутствует параметр ID - удаление невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	
	public function get_ajax_add_related()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_related');
			echo $this->mproducts_related->add_related_grid_build($id);
		}
	}
	
	public function get_ajax_get_related()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($id = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_related');
			echo $this->mproducts_related->get_related_grid_build($id);
		}
	}
	
}

?>