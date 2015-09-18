<?php
class Warehouses_products extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
		$this->template->add_js('highslide.min', 'highslide');
		$this->template->add_css('highslide', 'highslide');
		$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
		
		$this->load->model('warehouse/mwarehouses_products');
		$this->mwarehouses_products->get_warehouses_all_pr_grid();
		
		if(!$this->input->post('ajax')) $this->template->add_template('catalogue/products/products_grid_js', array('product_grid_id' => 'warehouses_all_products_grid'));
	}
	
	public function ajax_get_not_exists_pr()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id'])) > 0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->not_in_wh_pr_grid($wh_id))
			{
				if($this->input->post('ajax'))
				{
					echo $data;
				}
				else
				{
					$data = $this->load->view('warehouse/wh_actions_pr_not_in_wh', array('html_data' => $data), TRUE);
					echo json_encode(array('success' => 1, 'html' => $data));
				}
			}
		}
	}
	
	public function add_exist_pr()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id'])) > 0 && ($pr_id = intval($URI['pr_id'])) > 0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if(!$this->mwarehouses_products->add_exist_pr_to_wh($wh_id, $pr_id))
			{
				$this->massages->add_error_massage('Возникли ошибки при добавлении продукта!');
				$this->_redirect(set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function save_exist_pr()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id'])) > 0 && ($pr_id = intval($URI['pr_id'])) > 0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($this->mwarehouses_products->save_exist_pr_to_wh($wh_id, $pr_id))
			{
				$this->massages->add_success_massage('Продукт успешно добавлен на склад!');
				$this->_redirect(set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки при добавлении продукта!');
				$this->_redirect(set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function add_pr_to_wh()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id'])) > 0)
		{	
			$this->load->model('warehouse/mwarehouses_products');
			if(!$this->mwarehouses_products->add_pr_to_wh($wh_id))
			{
				$this->massages->add_error_massage('Действие невозможно!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function save_pr_to_wh()
	{
		$this->load->model('warehouse/mwarehouses_products');
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			if($this->mwarehouses_products->save_pr_to_wh($wh_id))
			{
				$this->massages->add_success_massage('Продукт удачно добавлен!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки при добавлении нового продукта!');
				$this->_redirect(set_url('*/warehouses/add_pr_to_wh/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function add_pr_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if(!$this->mwarehouses_products->add_pr_qty($wh_id, $pr_id))
			{
				$this->massages->add_error_massage('Возникли ошибки при добавлении количества продукта!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function save_add_pr_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($this->mwarehouses_products->save_add_pr_qty($wh_id, $pr_id))
			{
				$this->massages->add_success_massage('Количество продукта успешно добавлено!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки про добавлении количества!');
				$this->_redirect(set_url('warehouse/warehouses'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function reject_pr_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if(!$this->mwarehouses_products->reject_pr_qty($wh_id, $pr_id))
			{
				$this->massages->add_error_massage('Возникли ошибки при списании количества продукта!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function save_reject_pr_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($this->mwarehouses_products->save_reject_pr_qty($wh_id, $pr_id))
			{
				$this->massages->add_success_massage('Количество продукта успешно списано!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки про списании количества!');
				$this->_redirect(set_url('warehouse/warehouses'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function create_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->template->add_js('jquery.wh_create_sale', 'modules_js/warehouse');
			$this->template->add_js('highslide.min', 'highslide');
			$this->template->add_css('highslide', 'highslide');
			$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
			$this->load->model('warehouse/mwarehouses_products');
			if(!$this->mwarehouses_products->create_sale($wh_id))
			{
				$this->massages->add_error_massage('Возникли ошибки про создании продажи!');
				$this->_redirect(set_url('warehouse/warehouses'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function ajax_get_wh_pr_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->get_wh_pr_grid($wh_id))
			{
				echo $data;
			}
		}
	}
	
	public function ajax_get_create_sale_wh_pr_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->get_create_sale_wh_pr_grid($wh_id))
			{
				if($this->input->post('ajax'))
				{
					echo $data;
				}
				else
				{
					$data = $this->load->view('warehouse/create_sale_wh_pr_grid', array('html_data' => $data), TRUE);
					echo json_encode(array('success' => 1, 'html' => $data));
				}
			}
		}
	}
	
	public function ajax_get_pr_to_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->get_pr_to_sale($wh_id, $pr_id))
			{
				$html = $this->load->view('warehouse/view_pr_to_sale', $data, TRUE).$this->load->view('warehouse/view_pr_to_sale_js', array(), TRUE);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
		}
	}
	
	public function ajax_add_pr_to_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->add_pr_to_sale($wh_id, $pr_id))
			{
				echo json_encode(array('success' => 1, 'massage' => 'Продукт добавлен', 'products' => $data['grid']));
			}
			else
			{
				echo json_encode(array('success' => 0, 'massage' => 'Возникли ошибки при добавлении продукта'));
			}
		}
	}
	
	public function ajax_delete_pr_from_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['row_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->delete_pr_from_sale($wh_id, $URI['row_id']))
			{
				echo json_encode(array('success' => 1, 'massage' => 'Продукт добавлен', 'products' => $data['grid']));
			}
			else
			{
				echo json_encode(array('success' => 0, 'massage' => 'Возникли ошибки при добавлении продукта'));
			}
		}
	}
	
	public function ajax_view_edit_pr_sale_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['row_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->view_edit_pr_sale_qty($wh_id, $URI['row_id']))
			{
				$html = $this->load->view('warehouse/view_pr_to_sale', $data, TRUE).$this->load->view('warehouse/view_pr_to_sale_js', array(), TRUE);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
			else
			{
				echo json_encode(array('success' => 0, 'massage' => 'Возникли ошибки при редактировании!'));
			}
		}
	}
	
	public function save_wh_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($this->mwarehouses_products->save_wh_sale($wh_id))
			{
				$this->massages->add_success_massage('Продажа успешно создана!');
				$this->_redirect(set_url('*/warehouses_logs/sales_grid/wh_id/'.$wh_id));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки про создании переноса!');
				$this->_redirect(set_url('*/warehouses_products/create_sale/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function create_transfer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($this->mwarehouses_products->create_transfer($wh_id))
			{
				$this->template->add_js('jquery.wh_create_transfer', 'modules_js/warehouse');
				$this->template->add_js('highslide.min', 'highslide');
				$this->template->add_css('highslide', 'highslide');
				$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки про создании продажи!');
				$this->_redirect(set_url('*/warehouses_products/create_sale/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function ajax_get_transfer_wh_pr_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->get_transfer_wh_pr_grid($wh_id))
			{
				if($this->input->post('ajax'))
				{
					echo $data;
				}
				else
				{
					$data = $this->load->view('warehouse/create_transfer_wh_pr_grid', array('html_data' => $data), TRUE);
					echo json_encode(array('success' => 1, 'html' => $data));
				}
			}
			else
			{
				echo json_encode(array('success' => 0, 'massage' => 'Возникли ошибки при добавлении продукта'));
			}
		}
	}
	
	public function ajax_get_pr_to_transfer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->get_pr_to_transfer($wh_id, $pr_id))
			{
				$html = $this->load->view('warehouse/view_pr_to_transfer', $data, TRUE).$this->load->view('warehouse/view_pr_to_transfer_js', array(), TRUE);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
		}
	}
	
	public function ajax_add_pr_to_transfer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->add_pr_to_transfer($wh_id, $pr_id))
			{
				echo json_encode(array('success' => 1, 'massage' => 'Продукт добавлен', 'products' => $data['grid']));
			}
			else
			{
				echo json_encode(array('success' => 0, 'massage' => 'Возникли ошибки при добавлении продукта'));
			}
		}
	}
	
	public function ajax_delete_pr_from_transfer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['row_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->delete_pr_from_transfer($wh_id, $URI['row_id']))
			{
				echo json_encode(array('success' => 1, 'massage' => 'Продукт добавлен', 'products' => $data['grid']));
			}
			else
			{
				echo json_encode(array('success' => 0, 'massage' => 'Возникли ошибки при добавлении продукта'));
			}
		}
	}
	
	public function ajax_view_edit_pr_transfer_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['row_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($data = $this->mwarehouses_products->view_edit_pr_transfer_qty($wh_id, $URI['row_id']))
			{
				$html = $this->load->view('warehouse/view_pr_to_transfer', $data, TRUE).$this->load->view('warehouse/view_pr_to_transfer_js', array(), TRUE);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
			else
			{
				echo json_encode(array('success' => 0, 'massage' => 'Возникли ошибки при редактировании!'));
			}
		}
	}
	
	public function save_wh_transfer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($this->mwarehouses_products->save_wh_transfer($wh_id))
			{
				$this->massages->add_success_massage('Перенос успешно произведен!');
				$this->_redirect(set_url('*/warehouses_logs/transfers_grid/wh_id/'.$wh_id));
			}
			else
			{
				$this->massages->add_error_massage('Возникли ошибки про создании переноса!');
				$this->_redirect(set_url('*/warehouses_products/create_transfer/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
}
?>