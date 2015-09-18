<?php
class Mwarehouses_shippings extends AG_Model
{
	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';
	
	const WH_PR 		= 'wh_products';
	const ID_WH_PR 		= 'id_wh_products';
	
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';

	const WH_LOGS		= 'wh_log';
	const ID_WH_LOGS	= 'id_wh_log';
	const WH_SALES			= 'wh_log_sale';
	const ID_WH_SALES		= 'id_wh_log_sale';
	const WH_SALES_ADDR		= 'wh_log_sale_address';

	const WH_SHP 		= 'wh_log_sale_shipping';
	const ID_WH_SHP 	= 'id_wh_log_sale_shipping';
	
	const PR_ADDEDIT_FORM_ID = 'products_add_edit_from';
	const ADD_PR_QTY_FORM_ID = 'add_pr_qty_form';
	const CREATE_SALE_FORM_ID = 'create_sale_form';
	const CREATE_TRANFER_FORM_ID = 'create_transfer_from';
	
	const create_sale_session = 'wh_create_sale_session';
	
	public function __construct()
	{
		parent::__construct();
	}

	public static function get_wh_shippings_state_collection()
	{
		return array('N' => 'Новый', 'C' => 'Завершен', 'CN' => 'Отменен', 'CM' => 'Возврат');
	}

	public function render_wh_shippings_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("wh_sales_shippings_grid", array('sort' => self::ID_WH_SHP, 'desc' => 'DESC'));
		
		$this->grid->db
			->select("A.`".self::ID_WH_SHP."` AS ID, A.`wh_shipping_number`, A.`wh_shipping_state`, B.`total_qty`, CONCAT(B.`total`, ' ', B.`base_currency_name`) AS total, B.`wh_sale_number`, A.`create_date`, A.`update_date`, C.`alias` as `wh_alias`")
			->from("`".self::WH_SHP."` AS A")
			->join(	"`".self::WH_SALES."` AS B",
					"B.`".self::ID_WH_SALES."` = A.`".self::ID_WH_SALES."`",
					"LEFT")
			->join("`".self::WH_LOGS."` AS LOG",
					"LOG.`".self::ID_WH_LOGS."` = B.`".self::ID_WH_LOGS."`",
					"LEFT")
			->join(	"`".self::WH."` AS C",
					"C.`".self::ID_WH."` = LOG.`".self::ID_WH."`",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$this->load->helper("warehouses_shippings");
		helper_wh_shippings_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('wh_shipping_state', self::get_wh_shippings_state_collection());
		$this->grid->render_grid();
	}

	public function get_shipping($shp_id)
	{
		$this->db->select("*")
				 ->from("`".self::WH_SHP."`")
				 ->where("`".self::ID_WH_SHP."`", $shp_id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($invoice = $this->db->get()->row_array()) == 0) return FALSE;
		return $invoice;
	}

	public function get_sale_shipping($sale_id)
	{
		$this->db->select("*")
				 ->from("`".self::WH_SHP."`")
				 ->where("`".self::ID_WH_SALES."`", $sale_id)->where("`".self::ID_USERS."`", $this->id_users)
				 ->where("wh_shipping_state <>", 'CN')->limit(1);
		if(count($result = $this->db->get()->row_array()) == 0) return FALSE;
		return $result;
	}

	public function create_wh_sale_shipping($sale_id, $data = array())
	{
		$sale_id = intval($sale_id);
		if($sale_id <= 0) return FALSE;
		$this->load->model('warehouse/mwarehouses_sales');
		if($this->get_sale_shipping($sale_id)) return FALSE;

		$this->db->select("MAX(wh_shipping_number) AS wh_shipping_number")
				 ->from("`".self::WH_SHP."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$shipping_number = $this->db->get()->row_array();
		$max = 1;
		if(count($shipping_number)>0)
		{
			$max = intval($shipping_number['wh_shipping_number']) + 1;
		}
		$shipping_number = str_repeat("0", 8-strlen($max)).($max);

		$shp_data[self::ID_WH_SALES] = $sale_id;
		$shp_data['wh_shipping_number'] = $shipping_number;
		$shp_data['wh_shipping_state'] = 'N';
		if(isset($data['wh_shipping_state'])) $shp_data['wh_shipping_state'] = $data['wh_shipping_state'];

		$shp_id = $this->sql_add_data($shp_data)->sql_update_date()->sql_using_user()->sql_save(self::WH_SHP);
		return $shp_id;
	}

	public function change_sale_shipping_state($sale_id, $state)
	{
		if($shipping_data = $this->get_sale_shipping($sale_id))
		{
			$this->sql_add_data(array('wh_shipping_state' => $state))->sql_update_date()->sql_save(self::WH_SHP, $shipping_data[self::ID_WH_SHP]);
			return TRUE;
		}
		return FALSE;
	}
	/*public function create_invoice_wh_shipping($ord_id, $wh_id)
	{
		$this->load->model('sales/morders');
		//$order_data = $this->morders->get_order_data($ord_id);
		$order_products = $this->morders->get_order_products($ord_id);
		foreach($order_products as $key => $ms)
		{
			$order_products[$key]['qty'] = $ms['qty']*$ms['real_qty'];
		}

		$this->save_wh_shipping($ord_id, $wh_id, $order_products);
		return TRUE;
	}*/
	
	/*public function save_wh_shipping($ord_id, $wh_id, $products_array)
	{
		$this->db->select("MAX(wh_shipping_number) AS wh_shipping_number")
				->from("`".self::WH_SHIPPING."`")
				->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$wh_shipping_number = $this->db->get()->row_array();
		$max = 1;
		if(count($wh_shipping_number)>0)
		{
			$max = intval($wh_shipping_number['wh_shipping_number']) + 1;
		}
		$wh_shipping_number = str_repeat("0", 8-strlen($max)).($max);

		$total_qty = 0;
		foreach($products_array as $ms)
		{
			$total_qty += $ms['qty'];
		}
		$sh_array = array(self::ID_ORD => $ord_id, self::ID_WH => $wh_id, 'total_qty' => $total_qty, 'wh_shipping_number' => $wh_shipping_number);
		$sh_id = $this->sql_add_data($sh_array)->sql_update_date()->sql_using_user()->sql_save(self::WH_SHIPPING);
		
		foreach($products_array as $ms)
		{
			$pr_array = array(self::ID_WH_SHIPPING => $sh_id, self::ID_PR => $ms[self::ID_PR], 'sku' => $ms['sku'], 'qty' => $ms['qty']);
			$this->sql_add_data($pr_array)->sql_save(self::WH_SHIPPING_PR);
		}
		
		$this->load->model('warehouse/mwarehouses_products');
		$this->mwarehouses_products->create_wh_shipping($wh_id, $products_array);
		return TRUE;
	}
	
	public function cancel_invoice_wh_shipping($ord_id)
	{
		$this->db->select("`".self::ID_WH_SHIPPING."`, `".self::ID_WH."`")
				->from("`".self::WH_SHIPPING."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_ORD."`", $ord_id)->where("`wh_shipping_state`", 'N')->limit(1);
		$wg_shipping = $this->db->get()->row_array();
		if(count($wg_shipping) > 0)
		{
			$products_array = array();
			$this->db->select("*")
				->from("`".self::WH_SHIPPING_PR."`")
				->where("`".self::ID_WH_SHIPPING."`", $wg_shipping[self::ID_WH_SHIPPING]);
			$result = $this->db->get()->result_array();
			foreach($result as $ms)
			{
				$products_array[] = $ms;
			}
			
			$this->load->model('warehouse/mwarehouses_products');
			$this->mwarehouses_products->cancel_wh_shipping($wg_shipping[self::ID_WH], $products_array);
			
			$this->sql_add_data(array('wh_shipping_state' => 'CN'))->sql_update_date()->sql_save(self::WH_SHIPPING, $wg_shipping[self::ID_WH_SHIPPING]);
			return TRUE;
		}
		return FALSE;
	}
	
	public function create_credit_memo_wh_shipping($ord_id)
	{
		$this->db->select("`".self::ID_WH_SHIPPING."`, `".self::ID_WH."`")
				->from("`".self::WH_SHIPPING."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_ORD."`", $ord_id)->where("`wh_shipping_state`", 'N')->limit(1);
		$wg_shipping = $this->db->get()->row_array();
		if(count($wg_shipping) > 0)
		{
			$products_array = array();
			$this->db->select("*")
					->from("`".self::WH_SHIPPING_PR."`")
					->where("`".self::ID_WH_SHIPPING."`", $wg_shipping[self::ID_WH_SHIPPING]);
			$result = $this->db->get()->result_array();
			foreach($result as $ms)
			{
				$products_array[] = $ms;
			}
			
			$this->load->model('warehouse/mwarehouses_products');
			$this->mwarehouses_products->cancel_wh_shipping($wg_shipping[self::ID_WH], $products_array);
			
			$this->sql_add_data(array('wh_shipping_state' => 'CM'))->sql_update_date()->sql_save(self::WH_SHIPPING, $wg_shipping[self::ID_WH_SHIPPING]);
			return TRUE;
		}
		return FALSE;
	}
	
	public function complete_order_wh_shipping($order_data)
	{
		$this->db->select("`".self::ID_WH_SHIPPING."`, `".self::ID_WH."`")
				->from("`".self::WH_SHIPPING."`")
				->where("`".self::ID_USERS."`", $this->id_users)
				->where("`".self::ID_ORD."`", $order_data['order'][self::ID_ORD])->where("`wh_shipping_state`", 'N')->limit(1);
		$wg_shipping = $this->db->get()->row_array();
		if(count($wg_shipping) > 0)
		{
			$log_sale_total_qty = 0;
			$log_products_array = array();
			foreach($order_data['order_products'] as $ms)
			{
				$log_products_array[] = array(
					self::ID_PR => $ms[self::ID_PR],
					'sku' => $ms['sku'],
					'qty' => $ms['qty']*$ms['real_qty'],
					'price' => $ms['price'],
					'total' => $ms['price']*$ms['qty']);
				$log_sale_total_qty += $ms['qty']*$ms['real_qty'];
			}

			$log_sale_array = array(
				self::ID_ORD => $order_data['order'][self::ID_ORD],
				'total_qty' => $log_sale_total_qty,
				'subtotal' => $order_data['order']['subtotal'],
				'discount' => $order_data['order']['discount'],
				'tax' => $order_data['order']['tax'],
				'total' => $order_data['order']['total'],
				'id_m_c_currency' => $order_data['order']['id_m_c_currency']);

			$this->load->model('warehouse/mwarehouses_logs');
			$this->mwarehouses_logs->create_sale_log($wg_shipping[self::ID_WH], $log_sale_array, $log_products_array);
			
			$this->sql_add_data(array('wh_shipping_state' => 'C'))->sql_update_date()->sql_save(self::WH_SHIPPING, $wg_shipping[self::ID_WH_SHIPPING]);
			return TRUE;
		}
		return FALSE;
	}*/
}	
?>