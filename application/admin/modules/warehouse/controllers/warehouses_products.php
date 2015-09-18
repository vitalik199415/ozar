<?php
class Warehouses_products extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$user = $this->musers->get_user();
		if($user['warehouse'] == 0)
		{
			$this->template->add_template('warehouse/wh_access', array(), '1');
			$this->template->render(TRUE);
		}
	}
	
	public function index()
	{
		$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
		
		$this->load->model('warehouse/mwarehouses_products');
		$this->mwarehouses_products->get_warehouses_all_pr_grid();
		
		if(!$this->input->post('ajax')) $this->template->add_template('catalogue/products/products_grid_js', array('product_grid_id' => 'warehouses_all_products_grid'));
	}

	public function ajax_get_wh_pr_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id'])) > 0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			echo $this->mwarehouses_products->get_wh_pr_grid($wh_id);
		}
	}

	public function ajax_print_wh_pr()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id'])) > 0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($wh_print_data = $this->mwarehouses_products->get_print_wh_pr($wh_id))
			{
				$data = $this->load->view('warehouse/wh_actions_print_pr', array('wh_print_data' => $wh_print_data), TRUE);
				echo json_encode(array('success' => 1, 'html' => $data));			
			}
		}
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
				$this->messages->add_error_message('Возникли ошибки при добавлении продукта!');
				$this->_redirect(set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
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
				$this->messages->add_success_message('Продукт успешно добавлен на склад!');
				$this->_redirect(set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при добавлении продукта!');
				$this->_redirect(set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
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
				$this->messages->add_error_message('Действие невозможно!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
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
				$this->messages->add_success_message('Продукт удачно добавлен!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при добавлении нового продукта!');
				$this->_redirect(set_url('*/warehouses/add_pr_to_wh/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
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
				$this->messages->add_error_message('Возникли ошибки при добавлении количества продукта!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
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
				$this->messages->add_success_message('Количество продукта успешно добавлено!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки про добавлении количества!');
				$this->_redirect(set_url('warehouse/warehouses'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
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
				$this->messages->add_error_message('Возникли ошибки при списании количества продукта!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
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
				$this->messages->add_success_message('Количество продукта успешно списано!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки про списании количества!');
				$this->_redirect(set_url('warehouse/warehouses'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
	
	public function delete_pr()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('warehouse/mwarehouses_products');
			if($this->mwarehouses_products->delete_pr($wh_id, $pr_id))
			{
				$this->messages->add_success_message('Продукт успешно удален со склада!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при удалении продукта! Количество должно быть 0!');
				$this->_redirect(set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}

	public function wh_sales_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$wh_id = intval($URI['wh_id']);
			$this->load->model('warehouse/mwarehouses');
			$wh = $this->mwarehouses->get_wh_to_select();
			if(isset($wh[$wh_id]))
			{
				$this->template->add_title('Склад | Список складов | Склад '.$wh[$wh_id].' | Продажи');
				$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('*/warehouses'))->add_navigation('Склад '.$wh[$wh_id], set_url('*/warehouses/wh_actions/wh_id/'.$wh_id))->add_navigation('Продажи');

				$this->load->model('warehouse/mwarehouses_sales');
				$this->mwarehouses_sales->render_warehouse_sales_grid($wh_id);
			}
		}
	}

	public function wh_sales_view()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0 && isset($URI['sale_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$sale_id = intval($URI['sale_id']);
			$this->load->model('warehouse/mwarehouses');
			$wh = $this->mwarehouses->get_wh_to_select();
			if(isset($wh[$wh_id]))
			{

				$this->template->add_title('Склад | Список складов | Склад '.$wh[$wh_id]);
				$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('*/warehouses'))->add_navigation('Склад '.$wh[$wh_id], set_url('*/warehouses/wh_actions/wh_id/'.$wh_id));

				$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
				$this->template->add_js('jquery.gbc_wh_sales', 'modules_js/warehouse/sales');

				$this->load->model('warehouse/mwarehouses_sales');
				$view_sale_data = $this->mwarehouses_sales->view_sale($wh_id, $sale_id);

				$this->template->add_title('Продажи | Продажа '.$view_sale_data['sale']['wh_sale_number']);
				$this->template->add_navigation('Продажи', set_url('*/warehouses_products/wh_sales_grid/wh_id/'.$wh_id))->add_navigation('Продажа '.$view_sale_data['sale']['wh_sale_number']);

				$this->load->helper('warehouses_sales');
				helper_controller_warehouses_produtcs_form_view_sale($wh_id, $view_sale_data);
			}
		}
	}

	public function create_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$wh_id = intval($URI['wh_id']);
			$this->load->model('warehouse/mwarehouses');
			$wh = $this->mwarehouses->get_wh_to_select();

			if(isset($wh[$wh_id]))
			{
				$this->template->add_title('Склад | Список складов | Склад '.$wh[$wh_id].' | Продажи '.$wh[$wh_id].' | Добавить продажу');
				$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('*/warehouses'))->add_navigation('Склад '.$wh[$wh_id], set_url('*/warehouses/wh_actions/wh_id/'.$wh_id))->add_navigation('Продажи', set_url('*/warehouses_products/wh_sales_grid/wh_id/'.$wh_id))->add_navigation('Добавить продажу');

				$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
				$this->template->add_js('jquery.gbc_wh_sales', 'modules_js/warehouse/sales');

				$this->load->model('warehouse/mwarehouses_sales');
				$add_sale_data = $this->mwarehouses_sales->add_sale($wh_id);
				$this->load->helper('warehouses_sales');
				helper_controller_warehouses_products_form_add_sale($wh_id, $add_sale_data);
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}

	public function save_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$sale_id = 0;
			if(isset($URI['sale_id']))
			{
				$sale_id = intval($URI['sale_id']);
			}
			$this->load->model('warehouse/mwarehouses_sales');
			if($id_sale = $this->mwarehouses_sales->save_sale($wh_id, $sale_id))
			{
				$this->messages->add_success_message('Продажа успешно добавлена!');
				$this->_redirect(set_url('*/warehouses_products/view_sale/wh_id/'.$wh_id.'/sale_id/'.$id_sale));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при создании продажи!');
				$this->_redirect(set_url('*/warehouses_products/create_sale/wh_id/'.$wh_id));
			}
		}
	}

	public function view_sale()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && isset($URI['sale_id']))
		{
			$wh_id = intval($URI['wh_id']);
			$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');

			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['sale_id']) && intval($URI['sale_id'])>0)
			{
				$sale_id = intval($URI['sale_id']);
				$this->load->model('mwarehouses_sales');
				if($view_sale_data = $this->mwarehouses_sales->view_sale($wh_id, $sale_id))
				{
					$this->template->add_title('Склад | Список складов | '.$view_sale_data['sale']['warehouse_alias'].' | Продажа '.$view_sale_data['sale']['wh_sale_number']);
					$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('*/warehouses'))->add_navigation($view_sale_data['sale']['warehouse_alias'], set_url('*/warehouses/wh_actions/wh_id/'.$wh_id))->add_navigation('Продажа '.$view_sale_data['sale']['wh_sale_number']);

					$this->template->add_js('jquery.print_element.min');
					$this->load->helper('warehouses_sales');
					helper_controller_warehouses_produtcs_form_view_sale($wh_id, $view_sale_data);
				}
				else
				{
					$this->messages->add_error_message('Server error!');
					$this->_redirect(set_url('*/*'));
				}
			}
			else
			{
				$this->messages->add_error_message('Параметры отсутствует! Просмотр невозможен!');
				$this->_redirect(set_url('*/*'));
			}
		}
	}

	public function wh_transfers_grid()
	{

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
				$this->messages->add_error_message('Возникли ошибки про создании продажи!');
				$this->_redirect(set_url('*/warehouses_products/create_sale/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
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
				$this->messages->add_success_message('Перенос успешно произведен!');
				$this->_redirect(set_url('*/warehouses_logs/transfers_grid/wh_id/'.$wh_id));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки про создании переноса!');
				$this->_redirect(set_url('*/warehouses_products/create_transfer/wh_id/'.$wh_id));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметры отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('warehouse/warehouses'));
		}
	}
}
?>