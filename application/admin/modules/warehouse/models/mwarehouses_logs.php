<?php
class Mwarehouses_logs extends AG_Model
{
	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';
	const WH_SH 		= 'wh_shops';
	const ID_WH_SH 		= 'id_wh_shops';
	const WHNSH			= 'wh_whNshops';
	
	const WH_PR 		= 'wh_products';
	const ID_WH_PR 		= 'id_wh_products';
	
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	
	const WH_LOG		= 'wh_log';
	const ID_WH_LOG		= 'id_wh_log';
	const WH_LOG_PR		= 'wh_log_products';
	const ID_WH_LOG_PR	= 'id_wh_log_products';
	
	const WH_LOG_SALE 		= 'wh_log_sale';
	const ID_WH_LOG_SALE 	= 'id_wh_log_sale';
	const WH_LOG_SALE_PR	= 'wh_log_sale_pr';
	const ID_WH_LOG_SALE_PR	= 'id_wh_log_sale_pr';
	
	const WH_LOG_TRANSFER 		= 'wh_log_transfer';
	const ID_WH_LOG_TRANSFER 	= 'id_wh_log_transfer';
	const WH_LOG_TRANSFER_PR	= 'wh_log_transfer_pr';
	const ID_WH_LOG_TRANSFER_PR	= 'id_wh_log_transfer_pr';
	
	const SALE_REPORT_FORM_ID = 'warehouses_sale_report_form';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_log_types_collection()
	{
		return array('SALE' => 'Продажа', 'CREDIT_MEMO' => 'Возврат', 'TRANSFER' => 'Перенос продуктов', 'EDIT_PR' => 'Количество добавлено', 'REJECT_PR' => 'Списан', 'ADD_PR' => 'Продукт добавлен', 'DELETE_PR' => 'Продукт Удален');
	}
	
	public function render_logs_grid()
	{
		$wh_array = array();
		$this->db->select("`".self::ID_WH."`, `alias`")
				->from("`".self::WH."`")
				->where("`".self::ID_USERS."`", $this->id_users)->order_by("`sort`");
		foreach($this->db->get()->result_array() as $ms)
		{
			$wh_array[$ms[self::ID_WH]] = $ms['alias'];
		}

		$this->load->library('grid');
		$this->grid->_init_grid('wh_logs_grid', array('sort' => "A.`".self::ID_WH_LOG."`", 'desc' => 'DESC'));
		$this->grid->db
			->select("`".self::ID_WH_LOG."` AS ID, `wh_log_number`, `comment`, `create_date`, `type`, `".self::ID_WH."`")
			->from("`".self::WH_LOG."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		
		$this->load->helper('warehouses_logs');
		helper_logs_grid_build($this->grid, $wh_array, $this->get_log_types_collection());
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('type', $this->get_log_types_collection());
		$this->grid->update_grid_data(self::ID_WH, $wh_array);
		$this->grid->render_grid();
	}
	
	public function render_edit_pr_logs_grid()
	{
		$wh_array = array();
		$this->db->select("`".self::ID_WH."`, `alias`")
				->from("`".self::WH."`")
				->where("`".self::ID_USERS."`", $this->id_users)->order_by("`sort`");
		foreach($this->db->get()->result_array() as $ms)
		{
			$wh_array[$ms[self::ID_WH]] = $ms['alias'];
		}
		
		$this->load->library('grid');
		$this->grid->_init_grid('edit_pr_logs_grid', array('sort' => "A.`".self::ID_WH_LOG."`", 'desc' => 'DESC'));
		$this->grid->db
			->select("A.`".self::ID_WH_LOG."` AS ID, A.`comment`, A.`create_date`, A.`type`, A.`".self::ID_WH."`, B.`".self::ID_PR."` AS PR_ID, B.`sku`, B.`qty`")
			->from("`".self::WH_LOG."` AS A")
			->join("`".self::WH_LOG_PR."` AS B",
					"B.`".self::ID_WH_LOG."` = A.`".self::ID_WH_LOG."`",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`type`", "EDIT_PR");
		
		$this->load->helper('warehouses_logs');
		helper_edit_pr_logs_grid_build($this->grid, $wh_array);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data(self::ID_WH, $wh_array);
		$this->grid->render_grid();
	}
	
	public function render_reject_pr_logs_grid()
	{
		$wh_array = array();
		$this->db->select("`".self::ID_WH."`, `alias`")
				->from("`".self::WH."`")
				->where("`".self::ID_USERS."`", $this->id_users)->order_by("`sort`");
		foreach($this->db->get()->result_array() as $ms)
		{
			$wh_array[$ms[self::ID_WH]] = $ms['alias'];
		}
		
		$this->load->library('grid');
		$this->grid->_init_grid('wh_logs_grid');
		$this->grid->db
			->select("A.`".self::ID_WH_LOG."` AS ID, A.`comment`, A.`create_date`, A.`type`, A.`".self::ID_WH."`, B.`".self::ID_PR."` AS PR_ID, B.`sku`, B.`qty`")
			->from("`".self::WH_LOG."` AS A")
			->join("`".self::WH_LOG_PR."` AS B",
					"B.`".self::ID_WH_LOG."` = A.`".self::ID_WH_LOG."`",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("`type`", "REJECT_PR");
		
		$this->load->helper('warehouses_logs');
		helper_reject_pr_logs_grid_build($this->grid, $wh_array);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data(self::ID_WH, $wh_array);
		$this->grid->render_grid();
	}
	
	public function render_add_pr_logs_grid()
	{
		$wh_array = array();
		$this->db->select("`".self::ID_WH."`, `alias`")
				->from("`".self::WH."`")
				->where("`".self::ID_USERS."`", $this->id_users)->order_by("`sort`");
		foreach($this->db->get()->result_array() as $ms)
		{
			$wh_array[$ms[self::ID_WH]] = $ms['alias'];
		}
		
		$this->load->library('grid');
		$this->grid->_init_grid('wh_logs_grid');
		$this->grid->db
			->select("`".self::ID_WH_LOG."` AS ID, `comment`, `create_date`, `type`, `".self::ID_WH."`")
			->from("`".self::WH_LOG."` AS A")
			->join("`".self::WH_LOG_PR."` AS B",
					"B.`".self::ID_WH_LOG."` = A.`".self::ID_WH_LOG."`",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("`type`", "ADD_PR");
		
		$this->load->helper('warehouses_logs');
		helper_add_pr_logs_grid_build($this->grid, $wh_array);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('type', $this->get_log_types_collection());
		$this->grid->update_grid_data(self::ID_WH, $wh_array);
		$this->grid->render_grid();
	}
	
	public function render_delete_pr_logs_grid()
	{
		$wh_array = array();
		$this->db->select("`".self::ID_WH."`, `alias`")
				->from("`".self::WH."`")
				->where("`".self::ID_USERS."`", $this->id_users)->order_by("`sort`");
		foreach($this->db->get()->result_array() as $ms)
		{
			$wh_array[$ms[self::ID_WH]] = $ms['alias'];
		}
		
		$this->load->library('grid');
		$this->grid->_init_grid('wh_logs_grid');
		$this->grid->db
			->select("`".self::ID_WH_LOG."` AS ID, `comment`, `create_date`, `type`, `".self::ID_WH."`")
			->from("`".self::WH_LOG."` AS A")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("`type`", "DELETE_PR");;
		
		$this->load->helper('warehouses_logs');
		helper_delete_pr_logs_grid_build($this->grid, $wh_array);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('type', $this->get_log_types_collection());
		$this->grid->update_grid_data(self::ID_WH, $wh_array);
		$this->grid->render_grid();
	}
	
	/*public function view_log($wh_id, $log_id)
	{
		$query = $this->db->select("`".self::ID_WH_LOG."`, `type`")
				->from("`".self::WH_LOG."`")
				->where("`".self::ID_WH_LOG."`", $log_id)->where("`".self::ID_WH."`", $wh_id)->limit(1);
		$result = $query->get()->row_array();
		if(count($result) == 0) return FALSE;
		
		$html = FALSE;
		$data = array();
		switch($result['type'])
		{
			case "SALE":
				$data = $this->view_sale($wh_id, $log_id);
				$html = $this->load->view('warehouse/wh_sales_view_sale', $data, TRUE);
			break;
			case "TRANSFER":
				$data = $this->view_transfer($wh_id, $log_id);
				$html = $this->load->view('warehouse/wh_transfers_view_transfer', $data, TRUE);
			break;
		}
		
		return $html;
	}*/
	public function add_log($wh_id, $type, $comment = NULL)
	{
		$log_data = array();
		$this->db->select("MAX(wh_log_number) AS wh_log_number")
				 ->from("`".self::WH_LOG."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$log_number = $this->db->get()->row_array();
		$max = 1;
		if(count($log_number)>0)
		{
			$max = intval($log_number['wh_log_number']) + 1;
		}
		$max = str_repeat("0", 9-strlen($max)).($max);
		$log_data['wh_log_number'] = $max;
		$log_data[self::ID_WH] = $wh_id;
		$log_data['type'] = $type;
		$log_data['comment'] = $comment;
		$id = $this->sql_add_data($log_data)->sql_update_date()->sql_using_user()->sql_save(self::WH_LOG);
		return $id;
	}

	public function create_add_pr_log($wh_id, $pr_id, $sku)
	{
		$id = $this->add_log($wh_id, 'ADD_PR', 'SKU: '.$sku);
		$this->sql_add_data(array(self::ID_WH_LOG => $id, self::ID_PR => $pr_id, 'sku' => $sku, 'qty' => 0))->sql_save(self::WH_LOG_PR);
	}
	
	public function create_edit_pr_log($wh_id, $pr_id, $sku, $qty, $was_qty = FALSE)
	{
		$comment = array();
		if($was_qty !== FALSE)
		{
			$comment = 'SKU: '.$sku.', qty: '.$was_qty.' + '.($qty-$was_qty).' = '.$qty;
		}
		$id = $this->add_log($wh_id, 'EDIT_PR', $comment);
		$this->sql_add_data(array(self::ID_WH_LOG => $id, self::ID_PR => $pr_id, 'sku' => $sku, 'qty' => $qty-$was_qty))->sql_save(self::WH_LOG_PR);
	}
	
	public function create_reject_pr_log($wh_id, $pr_id, $sku, $qty, $was_qty, $c)
	{
		$u_comment = '';
		if(trim($c) != '')
		{
			$u_comment = '<br>'.$c;
		}
		$comment = 'SKU: '.$sku.', qty: '.$was_qty.' - '.($was_qty-$qty).' = '.$qty.$u_comment;
		$id = $this->add_log($wh_id, 'REJECT_PR', $comment);
		$this->sql_add_data(array(self::ID_WH_LOG => $id, self::ID_PR => $pr_id, 'sku' => $sku, 'qty' => $was_qty-$qty))->sql_save(self::WH_LOG_PR);
	}
	
	public function create_delete_pr_log($wh_id, $pr_id, $sku)
	{
		$id = $this->add_log($wh_id, 'DELETE_PR', 'SKU: '.$sku);
		$this->sql_add_data(array(self::ID_WH_LOG => $id, self::ID_PR => $pr_id, 'sku' => $sku))->sql_save(self::WH_LOG_PR);
	}
	
	/*public function create_sale_log($wh_id, $sale_array, $pr_array)
	{
		$comment = NULL;
		if(($POST = $this->input->post('wh_log')) && isset($POST['comment']))
		{
			$comment = $POST['comment'];
		}
		$id = $this->sql_add_data(array(self::ID_WH => $wh_id, 'type' => 'SALE', 'comment' => $comment))->sql_update_date()->sql_using_user()->sql_save(self::WH_LOG);
			
			$query = $this->db->select("MAX(sales_number) AS sales_number")
						->from("`".self::WH_LOG_SALE."`")
						->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
			$sales_number = $query->get()->row_array();
			$max = 1;
			if(count($sales_number)>0)
			{
				$max = intval($sales_number['sales_number']) + 1;
			}
			$sales_number = str_repeat("0", 8-strlen($max)).($max);
			
		$id_sale = $this->sql_add_data(array(self::ID_WH_LOG => $id, 'sales_number' => $sales_number)+$sale_array)->sql_using_user()->sql_save(self::WH_LOG_SALE);
		foreach($pr_array as $ms)
		{
			$data = array(self::ID_WH_LOG_SALE => $id_sale, self::ID_PR => $ms[self::ID_PR], 'sku' => $ms['sku'], 'qty' => $ms['qty'], 'price' => $ms['price'], 'total' => $ms['total']);
			$this->sql_add_data($data)->sql_save(self::WH_LOG_SALE_PR);
		}
	}*/

	/*public function create_credit_memo_log($wh_id, $credit_memo_array, $pr_array)
	{
		$comment = NULL;
		$id = $this->sql_add_data(array(self::ID_WH => $wh_id, 'type' => 'CREDIT_MEMO', 'comment' => $comment))->sql_update_date()->sql_using_user()->sql_save(self::WH_LOG);

		$query = $this->db->select("MAX(sales_number) AS sales_number")
						  ->from("`".self::WH_LOG_SALE."`")
						  ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$sales_number = $query->get()->row_array();
		$max = 1;
		if(count($sales_number)>0)
		{
			$max = intval($sales_number['sales_number']) + 1;
		}
		$sales_number = str_repeat("0", 8-strlen($max)).($max);

		$id_sale = $this->sql_add_data(array(self::ID_WH_LOG => $id, 'sales_number' => $sales_number)+$credit_memo_array)->sql_using_user()->sql_save(self::WH_LOG_SALE);
		foreach($pr_array as $ms)
		{
			$data = array(self::ID_WH_LOG_SALE => $id_sale, self::ID_PR => $ms[self::ID_PR], 'sku' => $ms['sku'], 'qty' => $ms['qty'], 'price' => $ms['price'], 'total' => $ms['total']);
			$this->sql_add_data($data)->sql_save(self::WH_LOG_SALE_PR);
		}
	}*/
	/*
	public function create_transfer_log($wh_id, $transfer_array, $pr_array, $comment = NULL)
	{
		$id = $this->sql_add_data(array(self::ID_WH => $wh_id, 'type' => 'TRANSFER', 'comment' => $comment))->sql_update_date()->sql_using_user()->sql_save(self::WH_LOG);
			
			$query = $this->db->select("MAX(transfers_number) AS transfers_number")
						->from("`".self::WH_LOG_TRANSFER."`")
						->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
			$transfers_number = $query->get()->row_array();
			$max = 1;
			if(count($transfers_number)>0)
			{
				$max = intval($transfers_number['transfers_number']) + 1;
			}
			$max = str_repeat("0", 8-strlen($max)).($max);
			
		$id_transfer = $this->sql_add_data(array(self::ID_WH_LOG => $id, 'transfers_number' => $max) + $transfer_array)->sql_using_user()->sql_save(self::WH_LOG_TRANSFER);
		foreach($pr_array as $ms)
		{
			$this->sql_add_data(array(self::ID_WH_LOG_TRANSFER => $id_transfer)+$ms)->sql_save(self::WH_LOG_TRANSFER_PR);
		}
	}*/
	
	public function get_sales_reports($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$wh = $this->mwarehouses->get_wh($wh_id);
		
		$this->template->add_title(' | '.$wh['alias'].' | Отчеты по продажам');
		$this->template->add_navigation($wh['alias'], set_url('*/warehouses/wh_actions/wh_id/'.$wh[self::ID_WH]))->add_navigation('Список продаж', set_url('*/warehouses_logs/sales_grid/wh_id/'.$wh[self::ID_WH]))->add_navigation('Отчеты по продажам');
		
		$data['sales_grid'] = NULL;
		if($date = $this->input->post('date'))
		{
			$data['date'] = $this->input->post('date');
			$this->load->library('nosql_grid');
			$this->nosql_grid->_init_grid('wh_logs_sales_reports_grid');
			$this->nosql_grid->init_fixed_buttons(FALSE);
			
			$this->db->select("A.`".self::ID_WH_LOG."` AS ID, A.`comment`, A.`create_date`, B.`sales_number`, B.`total_qty`, B.`total`")
					->from("`".self::WH_LOG."` AS A")
					->join("`".self::WH_LOG_SALE."` AS B",
							"B.`".self::ID_WH_LOG."` = A.`".self::ID_WH_LOG."`",
							"LEFT")
					->where("A.`".self::ID_WH."`", $wh_id)->where("A.`type`", 'SALE')->where("DATE(A.`create_date`) >=" , $date['date_from'])->where("DATE(A.`create_date`) <= ", $date['date_to'])->order_by("A.`".self::ID_WH_LOG."`", 'DESC');
			$sales_array = $this->db->get()->result_array();
			
			$data['total_qty'] = 0;
			$data['total_sum'] = 0;
			
			foreach($sales_array as $ms)
			{
				$data['total_qty'] += $ms['total_qty'];
				$data['total_sum'] += $ms['total'];
			}
			
			$this->load->helper('warehouses/warehouses_logs_helper');
			helper_sales_reports_grid_build($this->nosql_grid, $wh_id);
			
			$this->nosql_grid->set_grid_data($sales_array);
			$data['sales_grid'] = $this->nosql_grid->render_grid(TRUE);
		}
		
		$this->load->helper('warehouses/warehouses_logs_helper');
		helper_sales_report_form_build($data, $wh_id);
		return TRUE;
	}
	
	public function check_isset_log($log_id)
	{
		$log_id = intval($log_id);
		$this->db->select("COUNT(*) AS COUNT")
				->from("`".self::WH_LOG."`")
				->where("`".self::ID_WH_LOG."`", $log_id);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1)
		{
			return TRUE;
		}
		return FALSE;
	}
}
?>