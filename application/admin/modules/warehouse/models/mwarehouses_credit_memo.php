<?php
class Mwarehouses_credit_memo extends AG_Model
{
	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';

	const WH_PR 		= 'wh_products';
	const ID_WH_PR 		= 'id_wh_products';

	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';

	const ORD 		= 'm_orders';
	const ID_ORD 	= 'id_m_orders';

	const WH_LOGS		= 'wh_log';
	const ID_WH_LOGS	= 'id_wh_log';
	const WH_SALES			= 'wh_log_sale';
	const ID_WH_SALES		= 'id_wh_log_sale';
	const WH_SALES_ADDR		= 'wh_log_sale_address';
	const WH_SALES_PR		= 'wh_log_sale_products';
	const ID_WH_SALES_PR	= 'id_wh_log_sale_products';
	const WH_SALES_PR_ATTR	= 'wh_log_sale_products_attributes';

	const WH_INV		= 'wh_log_sale_invoice';
	const ID_WH_INV		= 'id_wh_log_sale_invoice';

	const WH_SHP		= 'wh_log_sale_shipping';
	const ID_WH_SHP		= 'id_wh_log_sale_shipping';

	const WH_CM		= 'wh_log_sale_credit_memo';
	const ID_WH_CM	= 'id_wh_log_sale_credit_memo';

	public function render_wh_credit_memo_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("wh_sales_credit_memo_grid", array('sort' => self::ID_WH_CM, 'desc' => 'DESC'));

		$this->grid->db
			->select("CM.`".self::ID_WH_CM."` AS ID, CM.`wh_credit_memo_number`, SALE.`wh_sale_number`, INV.`wh_invoice_number`, SHP.`wh_shipping_number`, CM.`admin_note`, CM.`create_date`, SALE.`total_qty`, CONCAT(SALE.`total`, ' ', SALE.`base_currency_name`) AS total, WH.`alias` as `wh_alias`")
			->from("`".self::WH_CM."` AS CM")
			->join(	"`".self::WH_SALES."` AS SALE",
				"SALE.`".self::ID_WH_SALES."` = CM.`".self::ID_WH_SALES."`",
				"LEFT")
			->join(	"`".self::WH_INV."` AS INV",
				"INV.`".self::ID_WH_INV."` = CM.`".self::ID_WH_INV."`",
				"LEFT")
			->join(	"`".self::WH_SHP."` AS SHP",
				"SHP.`".self::ID_WH_SHP."` = CM.`".self::ID_WH_SHP."`",
				"LEFT")
			->join(	"`".self::WH."` AS WH",
				"WH.`".self::ID_WH."` = SALE.`".self::ID_WH."`",
				"LEFT")
			->where("CM.`".self::ID_USERS."`", $this->id_users);

		$this->load->helper("warehouses_credit_memo");
		helper_wh_credit_memo_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->render_grid();
	}

	public function get_credit_memo($cm_id)
	{
		$this->db->select("*")
				 ->from("`".self::WH_CM."`")
				 ->where("`".self::ID_WH_CM."`", $cm_id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($CM = $this->db->get()->row_array()) == 0) return FALSE;
		return $CM;
	}

	public function create_wh_sale_credit_memo($sale_id, $admin_note = NULL)
	{
		$sale_id = intval($sale_id);
		if($sale_id <= 0) return FALSE;

		$this->load->model('warehouse/mwarehouses_sales');
		$this->load->model('warehouse/mwarehouses_invoices');
		$this->load->model('warehouse/mwarehouses_shippings');
		$sale = $this->mwarehouses_sales->get_sale($sale_id);
		$inv = $this->mwarehouses_invoices->get_sale_invoice($sale_id);
		$shp = $this->mwarehouses_shippings->get_sale_shipping($sale_id);

		$this->db->select("MAX(wh_credit_memo_number) AS wh_credit_memo_number")
				 ->from("`".self::WH_CM."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$credit_memo_number = $this->db->get()->row_array();
		$max = 1;
		if(count($credit_memo_number)>0)
		{
			$max = intval($credit_memo_number['wh_credit_memo_number']) + 1;
		}
		$credit_memo_number = str_repeat("0", 8-strlen($max)).($max);

		$cm_data[self::ID_WH_SALES] = $sale_id;
		$cm_data[self::ID_WH_INV] = $inv[self::ID_WH_INV];
		if($shp) $cm_data[self::ID_WH_SHP] = $shp[self::ID_WH_SHP];
		$cm_data['wh_credit_memo_number'] = $credit_memo_number;
		$cm_data['admin_note'] = $admin_note;

		$this->load->model('warehouse/mwarehouses_logs');
		$log_id = $this->mwarehouses_logs->add_log($sale[self::ID_WH], 'CREDIT_MEMO', $admin_note);

		$cm_id = $this->sql_add_data($cm_data + array(self::ID_WH_LOGS => $log_id))->sql_update_date()->sql_using_user()->sql_save(self::WH_CM);
		$this->sql_add_data(array('wh_sale_state' => 'CM'))->sql_update_date()->sql_using_user()->sql_save(self::WH_SALES, $sale_id);
		$this->sql_add_data(array('wh_invoice_state' => 'CM'))->sql_update_date()->sql_using_user()->sql_save(self::WH_INV, $inv[self::ID_WH_INV]);
		if($shp) $this->sql_add_data(array('wh_shipping_state' => 'CM'))->sql_update_date()->sql_using_user()->sql_save(self::WH_SHP, $shp[self::ID_WH_SHP]);

		return $cm_id;
	}

	public function isset_wh_cm($cm_id)
	{
		$cm_id = intval($cm_id);
		$this->db->select("COUNT(*) AS COUNT")
				 ->from("`".self::WH_CM."`")
				 ->where("`".self::ID_WH_CM."`", $cm_id)->where("`".self::ID_USERS."`", $this->id_users);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] == 1) return TRUE;
		return FALSE;
	}
}
?>