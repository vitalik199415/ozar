<?php
class Warehouses_transfers extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Склад');
		$this->template->add_navigation('Склад');
		$user = $this->musers->get_user();
		if($user['warehouse'] == 0)
		{
			$this->template->add_template('warehouse/wh_access', array(), '1');
			$this->template->render(TRUE);
		}
	}

	public function index()
	{
		$this->template->add_title(' | Переносы продуктов');
		$this->template->add_navigation('Переносы продуктов', set_url('*/warehouses_transfers'));
		$this->load->model('warehouse/mwarehouses_transfers');
		$this->mwarehouses_transfers->render_wh_transfers_grid();
	}

	public function prepare_add_transfer()
	{
		$this->template->add_title(' | Создать перенос | Выбор склада');
		$this->template->add_navigation('Создать перенос')->add_navigation('Выбор склада');
		$this->load->model('warehouse/mwarehouses_transfers');
		if(!$this->mwarehouses_transfers->prepare_add_transfer())
		{
			$this->messages->add_error_message('Склады не существует, создание переноса не возможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function add_tranfer_select_wh()
	{
		if(($wh_id_from = $this->input->post('wh_id_from')) && ($wh_id_to = $this->input->post('wh_id_to')))
		{
			$this->load->model('warehouse/mwarehouses');
			$wh = $this->mwarehouses->get_wh_to_select();
			if(isset($wh[$wh_id_from]) && isset($wh[$wh_id_to]) && $wh_id_from != $wh_id_to)
			{
				$this->_redirect(set_url('*/warehouses_transfers/add_transfer/wh_id_from/'.$wh_id_from.'/wh_id_to/'.$wh_id_to));
			}
			else
			{
				$this->messages->add_error_message('Созникли ошибки! Попробуйте снова.');
				$this->_redirect(set_url('*/warehouses_transfers/prepare_add_transfer'));
			}
		}
		else
		{
			$this->messages->add_error_message('Server error!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function add_transfer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id_from']) && isset($URI['wh_id_to']))
		{
			$wh_id_from = intval($URI['wh_id_from']);
			$wh_id_to = intval($URI['wh_id_to']);
			$this->load->model('warehouse/mwarehouses');
			$wh = $this->mwarehouses->get_wh_to_select();

			if(isset($wh[$wh_id_from]) && isset($wh[$wh_id_to]) && $wh_id_from != $wh_id_to)
			{
				$this->template->add_title(' | Переносы | Создать перенос с '.$wh[$wh_id_from].' на '.$wh[$wh_id_to]);
				$this->template->add_navigation('Переносы', set_url('*/*'))->add_navigation('Создать перенос с '.$wh[$wh_id_from].' в '.$wh[$wh_id_to]);

				$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
				$this->template->add_js('jquery.gbc_wh_transfers', 'modules_js/warehouse/transfers');

				$this->load->model('warehouse/mwarehouses_transfers');
				$add_transfer_data = $this->mwarehouses_transfers->add_transfer($wh_id_from, $wh_id_to);
				$this->load->helper('warehouses_transfers');
				helper_controller_warehouses_transfers_form_add_sale($wh_id_from, $wh_id_to, $add_transfer_data);
			}
		}
	}

	public function view_transfer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['tr_id']))
		{
			$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');

			$URI = $this->uri->uri_to_assoc(4);
			$tr_id = intval($URI['tr_id']);
			$this->load->model('warehouse/mwarehouses_transfers');
			if($view_tr_data = $this->mwarehouses_transfers->view_transfer($tr_id))
			{
				$this->template->add_title(' | Переносы | Перенос с '.$view_tr_data['transfer']['warehouse_from_alias'].' в '.$view_tr_data['transfer']['warehouse_to_alias'].' '.$view_tr_data['transfer']['wh_transfer_number']);
				$this->template->add_navigation('Переносы', set_url('*/*'))->add_navigation('Перенос с '.$view_tr_data['transfer']['warehouse_from_alias'].' на '.$view_tr_data['transfer']['warehouse_to_alias'].' '.$view_tr_data['transfer']['wh_transfer_number']);

				$this->template->add_js('jquery.print_element.min');
				$this->load->helper('warehouses_transfers');
				helper_controller_warehouses_transfers_form_view_transfer($view_tr_data);
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

	public function save_transfer()
	{
		$this->load->model('warehouse/mwarehouses_transfers');
		if($tr_id = $this->mwarehouses_transfers->save_transfer())
		{
			$this->messages->add_success_message('Перенос успешно осуществлен!');
			$this->_redirect(set_url('*/*/view_transfer/tr_id/'.$tr_id));
		}
		else
		{
			$this->messages->add_error_message('Процесс добавления невозможен!');
			$this->_redirect(set_url('*/*'));
		}
	}


	public function ajax_get_wh_shop_products_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id_from']))
		{
			$wh_id_from = intval($URI['wh_id_from']);
			$this->load->model('warehouse/mwarehouses_transfers');
			$grid = $this->mwarehouses_transfers->render_wh_shop_products_grid($wh_id_from);
			echo $grid;
		}
	}

	public function ajax_unset_products_temp_data()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id_from']))
		{
			$wh_id_from = intval($URI['wh_id_from']);
			$this->load->model('warehouse/mwarehouses_transfers');
			$this->mwarehouses_transfers->unset_transfer_products_temp($wh_id_from);
			$products_grid = $this->mwarehouses_transfers->render_transfer_products_grid($wh_id_from, TRUE);
			$json = array('success' => 1, 'products' => $products_grid);
			echo json_encode($json);
		}
	}

	public function ajax_get_view_wh_shop_product()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id_from']))
		{
			$wh_id_from = intval($URI['wh_id_from']);
			if(isset($URI['pr_id']) && ($pr_id = intval($URI['pr_id']))>0)
			{
				$this->load->model('warehouse/mwarehouses_transfers');
				if($product_array = $this->mwarehouses_transfers->get_view_product_data($wh_id_from, $pr_id))
				{
					echo json_encode(array('success' => 1, 'html' => $this->load->view('warehouse/transfers/view_product/products_detail', array('PRD_array' => $product_array, 'PRD_ID' => $pr_id, 'wh_id_from' => $wh_id_from, 'PRD_block_id' => 'PRD_block'), TRUE).$this->load->view('warehouse/transfers/view_product/products_detail_js', array(), TRUE).$this->load->view('catalogue/products/view_product/albums_detail_js', array(), TRUE)));
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
	}

	public function ajax_add_product_to_cart()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id_from']))
		{
			$wh_id_from = intval($URI['wh_id_from']);
			$this->load->model('warehouse/mwarehouses_transfers');

			$data = $this->mwarehouses_transfers->add_product_to_transfer($wh_id_from);
			if($data['success'])
			{
				$products_grid = $this->mwarehouses_transfers->render_transfer_products_grid($wh_id_from, TRUE);
				$json = array('success' => 1, 'products' => $products_grid, 'message' => '<p>'.$data['message'].'</p>');
				echo json_encode($json);
			}
			else
			{
				echo json_encode(array('success' => 0, 'message' => '<p>'.$data['message'].'</p>'));
			}
		}
	}

	public function ajax_get_view_edit_product_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id_from']))
		{
			$wh_id_from = intval($URI['wh_id_from']);
			if(isset($URI['tr_pr_id']))
			{
				$tr_pr_id = $URI['tr_pr_id'];
				$this->load->model('warehouse/mwarehouses_transfers');
				if($product_data = $this->mwarehouses_transfers->get_transfer_product_qty($wh_id_from, $tr_pr_id))
				{
					echo json_encode(array('success' => 1, 'html' => $this->load->view('warehouse/transfers/view_transfer_edit_product_qty', $product_data, TRUE)));
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
	}

	public function ajax_transfer_edit_product_qty()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id_from']) && isset($URI['tr_pr_id']))
		{
			$wh_id_from = intval($URI['wh_id_from']);
			$tr_pr_id = $URI['tr_pr_id'];
			$this->load->model('warehouse/mwarehouses_transfers');
			$data = $this->mwarehouses_transfers->edit_transfer_product_qty($wh_id_from, $tr_pr_id);
			if($data['success'])
			{
				$products_grid = $this->mwarehouses_transfers->render_transfer_products_grid($wh_id_from);
				$data['message'] = $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$data['message'].'</p>'), TRUE);
				$json = array('success' => 1, 'products' => $products_grid, 'message' => $data['message']);
				echo json_encode($json);
			}
			else
			{
				$data['message'] = $this->load->view('site_messages/site_messages', array('error_message' => '<p>'.$data['message'].'</p>'), TRUE);
				$json = array('success' => 0, 'message' => $data['message']);
				echo json_encode($json);
			}
		}
	}

	public function ajax_detele_product_from_cart()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id_from']) && isset($URI['tr_pr_id']))
		{
			$wh_id_from = intval($URI['wh_id_from']);
			$tr_pr_id = $URI['tr_pr_id'];
			$this->load->model('warehouse/mwarehouses_transfers');
			$data = $this->mwarehouses_transfers->delete_product_from_transfer($wh_id_from, $tr_pr_id);
			if($data['success'])
			{
				$products_grid = $this->mwarehouses_transfers->render_transfer_products_grid($wh_id_from);
				$data['message'] = $this->load->view('site_messages/site_messages', array('success_message' => '<p>'.$data['message'].'</p>'), TRUE);
				$json = array('success' => 1, 'products' => $products_grid, 'message' => $data['message']);
				echo json_encode($json);
			}
			else
			{
				$data['message'] = $this->load->view('site_messages/site_messages', array('error_message' => '<p>'.$data['message'].'</p>'), TRUE);
				echo json_encode(array('success' => 0, 'message' => $data['message']));
			}
		}
	}
}
?>