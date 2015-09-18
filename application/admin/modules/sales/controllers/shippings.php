<?php
class Shippings extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Продажи - Отправки');
		$this->template->add_navigation('Продажи')->add_navigation('Отправки', set_url('*/*'));
	}
	
	public function index()
	{
		$this->load->model('sales/mshippings');
		$this->mshippings->render_shipping_grid();
	}
	
	public function view_shipping()
	{
		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['shp_id']) && ($shp_id = intval($URI['shp_id']))>0)
		{
			$this->template->add_title(' - Просмотр отправки');
			$this->template->add_navigation('Просмотр отправки');
			
			$this->template->add_js('jquery.gbc_shippings', 'modules_js/sales');
			
			$this->template->add_js('jquery.print_element.min');
			$this->template->add_js('highslide.min', 'highslide');
			$this->template->add_css('highslide', 'highslide');
			$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
			
			$this->load->model('sales/mshippings');
			if(!$this->mshippings->view_shipping($shp_id))
			{
				$this->messages->add_error_message('Отправки не существует! Просмотр не возможен!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Просмотр не возможен!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function create_shipping()
	{
		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->template->add_title(' - Создание отправки');
			$this->template->add_navigation('Создание отправки');
			$this->load->model('sales/mshippings');
			if($this->mshippings->create_shipping($ord_id) === FALSE)
			{
				$this->messages->add_error_message('Возникли ошибки при создании отправки!');
				$this->_redirect(set_url('*/orders/view/ord_id/'.$ord_id));
			}
		}
	}
	
	public function save_shipping()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['ord_id']) && ($ord_id = intval($URI['ord_id']))>0)
		{
			$this->load->model('sales/mshippings');
			if($shp_id = $this->mshippings->save_shipping($ord_id))
			{
				$this->messages->add_success_message('Отправка удачно создана!');
				$this->_redirect(set_url('*/*/view_shipping/shp_id/'.$shp_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при создании отправки!');
				$this->_redirect(set_url('*/orders/view/ord_id/'.$ord_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Создание не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function edit_shipping()
	{
		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['shp_id']) && ($shp_id = intval($URI['shp_id']))>0)
		{
			$this->load->model('sales/mshippings');
			if($shp_id = $this->mshippings->edit_shipping($shp_id))
			{
				$this->messages->add_success_message('Отправка удачно отредактирована!');
				$this->_redirect(set_url('*/*/view_shipping/shp_id/'.$shp_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при редактировании отправки!');
				$this->_redirect(set_url('*/*/view_shipping/shp_id/'.$shp_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Редактирование не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function cancel_shipping()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['shp_id']) && ($shp_id = intval($URI['shp_id']))>0)
		{
			$this->load->model('sales/mshippings');
			if($shp_id = $this->mshippings->cancel_shipping($shp_id))
			{
				$this->messages->add_success_message('Отправка отменена!');
				$this->_redirect(set_url('*/*/view_shipping/shp_id/'.$shp_id));
			}
			else
			{
				$this->messages->add_error_message('Отмена отправки не возможна!');
				$this->_redirect(set_url('*/*/view_shipping/shp_id/'.$shp_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
	
	public function send_shipping_mail()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['shp_id']) && ($shp_id = intval($URI['shp_id']))>0)
		{
			$this->load->model('sales/mshippings');
			if($this->mshippings->send_shipping_email($shp_id))
			{
				$this->messages->add_success_message('Письмо отправлено повторно!');
				$this->_redirect(set_url('*/*/view_shipping/shp_id/'.$shp_id));
			}
			else
			{
				$this->messages->add_error_message('Ошибка при отправке повторного письма!');
				$this->_redirect(set_url('*/*/view_shipping/shp_id/'.$shp_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}
}
?>