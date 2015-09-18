<?php
class Warehouses_logs extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->template->add_title('Склад | Логи');
		$this->template->add_navigation('Склад')->add_navigation('Логи', set_url('*/warehouses_logs'));
		$this->load->model('mwarehouses_logs');
		$this->mwarehouses_logs->render_logs_grid();
	}
	
	public function sales_logs()
	{
		$this->template->add_js('jquery.view_wh_sale', 'modules_js/warehouse');
		$this->template->add_title('Склад | Логи | Логи продаж');
		$this->template->add_navigation('Склад')->add_navigation('Логи', set_url('*/warehouses_logs'))->add_navigation('Логи продаж');
		$this->load->model('mwarehouses_logs');
		$this->mwarehouses_logs->render_sales_logs_grid();
		if(!$this->input->post('ajax'))
		{
			$this->template->add_template('warehouse/wh_sales_reports_grid_js', array('wh_sales_id' => '#sales_logs_grid'));
		}
	}
	
	public function transfers_logs()
	{
		$this->template->add_js('jquery.view_wh_transfer', 'modules_js/warehouse');
		$this->template->add_title('Склад | Логи | Логи переносов');
		$this->template->add_navigation('Склад')->add_navigation('Логи', set_url('*/warehouses_logs'))->add_navigation('Логи переносов');
		$this->load->model('mwarehouses_logs');
		
		$this->mwarehouses_logs->render_transfers_logs_grid();
		if(!$this->input->post('ajax'))
		{
			$this->template->add_template('warehouse/wh_transfers_grid_js', array('wh_transfers_id' => '#transfers_logs_grid'));
		}
	}
	
	public function edit_pr_logs()
	{
		//$this->template->add_js('jquery.view_wh_transfer', 'modules_js/warehouse');
		$this->template->add_title('Склад | Логи | Количество добавлено');
		$this->template->add_navigation('Склад')->add_navigation('Логи', set_url('*/warehouses_logs'))->add_navigation('Количество добавлено');
		$this->load->model('mwarehouses_logs');
		
		$this->mwarehouses_logs->render_edit_pr_logs_grid();
		/*if(!$this->input->post('ajax'))
		{
			$this->template->add_template('warehouse/wh_transfers_grid_js', array('wh_transfers_id' => '#transfers_logs_grid'));
		}*/
	}
	
	public function reject_pr_logs()
	{
		$this->template->add_title('Склад | Логи | Списан');
		$this->template->add_navigation('Склад')->add_navigation('Логи', set_url('*/warehouses_logs'))->add_navigation('Списан');
		$this->load->model('mwarehouses_logs');
		
		$this->mwarehouses_logs->render_reject_pr_logs_grid();
	}
	
	public function add_pr_logs()
	{
	
	}
	
	public function delete_pr_logs()
	{
	
	}
	
	public function view_log()
	{
		
	}
	
	public function ajax_view_log()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && isset($URI['log_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($log_id = intval($URI['log_id']))>0)
		{
			$this->load->model('mwarehouses_logs');
			if($html = $this->mwarehouses_logs->view_log($wh_id, $log_id))
			{
				echo json_encode(array('success' => 1, 'html' => $html));
			}
		}
	}
	
	public function sales_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->template->add_js('jquery.view_wh_sale', 'modules_js/warehouse');
			$this->template->add_title('Склад | Список складов');
			$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('*/warehouses'));
		
			$this->load->model('mwarehouses_logs');
			if($this->mwarehouses_logs->get_sales_grid($wh_id))
			{
				if(!$this->input->post('ajax'))
				{
					$this->template->add_template('warehouse/wh_sales_grid_js', array());
				}
			}
			else
			{
				$this->massages->add_error_massage('Запись отсутствует! Просмотр невозможен!!');
				$this->_redirect(set_url('*/warehouses'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутствует! Просмотр невозможен!');
			$this->_redirect(set_url('*/warehouses'));
		}
	}
	
	public function sales_reports()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->template->add_js('jquery.view_wh_sale', 'modules_js/warehouse');
			$this->template->add_title('Склад | Список складов');
			$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('*/warehouses'));
		
			$this->load->model('mwarehouses_logs');
			if($this->mwarehouses_logs->get_sales_reports($wh_id))
			{
				$this->template->add_template('warehouse/wh_sales_reports_grid_js', array());
			}
			else
			{
				$this->massages->add_error_massage('Запись отсутствует! Просмотр невозможен!');
				$this->_redirect(set_url('*/warehouses'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутствует! Просмотр невозможен!');
			$this->_redirect(set_url('*/warehouses'));
		}
	}
	
	public function ajax_view_sales()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && isset($URI['log_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($log_id = intval($URI['log_id']))>0)
		{
			$this->load->model('mwarehouses_logs');
			if($data = $this->mwarehouses_logs->view_sale($wh_id, $log_id))
			{
				$html = $this->load->view('warehouse/wh_sales_view_sale', $data, TRUE);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
		}
	}
	
	public function ajax_view_sales_pr()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && isset($URI['log_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($log_id = intval($URI['log_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('mwarehouses_logs');
			if($data = $this->mwarehouses_logs->view_sale_pr($wh_id, $log_id, $pr_id))
			{
				$html = $this->load->view('warehouse/wh_view_sales_pr_detail', $data, TRUE);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
		}
	}
	
	public function transfers_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && ($wh_id = intval($URI['wh_id']))>0)
		{
			$this->template->add_js('jquery.view_wh_transfer', 'modules_js/warehouse');
			$this->template->add_title('Склад | Список складов');
			$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('*/warehouses'));
		
			$this->load->model('mwarehouses_logs');
			if($this->mwarehouses_logs->get_transfers_grid($wh_id))
			{
				if(!$this->input->post('ajax'))
				{
					$this->template->add_template('warehouse/wh_transfers_grid_js', array());
				}
			}
			else
			{
				$this->massages->add_error_massage('Запись отсутствует! Просмотр невозможен!!');
				$this->_redirect(set_url('*/warehouses'));
			}
		}
		else
		{
			$this->massages->add_error_massage('Параметр ID отсутствует! Просмотр невозможен!');
			$this->_redirect(set_url('*/warehouses'));
		}
	}
	
	public function ajax_view_transfer()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && isset($URI['log_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($log_id = intval($URI['log_id']))>0)
		{
			$this->load->model('mwarehouses_logs');
			if($data = $this->mwarehouses_logs->view_transfer($wh_id, $log_id))
			{
				$html = $this->load->view('warehouse/wh_transfers_view_transfer', $data, TRUE);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
		}
	}
	
	public function ajax_view_transfer_pr()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['wh_id']) && isset($URI['log_id']) && isset($URI['pr_id']) && ($wh_id = intval($URI['wh_id']))>0 && ($log_id = intval($URI['log_id']))>0 && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('mwarehouses_logs');
			if($data = $this->mwarehouses_logs->view_transfer_pr($wh_id, $log_id, $pr_id))
			{
				$html = $this->load->view('warehouse/wh_view_transfer_pr_detail', $data, TRUE);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
		}
	}
}