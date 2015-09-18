<?php
class Mwarehouses_sales extends AG_Model
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


	const PR_ADDEDIT_FORM_ID = 'products_add_edit_from';
	const ADD_PR_QTY_FORM_ID = 'add_pr_qty_form';
	const CREATE_SALE_FORM_ID = 'create_sale_form';
	const CREATE_TRANFER_FORM_ID = 'create_transfer_from';

	const create_sale_session = 'wh_create_sale_session';

	public function __construct()
	{
		parent::__construct();
	}

	public static function get_wh_sales_state_collection()
	{
		return array('N' => 'Новый', 'P' => 'В процессе', 'I' => 'Ожидание оплаты', 'IS' => 'Проверка оплаты', 'IC' => 'Оплачен', 'S' => 'Процесс отправки', 'C' => 'Завершен', 'CN' => 'Отменен', 'H' => 'Временно заморожен', 'COD' => 'Наложенный платеж', 'COD_S' => 'Процесс отправки(Н.П.)', 'COD_S_С' => 'Отправлен(Н.П.)', 'CM' => 'Возврат');
	}

	public static function get_wh_sales_state_name($key)
	{
		$state_collection = self::get_wh_sales_state_collection();
		if(isset($state_collection[$key]))
		{
			return $state_collection[$key];
		}
		return FALSE;
	}

	public function render_wh_sales_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("wh_log_sales_grid", array('sort' => 'A.'.self::ID_WH_SALES, 'desc' => 'DESC'));

		$this->grid->db
			->select("A.`".self::ID_WH_SALES."` AS ID, A.`wh_sale_number`, A.`wh_sale_state`, A.`total_qty`, CONCAT(A.`total`, ' ', A.`base_currency_name`) AS total, B.`create_date`, B.`comment`, ORD.`orders_number`, C.`".self::ID_WH."` AS WH_ID, C.`alias` as `wh_alias`, ADDR.`name`")
			->from("`".self::WH_SALES."` AS A")
			->join(
				"`".self::WH_LOGS."` AS B",
				"B.`".self::ID_WH_LOGS."` = A.`".self::ID_WH_LOGS."`",
				"INNER")
			->join(
				"`".self::ORD."` AS ORD",
				"ORD.`".self::ID_ORD."` = A.`".self::ID_ORD."`",
				"LEFT")
			->join(
				"`".self::WH."` AS C",
				"C.`".self::ID_WH."` = B.`".self::ID_WH."`",
				"LEFT")
			->join(
				"`".self::WH_SALES_ADDR."` AS ADDR",
				"ADDR.`".self::ID_WH_SALES."` = A.`".self::ID_WH_SALES."` && ADDR.`type` = 'B'",
				"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);
		$this->load->helper("warehouses_sales");
		helper_wh_sales_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('wh_sale_state', self::get_wh_sales_state_collection());
		$this->grid->render_grid();
	}

	public function render_warehouse_sales_grid($wh_id)
	{
		$this->load->library("grid");
		$this->grid->_init_grid("warehouse_log_sales_grid", array('sort' => 'A.'.self::ID_WH_SALES, 'desc' => 'DESC'));

		$this->grid->db
			->select("A.`".self::ID_WH_SALES."` AS ID, A.`wh_sale_number`, A.`wh_sale_state`, A.`total_qty`, CONCAT(A.`total`, ' ', A.`base_currency_name`) AS total, A.`create_date`, A.`admin_note`, ORD.`orders_number`, A.`".self::ID_WH."` AS WH_ID, C.`alias` as `wh_alias`, ADDR.`name`")
			->from("`".self::WH_SALES."` AS A")
			->join(
			"`".self::ORD."` AS ORD",
				"ORD.`".self::ID_ORD."` = A.`".self::ID_ORD."`",
				"LEFT")
			->join(
			"`".self::WH."` AS C",
				"C.`".self::ID_WH."` = A.`".self::ID_WH."`",
				"LEFT")
			->join(
			"`".self::WH_SALES_ADDR."` AS ADDR",
				"ADDR.`".self::ID_WH_SALES."` = A.`".self::ID_WH_SALES."` && ADDR.`type` = 'B'",
				"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_WH."`", $wh_id);
		$this->load->helper("warehouses");
		helper_warehouse_sales_grid_build($this->grid, $wh_id);
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('wh_sale_state', self::get_wh_sales_state_collection());
		$this->grid->render_grid();
	}

	public function render_not_edited_sale_products_grid($wh_id, $sale_id = 0)
	{
		$this->db->select("A.`".self::ID_PR."` AS PR_ID, A.`".self::ID_WH_SALES_PR."` AS `SALES_PR_ID`, A.`sku`, A.`name`, CONCAT('<span class=\'label\'>', A.`price_name`, '</span>', ' ', ROUND(A.`price`, 2)) AS `price`, IF(A.`real_qty` <> 1, CONCAT(A.`qty`,'(<span class=\'label\'>',A.`qty`*A.`real_qty`, '</span>)'), A.`qty`) AS qty_str, A.`qty`, A.`real_qty`, ROUND(A.`total`, 2) AS total, GROUP_CONCAT(CONCAT(B.`attributes_name`, ' : ', B.`attributes_options_name`) SEPARATOR '<BR>') AS attributes")
				 ->from("`".self::WH_SALES_PR."` AS A")
				 ->join(	"`".self::WH_SALES_PR_ATTR."` AS B",
					 "B.`".self::ID_WH_SALES_PR."` = A.`".self::ID_WH_SALES_PR."`",
					 "LEFT")
				 ->where("A.`".self::ID_WH_SALES."`", $sale_id)->group_by("A.`".self::ID_WH_SALES_PR."`");

		$product_array = $this->db->get()->result_array();

		$i = 1;
		foreach($product_array as $key => $ms)
		{
			$product_array[$key]['number'] = $i;
			$i++;
		}
		$this->load->library("nosql_grid");
		$this->nosql_grid->_init_grid("wh_sale_products_grid", array('sort' => "A.".self::ID_WH_SALES_PR, 'url' => FALSE));
		$this->nosql_grid->init_fixed_buttons(FALSE);
		$this->load->helper('warehouses_sales');
		helper_not_edited_wh_sale_products_grid_build($wh_id, $sale_id, $this->nosql_grid);

		$this->nosql_grid->set_grid_data($product_array);
		return $this->nosql_grid->render_grid(TRUE);
	}

	public function render_sale_products_grid($wh_id, $sale_id = 0, $ajax = FALSE)
	{
		$this->load->library("nosql_grid");
		$this->nosql_grid->_init_grid("wh_sale_products_grid", array('sort' => "A.".self::ID_WH_SALES_PR, 'url' => FALSE));
		$this->nosql_grid->init_fixed_buttons(FALSE);
		$this->nosql_grid->ajax_output = $ajax;

		$product_array = $this->prepare_sale_view_products_data($wh_id, $sale_id);

		$this->load->helper('warehouses_sales');
		helper_wh_sale_products_grid_build($wh_id, $sale_id, $this->nosql_grid);

		$this->nosql_grid->set_grid_data($product_array);
		return $this->nosql_grid->render_grid(TRUE);
	}

	public function render_wh_shop_products_grid($wh_id, $sale_id = 0)
	{
		$this->load->model('catalogue/mproducts');
		$this->mproducts->prepare_products_grid_query();
		$this->grid->db->join(
			"`".self::WH_PR."` AS WH_PR",
			"WH_PR.`".self::ID_WH."` = '".$wh_id."' && WH_PR.`".self::ID_PR."` = A.`".self::ID_PR."`",
			"INNER")
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, WH_PR.`qty`, A.`status`, A.`create_date`");

		$this->grid->keep_filter_data(TRUE);
		$this->grid->_init_grid('wh_'.$wh_id.'_sale_add_view_'.$sale_id, array('sort' => 'A.'.self::ID_PR, 'desc' => 'DESC', 'url' => set_url('warehouse/warehouses_sales/ajax_get_wh_shop_products_grid/wh_id/'.$wh_id.'/sale_id/'.$sale_id)));

		$this->load->helper('warehouses_sales');
		helper_sales_wh_shop_products_grid_build($this->grid, $wh_id, $sale_id);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		return $this->grid->render_grid(TRUE);
	}

	/*public function render_customers_grid($ord_id = 0)
	{
		$this->load->model('customers/mcustomers_types');
		$customers_groups = $this->mcustomers_types->get_customers_types();
		if(count($customers_groups) > 0)
		{
			$customers_groups = array('0' => 'Пользователи без группы') + $customers_groups;
		}

		$this->load->library("grid");
		$this->grid->_reinit_grid("orders_customers_grid", array('url' => set_url('sales/orders/ajax_get_customers_grid/ord_id/'.$ord_id)), TRUE);

		if($extra_search = $this->grid->get_options('search'))
		{
			if(isset($extra_search['id_m_u_types']))
			{
				$temp_extra_search = $extra_search;
				unset($temp_extra_search['id_m_u_types']);
				$this->grid->set_options('search', $temp_extra_search);
				$update_select_types = $extra_search['id_m_u_types'];
			}
		}

		$qty_query = clone $this->db;
		$qty_query->select("COUNT(*) AS numrows")
				  ->from("`".self::CT."` AS A")
				  ->join("`".self::CT_ADDR."` AS B",
					  "B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
					  "LEFT")
				  ->where("A.`".self::ID_USERS."`", $this->id_users);

		$this->grid->db->select("A.`".self::ID_CT."` AS ID, A.`email`, A.`create_date`, A.`update_date`, A.`active`, B.`name`, B.`city`, GROUP_CONCAT(D.`name` ORDER BY D.`".self::ID_CT_TYPE."` SEPARATOR '<BR>') AS id_m_u_types")
					   ->from("`".self::CT."` AS A")
					   ->join( "`".self::CT_ADDR."` AS B",
						   "B.`".self::ID_CT."` = A.`".self::ID_CT."` && B.`type` = 'B'",
						   "LEFT")
					   ->join("`".self::CT_N_TYPE."` AS C",
						   "C.`".self::ID_CT."` = A.`".self::ID_CT."`",
						   "LEFT")
					   ->join("`".self::CT_TYPE_DESC."` AS D",
						   "D.`".self::ID_CT_TYPE."` = C.`".self::ID_CT_TYPE."` && D.`".self::ID_LANGS."` = '".$this->id_langs."'",
						   "LEFT")
					   ->where("A.`".self::ID_USERS."`", $this->id_users)->group_by("A.`".self::ID_CT."`");


		if(isset($update_select_types))
		{
			$update_select_types = intval($update_select_types);
			if($update_select_types > 0)
			{
				$this->grid->db->join("`".self::CT_N_TYPE."` AS T",
					"T.`".self::ID_CT."` = A.`".self::ID_CT."` && T.`".self::ID_CT_TYPE."` = '".$update_select_types."'",
					"INNER");
				$qty_query->join("`".self::CT_N_TYPE."` AS T",
					"T.`".self::ID_CT."` = A.`".self::ID_CT."` && T.`".self::ID_CT_TYPE."` = '".$update_select_types."'",
					"INNER");

			}
			else if($update_select_types == 0)
			{
				$this->grid->db->where("C.`".self::ID_CT_TYPE."` IS NULL", NULL, FALSE);
				$qty_query->where("`have_m_u_types`", 0);
			}
		}
		$this->grid->set_extra_select_qty_object($qty_query);
		unset($qty_query);

		$this->load->helper('orders');
		helper_customers_grid_build($this->grid, $customers_groups, $ord_id);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data("active", array('0' => 'Нет', '1' => 'Да'));
		if(isset($update_select_types))
		{
			$extra_search = $this->grid->get_options('search');
			$extra_search['id_m_u_types'] = $update_select_types;
			$this->grid->set_search_manualy('id_m_u_types', $update_select_types);
			$this->grid->set_options('search', $extra_search);
		}
		return $this->grid->render_grid(TRUE);
	}*/

	public function prepare_sale_view_products_data($wh_id, $sale_id = 0)
	{
		$product_array = array();
		if($sale_id)
		{
			$this->db->select("A.`".self::ID_PR."` AS PR_ID, A.`".self::ID_WH_SALES_PR."` AS `SALES_PR_ID`, A.`sku`, A.`name`, CONCAT('<span class=\'label\'>', A.`price_name`, '</span>', ' ', ROUND(A.`price`, 2)) AS `price`, IF(A.`real_qty` <> 1, CONCAT(A.`qty`,'(<span class=\'label\'>',A.`qty`*A.`real_qty`, '</span>)'), A.`qty`) AS qty_str, A.`qty`, A.`real_qty`, ROUND(A.`total`, 2) AS total, GROUP_CONCAT(CONCAT(B.`attributes_name`, ' : ', B.`attributes_options_name`) SEPARATOR '<BR>') AS attributes")
					 ->from("`".self::WH_SALES_PR."` AS A")
					 ->join(
					 	"`".self::WH_SALES_PR_ATTR."` AS B",
						"B.`".self::ID_WH_SALES_PR."` = A.`".self::ID_WH_SALES_PR."`",
						"LEFT")
					 ->where("A.`".self::ID_WH_SALES."`", $sale_id)->group_by("A.`".self::ID_WH_SALES_PR."`");
			$product_array = $this->db->get()->result_array();
		}
		if(count($delete_prod_array = $this->get_deleted_sale_products($sale_id)) > 0)
		{
			foreach($product_array as $key => $ms)
			{
				if(isset($delete_prod_array[$ms['SALES_PR_ID']])) unset($product_array[$key]);
			}
		}

		if(count($edit_prod_array = $this->get_edited_sale_products($sale_id)) > 0 )
		{
			foreach($product_array as $key => $ms)
			{
				if(isset($edit_prod_array[$ms['SALE_PR_ID']]))
				{
					$product_array[$key]['subtotal'] = round(($edit_prod_array[$ms['SALE_PR_ID']]['subtotal']), 2);
					$product_array[$key]['total'] = round(($edit_prod_array[$ms['SALE_PR_ID']]['total']), 2);
					$product_array[$key]['qty'] = $edit_prod_array[$ms['SALE_PR_ID']]['qty'];
					if($product_array[$key]['real_qty'] != 1) $product_array[$key]['qty_str'] = $edit_prod_array[$ms['SALE_PR_ID']]['qty'].'(<span class=\'label\'>'.$edit_prod_array[$ms['SALE_PR_ID']]['qty'] * $edit_prod_array[$ms['SALE_PR_ID']]['real_qty'].'</span>)';
					else $product_array[$key]['qty_str'] = $edit_prod_array[$ms['SALE_PR_ID']]['qty'];
				}
			}
		}

		if(count($cart_array = $this->get_added_sale_products($wh_id, $sale_id))>0)
		{
			$cart_products_id = array();
			$cart_products_price_id = array();
			foreach($cart_array as $ms)
			{
				$cart_products_id[$ms['id']] = $ms['id'];
				$cart_products_price_id[$ms['options']['price_id']] = $ms['options']['price_id'];
				unset($ms['options']['price_id']);
				foreach($ms['options'] as $at_key => $at)
				{
					$cart_products_attributes_id[$at_key] = $at_key;
					$cart_products_attributes_options_id[$at] = $at;
				}
			}
			$this->load->model('catalogue/mproducts');
			$cart_products_temp_array = $this->mproducts->get_product($cart_products_id);
			$cart_products_array = array();
			foreach($cart_products_temp_array as $ms)
			{
				$cart_products_array[$ms['PR_ID']] = $ms;
			}
			unset($cart_products_temp_array);
			$attributes_n_options = $this->mproducts->get_product_attributes($cart_products_id);

			foreach($cart_array as $key => $ms)
			{
				if(isset($cart_products_array[$ms['id']]))
				{
					$product_array[$key] = array('PR_ID' => $cart_products_array[$ms['id']]['PR_ID'], 'SALES_PR_ID' => $key, 'sku' => $cart_products_array[$ms['id']]['sku'], 'name' => $cart_products_array[$ms['id']]['name'], 'price' => round($ms['price'], 2), 'qty' => $ms['qty'], 'real_qty' => $ms['options']['real_qty'], 'total' => round(($ms['price'] * $ms['qty']), 2));
					$product_array[$key]['qty_str'] = $ms['qty'];
					if($ms['options']['real_qty'] != 1)
					{
						$product_array[$key]['qty_str'] = $ms['qty'].'(<span class=\'label\'>'.$ms['qty'] * $ms['options']['real_qty'].'</span>)';
					}
					$product_array[$key]['attributes'] = '';
					unset($ms['options']['price_id']);
					unset($ms['options']['real_qty']);
					foreach($ms['options'] as $at_key => $at)
					{
						$product_array[$key]['attributes'] .= @$attributes_n_options[$at_key][$at]['a_name'].' : '.@$attributes_n_options[$at_key][$at]['o_name'].'<BR>';
					}
				}
			}
		}
		if(count($product_array) > 0)
		{
			$pr_id_array = array();
			foreach($product_array as $ms)
			{
				$pr_id_array[$ms['PR_ID']] = $ms['PR_ID'];
			}
			$this->load->model('warehouse/mwarehouses');
			$wh_qty_array = $this->mwarehouses->get_wh_product_total_qty_array($wh_id, $pr_id_array);
			$i = 1;
			$pr_qty_array = array();
			foreach($product_array as $key => $ms)
			{
				$product_array[$key]['number'] = $i;
				$i++;
				if(isset($pr_qty_array[$ms['PR_ID']]))
				{
					$product_array[$key]['wh_qty'] = $wh_qty_array[$ms['PR_ID']]['qty'] - $pr_qty_array[$ms['PR_ID']] - ($ms['qty']*$ms['real_qty']);
					$product_array[$key]['wh_qty_str'] = $product_array[$key]['wh_qty'];
					$pr_qty_array[$ms['PR_ID']] += ($ms['qty']*$ms['real_qty']);
				}
				else
				{
					$product_array[$key]['wh_qty'] = $wh_qty_array[$ms['PR_ID']]['qty'] - ($ms['qty']*$ms['real_qty']);
					$product_array[$key]['wh_qty_str'] = $product_array[$key]['wh_qty'];
					$pr_qty_array[$ms['PR_ID']] = ($ms['qty']*$ms['real_qty']);
				}
				if($product_array[$key]['wh_qty'] < 0) $product_array[$key]['wh_qty_str'] = '<span class=\'error\'>'.$product_array[$key]['wh_qty'].'</span>';
			}
		}
		return $product_array;
	}

	public function prepare_add_sale()
	{
		$data = array();
		$this->load->model('warehouse/mwarehouses');
		$wh = $this->mwarehouses->get_wh_to_select();
		if(count($wh) > 0)
		{
			$data['wh_collection'] = $wh;
			$this->load->helper('warehouses_sales');
			helper_wh_sale_prepare_add($data);
			return TRUE;
		}
		return FALSE;
	}

	public function add_sale($wh_id)
	{
		$this->unset_sale_temp($wh_id, 0);
		$sale_data = array();

		$this->load->model('catalogue/mcurrency');

		list($sale_data['users_currency']['currency'], $sale_data['users_currency']['default']) = $this->mcurrency->get_users_currency_to_select();
		$sale_data['base_currency'] = $this->mcurrency->get_users_base_currency();
		$sale_data['default_selected_currency'] = $this->mcurrency->get_users_currency_by_cid($sale_data['users_currency']['default']);
		$sale_data['sales_states'] = self::get_wh_sales_state_collection();
		$sale_data['warehouse_alias'] = $this->mwarehouses->get_wh($wh_id);
		$sale_data['warehouse_alias'] = $sale_data['warehouse_alias']['alias'];
		$sale_data['sale_products_grid'] = $this->render_sale_products_grid($wh_id, 0);

		$this->_set_sale_currency_temp($wh_id, 0, $sale_data['default_selected_currency']);
		$this->_set_sale_base_currency_temp($wh_id, 0, $sale_data['base_currency']);

		return $sale_data;
	}

	public function view_sale($wh_id, $sale_id)
	{
		if($sale_data = $this->get_sale_data($sale_id))
		{
			$sale_data['sale_products_grid'] = $this->render_not_edited_sale_products_grid($wh_id, $sale_id);
			$this->load->model("warehouse/mwarehouses");
			$sale_wh = $this->mwarehouses->get_wh($sale_data['sale'][self::ID_WH]);
			$sale_data['sale']['warehouse_alias'] = $sale_wh['alias'];
			return $sale_data;
		}
		return FALSE;
	}

	public function get_sale_data($sale_id)
	{
		$sale = array();
		if($sale['sale'] = $this->get_sale($sale_id))
		{
			$sale['addresses'] = $this->get_sale_addresses($sale_id);
			$sale['sale'] += $this->get_sale_sum($sale['sale']);
			$sale['sale']['wh_sale_state_name'] = $this->get_wh_sales_state_name($sale['sale']['wh_sale_state']);
		}
		return $sale;
	}

	public function get_sale($sale_id)
	{
		$this->db->select("A.*, B.`".self::ID_WH."`, B.`comment`, B.`create_date`, B.`update_date`")
				->from("`".self::WH_SALES."` AS A")
				->join(
				"`".self::WH_LOGS."` AS B",
				"B.`".self::ID_WH_LOGS."` = A.`".self::ID_WH_LOGS."`",
				"INNER")
				->where("A.`".self::ID_WH_SALES."`", $sale_id)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($sale = $this->db->get()->row_array()) > 0)
		{
			return $sale;
		}
		return FALSE;
	}

	public function get_sale_addresses($sale_id)
	{
		$addresses = array();
		$this->db->select("*")
			->from("`".self::WH_SALES_ADDR."`")
			->where("`".self::ID_WH_SALES."`", $sale_id)->limit(2);
		foreach($this->db->get()->result_array() as $ms)
		{
			$addresses[$ms['type']] = $ms;
		}
		return $addresses;
	}

	public function get_sale_sum($sale_id)
	{
		if(!is_array($sale_id))
		{
			$sale = $this->get_sale($sale_id);
		}
		else
		{
			$sale = $sale_id;
		}
		$sale_sum = array();
		if(count($sale)>0)
		{
			$total_qty = $sale['total_qty'];
			if(($discount = floatval($sale['discount'])) > 0)
			{
				$subtotal = $sale['subtotal'];
				$total = $sale['total'];

				$subtotal_rate = $subtotal * $sale['currency_rate'];
				$total_rate = $total * $sale['currency_rate'] - $discount;
			}
			else
			{
				$subtotal = $sale['subtotal'];
				$total = $sale['total'];

				$subtotal_rate = $subtotal * $sale['currency_rate'];
				$total_rate = $total * $sale['currency_rate'];
			}

			if($sale['base_id_m_c_currency'] == $sale['id_m_c_currency'])
			{
				$subtotal_string = round($subtotal_rate, 2).' '.$sale['currency_name'];
			}
			else
			{
				$subtotal_string = round($subtotal_rate, 2).' '.$sale['currency_name'].' ('.round($subtotal, 2).' '.$sale['base_currency_name'].')';
			}
			$total_string = round($total_rate, 2).' '.$sale['currency_name'];

			$sale_sum = array(
				'total_qty_string' => $total_qty, 'subtotal_string' => $subtotal_string, 'total_string' => $total_string,
				'total_qty' => $total_qty,
				'subtotal' => $subtotal, 'total' => $total,
				'subtotal_rate' => $subtotal_rate, 'total_rate' => $total_rate,
				'discount' => $discount
			);
		}
		return $sale_sum;
	}

	public function get_sale_products($sale_id)
	{
		$products = array();
		$this->db->select("*")
				 ->from("`".self::WH_SALES_PR."` AS A")
				 ->where("`".self::ID_WH_SALES."`", $sale_id);
		$result = $this->db->get()->result_array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$products[$ms[self::ID_WH_SALES_PR]] = $ms;
			}
		}
		return $products;
	}

	public function get_view_product_data($wh_id, $sale_id, $pr_id)
	{
		$this->load->model('catalogue/mproducts_view');
		if($product_array = $this->mproducts_view->get_product($pr_id))
		{
			return $product_array;
		}
		return FALSE;
	}




	//SALE CUSTOMER ACTION
	public function set_sale_customer($wh_id, $sale_id, $cm_id)
	{
		$this->load->model('customers/mcustomers');
		if($this->mcustomers->check_isset_ct($cm_id))
		{
			$customer['customer'] = $this->mcustomers->get_customer($cm_id);
			$customer['addresses'] = $this->mcustomers->get_customer_addresses($cm_id);
			$this->_set_sale_customer_temp($wh_id, $sale_id, $customer['customer']);
			return $customer;
		}
		return FALSE;
	}

	public function unset_order_customer($wh_id, $sale_id = 0)
	{
		if($sale_id > 0)
		{
			$this->_set_sale_customer_temp($wh_id, $sale_id, NULL);
		}
		else
		{
			$this->_unset_sale_customer_temp($wh_id, $sale_id);
		}
		return TRUE;
	}

	protected function _set_sale_customer_temp($wh_id, $sale_id, $customer)
	{
		$this->session->set_userdata('wh_'.$wh_id.'_sale_customer_id_'.$sale_id, $customer);
	}

	protected function _unset_sale_customer_temp($wh_id, $sale_id)
	{
		$this->session->unset_userdata('wh_'.$wh_id.'_sale_customer_id_'.$sale_id);
	}

	protected function _get_sale_customer_temp($wh_id, $sale_id)
	{
		return $this->session->userdata('wh_'.$wh_id.'_sale_customer_id_'.$sale_id);
	}
	//----SALE CUSTOMER ACTION----

	//SALE PRODUCTS ACTIONS
	public function add_product_to_sale($wh_id, $sale_id = 0)
	{
		$this->load->model('warehouse/mwarehouses');
		$this->_init_cart($wh_id, $sale_id);
		$POST = $this->input->post();

		$attributes = array();
		if(isset($POST['attributes'])) $attributes = $POST['attributes'];
		list($price_array, $attributes_array) = $this->_prepare_product_price_n_attributes($POST['product_id'], $POST['price_id'], $attributes);

		if($price_array)
		{
			$data['id'] = $POST['product_id'];
			if(!isset($POST['qty'])) $POST['qty'] = $price_array['min_qty'];
			$data['qty'] = $POST['qty'];
			$data['name'] = $POST['product_id'];
			$data['price'] = floatval($POST['price']);
			if($data['price'] == 0) $data['price'] = 1;

			$data['options'] = array();
			$data['options']['price_id'] = $POST['price_id'];
			$data['options']['real_qty'] = $price_array['real_qty'];
			foreach($attributes_array as $ms)
			{
				$data['options'][$ms['ID']] = $ms['ID_OP'];
			}

			if($sale_id)
			{
				/*if(!$this->isset_sale($sale_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');

				$isset_catr_item_qty = 0;
				if($isset_cart_product = $this->cart->isset_cart_item($data))
				{
					$isset_catr_item_qty = $isset_cart_product['qty']*$isset_cart_product['options']['real_qty'];
				}

				$cart_pr_total_qty = $this->calculate_temp_order_product_qty($POST['product_id'], $sale_id);
				$wh_pr_total_qty = $this->mwarehouses->get_wh_product_total_qty($wh_id, $POST['product_id']);
				if(($cart_pr_total_qty - $isset_catr_item_qty + ($data['qty'] * $data['options']['real_qty'])) > $wh_pr_total_qty)
				{
					$message = "";
					if($isset_catr_item_qty > 0)
					{
						if($data['options']['real_qty'] > 1) $message .= "Внимание! Позиция с указанной комплектацией в количестве ".$isset_cart_product['qty']."(".$isset_cart_product['qty']*$isset_cart_product['options']['real_qty'].") уже была прикреплена к заказу. ";
						else $message .= "Внимание! Позиция с указанной комплектацией в количестве ".$isset_cart_product['qty']." уже была прикреплена к заказу. ";
					}

					$available_qty = floor(($wh_pr_total_qty + $isset_catr_item_qty - $cart_pr_total_qty)/$data['options']['real_qty']);

					if($data['options']['real_qty'] > 1) $message .= "Количества на складе не достаточно. Доступное количество ".$available_qty."(".$available_qty * $data['options']['real_qty'].").";
					else $message .= "Количества на складе не достаточно. Доступное количество ".$available_qty.".";

					return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message);
				}

				$this->_add_product_to_order($data, $ord_id);
				if($isset_cart_product)
				{
					if($data['options']['real_qty'] > 1) $message = "Позиция с указанной комплектацией уже была прикреплена к заказу. Количество изменено с ".$isset_cart_product['qty']."(".$isset_cart_product['qty']*$isset_cart_product['options']['real_qty'].") на ".$data['qty']."(".$data['qty'] * $data['options']['real_qty'].").";
					else $message = "Позиция с указанной комплектацией уже была прикреплена к заказу. Количество изменено с ".$isset_cart_product['qty']." на ".$data['qty'].".";
				}
				else
				{
					if($data['options']['real_qty'] > 1) $message = "Позиция в количестве ".$data['qty']."(".$data['qty'] * $data['options']['real_qty'].") успешно добавлена к заказу.";
					else $message = "Позиция в количестве ".$data['qty']." успешно добавлена к заказу.";
				}

				return array('success' => TRUE, 'message' => $message);*/

			}
			else
			{
				$isset_catr_item_qty = 0;
				if($isset_cart_product = $this->cart->isset_cart_item($data))
				{
					$isset_catr_item_qty = $isset_cart_product['qty']*$isset_cart_product['options']['real_qty'];
				}

				$cart_pr_total_qty = $this->calculate_temp_sale_product_qty($wh_id, $sale_id, $POST['product_id']);
				$wh_pr_total_qty = $this->mwarehouses->get_wh_product_total_qty($wh_id, $POST['product_id']);
				if(($cart_pr_total_qty - $isset_catr_item_qty + ($data['qty'] * $data['options']['real_qty'])) > $wh_pr_total_qty)
				{
					$message = "";
					if($isset_catr_item_qty > 0)
					{
						if($data['options']['real_qty'] > 1) $message .= "Внимание! Позиция с указанной комплектацией в количестве ".$isset_cart_product['qty']."(".$isset_cart_product['qty']*$isset_cart_product['options']['real_qty'].") уже была прикреплена к заказу.<BR>";
						else $message .= "Внимание! Позиция с указанной комплектацией в количестве ".$isset_cart_product['qty']." уже была прикреплена к заказу.<BR>";
					}

					$available_qty = floor(($wh_pr_total_qty + $isset_catr_item_qty - $cart_pr_total_qty)/$data['options']['real_qty']);

					if($data['options']['real_qty'] > 1) $message .= "Количества на складе не достаточно. Доступное количество ".$available_qty."(".$available_qty * $data['options']['real_qty'].")";
					else $message .= "Количества на складе не достаточно. Доступное количество ".$available_qty;

					return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message);
				}

				$this->_add_product_to_sale($wh_id, $sale_id, $data);
				if($isset_cart_product)
				{
					if($data['options']['real_qty'] > 1) $message = "Позиция с указанной комплектацией уже была прикреплена к заказу. Количество изменено с ".$isset_cart_product['qty']."(".$isset_cart_product['qty']*$isset_cart_product['options']['real_qty'].") на ".$data['qty']."(".$data['qty'] * $data['options']['real_qty'].").";
					else $message = "Позиция с указанной комплектацией уже была прикреплена к заказу. Количество изменено с ".$isset_cart_product['qty']." на ".$data['qty'].".";
				}
				else
				{
					if($data['options']['real_qty'] > 1) $message = "Позиция в количестве ".$data['qty']."(".$data['qty'] * $data['options']['real_qty'].") успешно добавлена к заказу.";
					else $message = "Продукт в количестве ".$data['qty']." успешно добавлена к заказу.";
				}

				return array('success' => TRUE, 'message' => $message);
			}
		}
		return FALSE;
	}

	protected function _prepare_product_price_n_attributes($pr_id, $price_id, $attributes = array())
	{
		$price_data = array();
		$attributes_data = array();
		$this->load->model('catalogue/mproducts_view');
		if($product_price = $this->mproducts_view->get_product_price($pr_id, $price_id))
		{
			if($product_price['original_special_price']) $price_data['price'] = $product_price['original_special_price'];
			else $price_data['price'] = $product_price['original_price'];
			$price_data['price_id'] = $price_id;
			$price_data['price_alias'] = $product_price['price_alias'];
			$price_data['price_name'] = $product_price['price_name'];
			$price_data['min_qty'] = $product_price['min_qty'];
			$price_data['real_qty'] = $product_price['real_qty'];
			if(count($attributes) > 0)
			{
				if($product_price['show_attributes'] == 1)
				{
					$product_attributes = $this->mproducts_view->get_product_attributes_and_options($pr_id);
					foreach($product_attributes as $key => $ms)
					{
						if(isset($attributes[$key]))
						{
							foreach($ms as $keyop => $msop)
							{
								if($attributes[$key] == $keyop)
								{
									$attributes_data[$key] = $msop;
								}
							}
						}
					}
				}
				else if($product_price['show_attributes'] == 2)
				{
					$product_attributes = $this->mproducts_view->get_product_attributes_and_options($pr_id);
					$id_attributes = explode(',', $product_price['id_attributes']);
					foreach($product_attributes as $key => $ms)
					{
						if(isset($attributes[$key]) && in_array($key, $id_attributes))
						{
							foreach($ms as $keyop => $msop)
							{
								if($attributes[$key] == $keyop)
								{
									$attributes_data[$key] = $msop;
								}
							}
						}
					}
				}
			}
			return array($price_data, $attributes_data);
		}
		return array(FALSE, FALSE);
	}

	public function edit_sale_product_qty($wh_id, $sale_id, $sale_pr_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(($qty = intval($this->input->post('sale_pr_qty')))>0)
		{
			if($sale_id > 0)
			{
				/*if(!$this->isset_sale($sale_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
				if($wh_id)
				{
					if($this->is_valid_md5($ord_pr_id))
					{
						$cart = $this->get_added_order_products($ord_id);
						if(!isset($cart[$ord_pr_id])) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
						$cart_pr_array = $cart[$ord_pr_id];
						$pr_id = $cart_pr_array['id'];

						$cart_pr_total_qty = $this->calculate_temp_order_product_qty($pr_id, $ord_id);
						$wh_pr_total_qty = $this->mwarehouses->get_wh_product_total_qty($wh_id, $pr_id);
						$real_qty_coef = $cart_pr_array['options']['real_qty'];

						$cart_pr_total_qty = $cart_pr_total_qty - ($cart_pr_array['qty'] * $cart_pr_array['options']['real_qty']);
					}
					else
					{
						if(!$ord_id) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
						if(!$order_product_array = $this->get_order_product($ord_id, $ord_pr_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');

						$cart_pr_total_qty = $this->calculate_temp_order_product_qty($order_product_array[self::ID_PR], $ord_id);
						$wh_pr_total_qty = $this->mwarehouses->get_wh_product_total_qty($wh_id, $order_product_array[self::ID_PR]);
						$real_qty_coef = $order_product_array['real_qty'];

						$edit_prod_array = $this->get_edited_order_products($ord_id);

						if(isset($edit_prod_array[$ord_pr_id]))
						{
							$cart_pr_total_qty = $cart_pr_total_qty - ($edit_prod_array[$ord_pr_id]['qty'] * $edit_prod_array[$ord_pr_id]['real_qty']);
						}
						else
						{
							$edit_prod_array[$ord_pr_id] = $order_product_array;
							$cart_pr_total_qty = $cart_pr_total_qty - ($order_product_array['qty'] * $order_product_array['real_qty']);
						}
					}

					if(($cart_pr_total_qty + ($qty * $real_qty_coef)) > $wh_pr_total_qty)
					{
						$available_qty = floor(($wh_pr_total_qty - $cart_pr_total_qty)/$real_qty_coef);

						if($real_qty_coef > 1) $message = "Количества продукта на складе не достаточно. Доступное количество ".$available_qty."(".$available_qty * $real_qty_coef.")";
						else $message = "Количества продукта на складе не достаточно. Доступное количество ".$available_qty;

						return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message);
					}

					if($this->is_valid_md5($ord_pr_id))
					{
						$this->_edit_order_product($ord_pr_id, $ord_id, $qty);
						if($real_qty_coef > 1) $message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']."(".$cart_pr_array['qty']*$real_qty_coef.") на ".$qty."(".$qty*$real_qty_coef.").";
						else $message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']." на ".$qty.".";
						return array('success' => TRUE, 'message' => $message);
					}
					else
					{
						$this->_edit_order_product($ord_pr_id, $ord_id, $qty, $order_product_array);
						if($real_qty_coef > 1) $message = "Количество позиции успешно отредактировано с ".$edit_prod_array[$ord_pr_id]['qty']."(".$edit_prod_array[$ord_pr_id]['qty']*$real_qty_coef.") на ".$qty."(".$qty*$real_qty_coef.").";
						else $message = "Количество позиции успешно отредактировано с ".$edit_prod_array[$ord_pr_id]['qty']." на ".$qty.".";
						return array('success' => TRUE, 'message' => $message);
					}
				}
				else
				{
					if($this->is_valid_md5($ord_pr_id))
					{
						$cart = $this->get_added_order_products($ord_id);
						if(!isset($cart[$ord_pr_id])) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
						$cart_pr_array = $cart[$ord_pr_id];
						$real_qty_coef = $cart_pr_array['options']['real_qty'];
						if($real_qty_coef > 1) $message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']."(".$cart_pr_array['qty']*$real_qty_coef.") на ".$qty."(".$qty*$real_qty_coef.").";
						else $message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']." на ".$qty.".";
						$this->_edit_order_product($ord_pr_id, $ord_id, $qty);
						return array('success' => TRUE, 'message' => $message);
					}
					else
					{
						$edit_prod_array = $this->get_edited_order_products($ord_id);
						if(!$order_product_array = $this->get_order_product($ord_id, $ord_pr_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
						$real_qty_coef = $order_product_array['real_qty'];
						if(isset($edit_prod_array[$ord_pr_id]))
						{
							if($real_qty_coef > 1) $message = "Количество позиции успешно отредактировано с ".$edit_prod_array[$ord_pr_id]['qty']."(".$edit_prod_array[$ord_pr_id]['qty']*$real_qty_coef.") на ".$qty."(".$qty*$real_qty_coef.").";
							else $message = "Количество позиции успешно отредактировано с ".$edit_prod_array[$ord_pr_id]['qty']." на ".$qty.".";
						}
						else
						{
							if($real_qty_coef > 1) $message = "Количество позиции успешно отредактировано с ".$order_product_array['qty']."(".$order_product_array['qty']*$real_qty_coef.") на ".$qty."(".$qty*$real_qty_coef.").";
							else $message = "Количество позиции успешно отредактировано с ".$order_product_array['qty']." на ".$qty.".";
						}

						$this->_edit_order_product($ord_pr_id, $ord_id, $qty, $order_product_array);
						return array('success' => TRUE, 'message' => $message);
					}
				}*/
			}
			else
			{
				if(!$this->is_valid_md5($sale_pr_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');

				$cart = $this->get_added_sale_products($wh_id, $sale_id);
				if(!isset($cart[$sale_pr_id])) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
				$cart_pr_array = $cart[$sale_pr_id];

				$cart_pr_total_qty = $this->calculate_temp_sale_product_qty($wh_id, $sale_id, $cart_pr_array['id']);
				$wh_pr_total_qty = $this->mwarehouses->get_wh_product_total_qty($wh_id, $cart_pr_array['id']);
				$real_qty_coef = $cart_pr_array['options']['real_qty'];

				$cart_pr_total_qty = $cart_pr_total_qty - ($cart_pr_array['qty'] * $cart_pr_array['options']['real_qty']);

				if(($cart_pr_total_qty + ($qty * $real_qty_coef)) > $wh_pr_total_qty)
				{
					$available_qty = floor(($wh_pr_total_qty - $cart_pr_total_qty)/$real_qty_coef);

					if($real_qty_coef > 1) $message = "Количества позиции на складе не достаточно. Доступное количество ".$available_qty."(".$available_qty * $real_qty_coef.")";
					else $message = "Количества позиции на складе не достаточно. Доступное количество ".$available_qty;

					return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message);
				}

				$this->_edit_sale_product($wh_id, $sale_id, $sale_pr_id, $qty);

				if($real_qty_coef > 1) $message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']."(".$cart_pr_array['qty']*$real_qty_coef.") на ".$qty."(".$qty*$real_qty_coef.").";
				else $message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']." на ".$qty.".";

				return array('success' => TRUE, 'message' => $message);
			}
		}
		return array('success' => FALSE, 'message' => 'Server Error. Try later!');
	}

	public function delete_product_from_sale($wh_id, $sale_id, $sale_pr_id)
	{
		if($this->_delete_sale_product($wh_id, $sale_id, $sale_pr_id))
		{
			$message = "Позиция успешно удалена.";
			return array('success' => TRUE, 'message' => $message);
		}
		return array('success' => FALSE, 'message' => 'Server Error. Try later!');
	}

	public function get_temp_sale_products_sum($wh_id, $sale_id = 0)
	{
		$product_array = array();
		if($sale_id > 0)
		{
			$this->db->select("*")
					 ->from("`".self::WH_SALES_PR."`")
					 ->where("`".self::ID_WH_SALES."`", $sale_id);
			$product_array = $this->db->get()->result_array();
		}
		$total_qty = 0;
		$total_sum = 0;
		$edit_prod_array = $this->get_edited_sale_products($wh_id, $sale_id);

		if(count($delete_prod_array = $this->get_deleted_sale_products($wh_id, $sale_id)) > 0)
		{
			foreach($product_array as $key => $ms)
			{
				if(isset($delete_prod_array[$ms[self::ID_WH_SALES_PR]])) unset($product_array[$key]);
			}
		}

		foreach($product_array as $ms)
		{
			if(isset($edit_prod_array[$ms[self::ID_WH_SALES_PR]]))
			{
				$total_qty += $edit_prod_array[$ms[self::ID_WH_SALES_PR]]['qty'] * $ms['real_qty'];
				$total_sum += $edit_prod_array[$ms[self::ID_WH_SALES_PR]]['total'];
			}
			else
			{
				$total_qty += $ms['qty'] * $ms['real_qty'];
				$total_sum += $ms['total'];
			}
		}

		$cart_array = $this->get_added_sale_products($wh_id, $sale_id);
		foreach($cart_array as $ms)
		{
			$total_qty += $ms['qty'] * $ms['options']['real_qty'];
			$total_sum += $ms['price'] * $ms['qty'];
		}

		$currency = $this->_get_sale_currency_temp($wh_id, $sale_id);
		$base_currency = $this->_get_sale_base_currency_temp($wh_id, $sale_id);
		if($discount = $this->_get_sale_discount_temp($wh_id, $sale_id))
		{
			$subtotal = $total_sum;
			$total = $total_sum;

			$subtotal_rate = $total_sum * $currency['rate'];
			$total_rate = $total_sum * $currency['rate'] - $discount;
		}
		else
		{
			$subtotal = $total_sum;
			$total = $total_sum;

			$subtotal_rate = $total_sum * $currency['rate'];
			$total_rate = $total_sum * $currency['rate'];
		}

		if($currency['name'] == $base_currency['name'])
		{
			$subtotal_string = round($subtotal_rate, 2).' '.$currency['name'];
		}
		else
		{
			$subtotal_string = round($subtotal_rate, 2).' '.$currency['name'].' ('.round($subtotal, 2).' '.$base_currency['name'].')';
		}
		$total_string = round($total_rate, 2).' '.$currency['name'];

		return array('total_qty_string' => $total_qty, 'subtotal_string' => $subtotal_string, 'total_string' => $total_string,
			'total_qty' => $total_qty,
			'subtotal' => $subtotal, 'total' => $total,
			'subtotal_rate' => $subtotal_rate, 'total_rate' => $total_rate,
			'discount' => floatval($discount)
		);
	}

	protected function get_added_sale_products($wh_id, $sale_id = 0)
	{
		$this->_init_cart($wh_id, $sale_id);
		return $this->cart->contents();
	}

	protected function get_edited_sale_products($wh_id, $sale_id = 0)
	{
		$products_array = array();
		if($this->session->userdata('wh_'.$wh_id.'_sale_edit_products_'.$sale_id))
		{
			$products_array = $this->session->userdata('wh_sale_edit_products_'.$sale_id);
		}
		return $products_array;
	}

	protected function get_deleted_sale_products($wh_id, $sale_id = 0)
	{
		$products_array = array();
		if($this->session->userdata('wh_'.$wh_id.'_sale_delete_products_'.$sale_id))
		{
			$products_array = $this->session->userdata('wh_sale_delete_products_'.$sale_id);
		}
		return $products_array;
	}

	private function _init_cart($wh_id, $sale_id = 0)
	{
		$this->load->library('cart');
		$this->cart->set_cart_key_suffix('_wh_'.$wh_id.'_sales_'.$sale_id);
	}

	protected function _add_product_to_sale($wh_id, $sale_id, $data)
	{
		$this->_init_cart($wh_id, $sale_id);
		return $this->cart->insert($data);
	}

	protected function _edit_sale_product($wh_id, $sale_id, $sale_pr_id, $qty, $sale_product_array = array())
	{
		if($qty <= 0) return FALSE;
		if($this->is_valid_md5($sale_pr_id))
		{
			$this->cart->update(array('rowid' => $sale_pr_id, 'qty' => $qty));
			return TRUE;
		}
		else
		{
			$edit_prod_array = $this->get_edited_sale_products($wh_id, $sale_id);
			$edit_prod_array[$sale_pr_id] = array('qty' => $qty, 'real_qty' => $sale_product_array['real_qty'], 'subtotal' =>$qty*$sale_product_array['price'], 'total' => $qty*$sale_product_array['price']);
			$this->session->set_userdata('wh_'.$wh_id.'_sales_edit_products_'.$sale_id, $edit_prod_array);
			return TRUE;
		}
	}

	protected function _delete_sale_product($wh_id, $sale_id, $sale_pr_id)
	{
		if($this->is_valid_md5($sale_pr_id))
		{
			$this->_init_cart($wh_id, $sale_id);
			$this->cart->update(array('rowid' => $sale_pr_id, 'qty' => 0));
			return TRUE;
		}
		else if($sale_id && ($sale_pr_id = intval($sale_pr_id))>0)
		{
			if(!$this->isset_sale($sale_id)) return FALSE;
			$deleted_products = $this->get_deleted_sale_products($wh_id, $sale_id);
			$product = array($sale_pr_id => $sale_pr_id);
			$this->session->set_userdata('wh_'.$wh_id.'_sales_delete_products_'.$sale_id, $deleted_products + $product);
			return TRUE;
		}
		return FALSE;
	}

	protected function calculate_temp_sale_product_qty($wh_id, $sale_id, $pr_id)
	{
		$cart_pr_total_qty = 0;
		if($sale_id)
		{
			$this->db->select("A.`".self::ID_WH_SALES_PR."` AS SALE_PR_ID, A.`".self::ID_PR."` AS PR_ID, A.`sku`, A.`qty`, A.`real_qty`")
					 ->from("`".self::WH_SALES_PR."` AS A")
					 ->where("A.`".self::ID_WH_SALES."`", $sale_id)->where("A.`".self::ID_PR."`", $pr_id);
			$sale_pr = $this->db->get()->result_array();

			$delete_prod_array = $this->get_deleted_sale_products($wh_id, $sale_id);
			$edit_prod_array = $this->get_edited_sale_products($wh_id, $sale_id);

			foreach($sale_pr as $ms)
			{
				if(isset($edit_prod_array[$ms['SALE_PR_ID']]))
				{
					$ms['qty'] = $edit_prod_array[$ms['SALE_PR_ID']]['qty'];
					$ms['real_qty'] = $edit_prod_array[$ms['SALE_PR_ID']]['real_qty'];
				}
				if(isset($delete_prod_array[$ms['SALE_PR_ID']])) $ms['qty'] = 0;

				$cart_pr_total_qty += $ms['qty'] * $ms['real_qty'];
			}
		}

		$cart_pr = $this->get_added_sale_products($wh_id, $sale_id);
		foreach($cart_pr as $ms)
		{
			if($ms['id'] == $pr_id) $cart_pr_total_qty += $ms['qty'] * $ms['options']['real_qty'];
		}
		return $cart_pr_total_qty;
	}

	public function unset_sale_products_temp($wh_id, $sale_id = 0)
	{
		$this->_unset_sale_products_temp($wh_id, $sale_id);
		return TRUE;
	}

	protected function _unset_sale_products_temp($wh_id, $sale_id)
	{
		$this->session->unset_userdata('wh_'.$wh_id.'_sales_edit_products_'.$sale_id);
		$this->session->unset_userdata('wh_'.$wh_id.'_sales_delete_products_'.$sale_id);
		$this->_init_cart($wh_id, $sale_id);
		$this->cart->destroy();
	}

	public function get_sale_product_qty($wh_id, $sale_id, $sale_pr_id)
	{
		if($this->is_valid_md5($sale_pr_id))
		{
			$cart_products = $this->get_added_sale_products($wh_id, $sale_id);
			if(isset($cart_products[$sale_pr_id]))
			{
				return array('qty' => $cart_products[$sale_pr_id]['qty'], 'wh_id' => $wh_id, 'sale_id' => $sale_id, 'sale_pr_id' => $sale_pr_id);
			}
			return FALSE;
		}
		$this->db->select("`qty`, `real_qty`")
				 ->from("`".self::WH_SALES_PR."` AS A")
				 ->where("A.`".self::ID_WH_SALES_PR."`", $sale_pr_id)->limit(1);
		$result = $this->db->get()->row_array();
		if(count($result)>0)
		{
			return array('qty' => $result['qty'], 'real_qty' => $result['real_qty'], 'wh_id' => $wh_id, 'sale_id' => $sale_id, 'sale_pr_id' => $sale_pr_id);
		}
		return FALSE;
	}
	//---SALE PRODUCTS ACTIONS---

	//SALE CURRENCY ACTIONS
	public function set_sale_currency($wh_id, $sale_id, $currency_id)
	{
		$this->load->model('catalogue/mcurrency');
		if($currency = $this->mcurrency->get_users_currency_by_cid($currency_id))
		{
			$this->_set_sale_currency_temp($wh_id, $sale_id, $currency);
			return $currency;
		}
		return FALSE;
	}

	public function set_sale_currency_rate($wh_id, $sale_id, $currency_rate)
	{
		$currency_rate = floatval($currency_rate);
		$currency = $this->_get_sale_currency_temp($wh_id, $sale_id);
		$base_currency = $this->_get_sale_base_currency_temp($wh_id, $sale_id);

		if($currency_rate <= 0) return $currency;

		if($currency['id_m_c_currency'] != $base_currency['id_m_c_currency'])
		{
			$currency['rate'] = $currency_rate;
			$this->_set_sale_currency_temp($wh_id, $sale_id, $currency);
			return $currency;
		}
		return $currency;
	}

	protected function _set_sale_currency_temp($wh_id, $sale_id, $currency)
	{
		$this->session->set_userdata('wh_'.$wh_id.'_sale_currency_'.$sale_id, $currency);
	}

	protected function _get_sale_currency_temp($wh_id, $sale_id)
	{
		return $this->session->userdata('wh_'.$wh_id.'_sale_currency_'.$sale_id);
	}

	protected function _set_sale_base_currency_temp($wh_id, $sale_id, $currency)
	{
		$this->session->set_userdata('wh_'.$wh_id.'_sale_base_currency_'.$sale_id, $currency);
	}

	protected function _get_sale_base_currency_temp($wh_id, $sale_id)
	{
		return $this->session->userdata('wh_'.$wh_id.'_sale_base_currency_'.$sale_id);
	}

	protected function _unset_sale_currency_temp($wh_id, $sale_id)
	{
		$this->session->unset_userdata('wh_'.$wh_id.'_sale_currency_'.$sale_id);
		$this->session->unset_userdata('wh_'.$wh_id.'_sale_base_currency_'.$sale_id);
	}
	//---SALE CURRENCY ACTIONS---

	//SALE DISCOUNT
	public function set_sale_discount($wh_id, $sale_id, $sum)
	{
		$sum = round(floatval($sum), 2);
		$sum_array = $this->get_temp_sale_products_sum($wh_id, $sale_id);
		if($sum < $sum_array['subtotal_rate'])
		{
			$this->_set_sale_discount_temp($wh_id, $sale_id, $sum);
			return TRUE;
		}
		else
		{
			$this->_set_sale_discount_temp($wh_id, $sale_id, 0);
		}
		return FALSE;
	}

	protected function _set_sale_discount_temp($wh_id, $sale_id, $sum)
	{
		$this->session->set_userdata('wh_'.$wh_id.'_sale_discount_'.$sale_id, $sum);
	}

	protected function _get_sale_discount_temp($wh_id, $sale_id)
	{
		return $this->session->userdata('wh_'.$wh_id.'_sale_discount_'.$sale_id);
	}

	protected function _unset_sale_discount_temp($wh_id, $sale_id)
	{
		$this->session->unset_userdata('wh_'.$wh_id.'_sale_discount_'.$sale_id);
	}
	//---SALE DISCOUNT---
	public function unset_sale_temp($wh_id, $sale_id = 0)
	{
		$this->_unset_sale_products_temp($wh_id, $sale_id);
		$this->_unset_sale_customer_temp($wh_id, $sale_id);
		$this->_unset_sale_currency_temp($wh_id, $sale_id);
		$this->_unset_sale_discount_temp($wh_id, $sale_id);
		return TRUE;
	}

	public function save_sale($wh_id, $sale_id = 0)
	{
		$sale_data = array();
		$SALE_POST = $this->input->post('sale');

		list($added_products, $added_products_attributes, $sale_total) = $this->_prepare_save_sale_added_products($wh_id, $sale_id, $SALE_POST);
		if($added_products === FALSE || count($added_products) == 0) return FALSE;

		$sale_data[self::ID_WH] = $wh_id;
		$sale_data['wh_sale_state'] = 'C';

		$sale_data['discount'] = floatval($SALE_POST['discount']);
		$sale_data['total_qty'] = $sale_total['total_qty'];
		$sale_data['subtotal'] = $sale_total['subtotal'];
		$sale_data['total'] = $sale_total['total'];

		$s_currency = $this->_get_sale_currency_temp($wh_id, $sale_id);
		$this->load->model('catalogue/mcurrency');
		$base_currency = $this->mcurrency->get_users_base_currency();
		$sale_data['base_id_m_c_currency'] = $base_currency[Mcurrency::ID_CUR];
		$sale_data['base_currency_name'] = $base_currency['name'];
		$selected_currency = $this->mcurrency->get_users_currency_by_cid($SALE_POST[Mcurrency::ID_CUR]);
		$sale_data['id_m_c_currency'] = $selected_currency[Mcurrency::ID_CUR];
		$sale_data['currency_name'] = $selected_currency['name'];
		$sale_data['currency_rate'] = $s_currency['rate'];
		$sale_data['admin_note'] = $SALE_POST['admin_note'];

		$this->db->select("MAX(wh_sale_number) AS wh_sale_number")
				 ->from("`".self::WH_SALES."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$sale_number = $this->db->get()->row_array();
		$max = 1;
		if(count($sale_number)>0)
		{
			$max = intval($sale_number['wh_sale_number']) + 1;
		}
		$max = str_repeat("0", 8-strlen($max)).($max);
		$sale_data['wh_sale_number'] = $max;

		$this->load->model('warehouse/mwarehouses_logs');

		$this->db->trans_start();
		$log_id = $this->mwarehouses_logs->add_log($wh_id, 'SALE', $sale_data['admin_note']);

		$sale_id = $this->sql_add_data($sale_data + array(self::ID_WH_LOGS => $log_id))->sql_using_user()->sql_update_date()->sql_save(self::WH_SALES);

		if($SALE_ADDRESSES = $this->input->post('addresses'))
		{
			foreach($SALE_ADDRESSES as $key => $ms)
			{
				$address_data = $ms + array(self::ID_WH_SALES => $sale_id, 'type' => $key);
				$this->sql_add_data($address_data)->sql_save(self::WH_SALES_ADDR);
			}
		}

		foreach($added_products as $key => $ms)
		{
			$product_data = $ms + array(self::ID_WH_SALES => $sale_id);
			$sale_product_id = $this->sql_add_data($product_data)->sql_save(self::WH_SALES_PR);
			foreach($added_products_attributes[$key] as $attr)
			{
				$product_attr_data = $attr + array(self::ID_WH_SALES_PR => $sale_product_id, self::ID_WH_SALES => $sale_id);
				$this->sql_add_data($product_attr_data)->sql_save(self::WH_SALES_PR_ATTR);
			}
		}

		$this->load->model('warehouse/mwarehouses_invoices');
		$this->mwarehouses_invoices->create_wh_sale_invoice($sale_id, array('wh_invoice_state' => 'C'));
		$this->load->model('warehouse/mwarehouses_shippings');
		$this->mwarehouses_shippings->create_wh_sale_shipping($sale_id, array('wh_shipping_state' => 'C'));
		$this->load->model('warehouse/mwarehouses_products');
		$this->mwarehouses_products->create_wh_products_sale($wh_id, $added_products);

		$this->db->trans_complete();
		if($this->db->trans_status())
		{
			$this->unset_sale_temp($wh_id, $sale_id);
			return $sale_id;
		}
		return FALSE;
	}



	protected function _prepare_save_sale_added_products($wh_id, $sale_id, $sale_post)
	{
		$PR = $this->prepare_sale_view_products_data($wh_id, $sale_id);
		foreach($PR as $ms)
		{
			if($ms['wh_qty'] < 0) return array(FALSE, FALSE, FALSE);
		}
		$added_sale_products = $this->get_added_sale_products($wh_id, $sale_id);
		$sale_total = array('subtotal' => 0, 'total' => 0, 'total_qty' => 0);
		$added_products = array();
		$added_products_attributes = array();
		foreach($added_sale_products as $key => $ms)
		{
			$this->db->select("A.`".self::ID_PR."`, A.`sku`, B.`name`")
					 ->from("`".self::PR."` AS A")
					 ->join("`".self::PR_DESC."` AS B",
						 "B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
						 "LEFT")
					 ->where("A.`".self::ID_PR."`", $ms['id'])->limit(1);
			$product = $this->db->get()->row_array();
			if(count($product) > 0)
			{
				$attributes = $ms['options'];
				unset($attributes['price_id']);
				unset($attributes['real_qty']);
				list($price_array, $attributes_array) = $this->_prepare_product_price_n_attributes($ms['id'], $ms['options']['price_id'], $attributes);
				if($price_array)
				{
					$added_products[$key] = array(
						self::ID_PR => $product[self::ID_PR],
						'sku' => $product['sku'],
						'name' => $product['name'],
						'price' => $ms['price'],
						'qty' => $ms['qty'],
						'real_qty' => $price_array['real_qty'],
						'subtotal' => $ms['qty'] * $ms['price'],
						'total' => $ms['qty'] * $ms['price'],
						'price_alias' => $price_array['price_alias'],
						'price_name' => $price_array['price_name']
					);
					$sale_total['subtotal'] += $ms['price'] * $ms['qty'];
					$sale_total['total'] += $ms['price'] * $ms['qty'];
					$sale_total['total_qty'] += $ms['qty'] * $ms['options']['real_qty'];

					$added_products_attributes[$key] = array();
					foreach($attributes_array as $attr)
					{
						$added_products_attributes[$key][] = array(
							'attributes_alias' => $attr['a_alias'],
							'attributes_name' => $attr['a_name'],
							'attributes_options_alias' => $attr['o_alias'],
							'attributes_options_name' => $attr['o_name']
						);
					}
				}
			}
		}
		return array($added_products, $added_products_attributes, $sale_total);
	}

	public function save_order_sale($ord_id, $sale_sate = 'P')
	{
		$this->load->model('warehouse/mwarehouses');
		$wh_id = $this->mwarehouses->get_shop_wh();

		$this->load->model('sales/morders');
		$order_data = $this->morders->get_order_data($ord_id);
		$order_products = $this->morders->get_order_products($ord_id);

		$sale_data[self::ID_WH] = $wh_id;
		$sale_data[self::ID_ORD] = $order_data['order'][self::ID_ORD];
		$sale_data['wh_sale_state'] = $sale_sate;

		$sale_data['discount'] = $order_data['order']['discount'];
		$sale_data['total_qty'] = $order_data['order']['total_qty'];
		$sale_data['subtotal'] = $order_data['order']['subtotal'];
		$sale_data['total'] = $order_data['order']['total'];

		$sale_data['base_id_m_c_currency'] = $order_data['order']['base_id_m_c_currency'];
		$sale_data['base_currency_name'] = $order_data['order']['base_currency_name'];

		$sale_data['id_m_c_currency'] = $order_data['order']['id_m_c_currency'];
		$sale_data['currency_name'] = $order_data['order']['currency_name'];
		$sale_data['currency_rate'] = $order_data['order']['currency_rate'];

		$sale_data['admin_note'] = $order_data['order']['admin_note'];

		$this->db->select("MAX(wh_sale_number) AS wh_sale_number")
				 ->from("`".self::WH_SALES."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$sale_number = $this->db->get()->row_array();
		$max = 1;
		if(count($sale_number)>0)
		{
			$max = intval($sale_number['wh_sale_number']) + 1;
		}
		$max = str_repeat("0", 8-strlen($max)).($max);
		$sale_data['wh_sale_number'] = $max;

		$this->load->model('warehouse/mwarehouses_logs');

		$this->db->trans_start();
		$log_id = $this->mwarehouses_logs->add_log($wh_id, 'SALE', $sale_data['admin_note']);

		$sale_id = $this->sql_add_data($sale_data + array(self::ID_WH_LOGS => $log_id))->sql_using_user()->sql_update_date()->sql_save(self::WH_SALES);

		foreach($order_data['addresses'] as $key => $ms)
		{
			unset($ms['id_m_orders_address']);
			unset($ms['id_m_orders']);
			$address_data = $ms + array(self::ID_WH_SALES => $sale_id, 'type' => $key);
			$this->sql_add_data($address_data)->sql_save(self::WH_SALES_ADDR);
		}

		list($added_products, $added_products_attributes) = $this->_prepare_save_order_sale_products($order_products);
		foreach($added_products as $key => $ms)
		{
			$product_data = $ms + array(self::ID_WH_SALES => $sale_id);
			$sale_product_id = $this->sql_add_data($product_data)->sql_save(self::WH_SALES_PR);
			foreach($added_products_attributes[$key] as $attr)
			{
				$product_attr_data = $attr + array(self::ID_WH_SALES_PR => $sale_product_id, self::ID_WH_SALES => $sale_id);
				$this->sql_add_data($product_attr_data)->sql_save(self::WH_SALES_PR_ATTR);
			}
		}

		$this->load->model('warehouse/mwarehouses_invoices');
		$this->mwarehouses_invoices->create_wh_sale_invoice($sale_id, array('wh_invoice_state' => 'N'));

		$this->load->model('warehouse/mwarehouses_products');
		$this->mwarehouses_products->create_wh_products_sale($wh_id, $added_products);

		$this->db->trans_complete();
		if($this->db->trans_status())
		{
			$this->unset_sale_temp($wh_id, $sale_id);
			return $sale_id;
		}
		return FALSE;
	}

	protected function _prepare_save_order_sale_products($order_products)
	{
		$added_products = array();
		$added_products_attributes = array();
		foreach($order_products as $key => $ms)
		{
			$added_products[$key] = $ms;
			unset($added_products[$key]['id_m_orders_products']);
			unset($added_products[$key]['id_m_orders']);
			unset($added_products[$key]['attributes']);

			$added_products_attributes[$key] = array();
			foreach($ms['attributes'] as $attr)
			{
				unset($attr['id_m_orders_products_attributes']);
				unset($attr['id_m_orders']);
				unset($attr['id_m_orders_products']);
				$added_products_attributes[$key][] = $attr;
			}
		}
		return array($added_products, $added_products_attributes);
	}

	public function get_order_sale($ord_id)
	{
		$this->db->select("*")
			->from("`".self::WH_SALES."`")
			->where("`".self::ID_ORD."`", $ord_id)->where("`".self::ID_USERS."`", $this->id_users)
			->where("wh_sale_state <>", 'CN')->limit(1);
		if(count($sale = $this->db->get()->row_array()) > 0)
		{
			return $sale;
		}
		return FALSE;
	}

	public function change_order_sale_state($ord_id, $sale_state)
	{
		if($sale_data = $this->get_order_sale($ord_id))
		{
			$this->sql_add_data(array('wh_sale_state' => $sale_state))->sql_update_date()->sql_save(self::WH_SALES, $sale_data[self::ID_WH_SALES]);
			if($sale_state == 'CN')
			{
				$this->load->model('warehouse/mwarehouses_products');
				$this->mwarehouses_products->cancel_wh_products_sale($sale_data[self::ID_WH], $this->get_sale_products($sale_data[self::ID_WH_SALES]));
			}
			return TRUE;
		}
		return FALSE;
	}

	public function change_order_sale_invoice_state($ord_id, $invoice_state)
	{
		if($sale_data = $this->get_order_sale($ord_id))
		{
			$this->load->model('warehouse/mwarehouses_invoices');
			if(!$this->mwarehouses_invoices->change_sale_invoice_state($sale_data[self::ID_WH_SALES], $invoice_state)) return FALSE;
			return TRUE;
		}
		return FALSE;
	}

	public function create_order_sale_shipping($ord_id)
	{
		if($sale_data = $this->get_order_sale($ord_id))
		{
			$this->load->model('warehouse/mwarehouses_shippings');
			$this->mwarehouses_shippings->create_wh_sale_shipping($sale_data[self::ID_WH_SALES], array('wh_shipping_state' => 'N'));
			return TRUE;
		}
		return FALSE;
	}

	public function change_order_sale_shipping_state($ord_id, $shipping_state)
	{
		if($sale_data = $this->get_order_sale($ord_id))
		{
			$this->load->model('warehouse/mwarehouses_shippings');
			if(!$this->mwarehouses_shippings->change_sale_shipping_state($sale_data[self::ID_WH_SALES], $shipping_state)) return FALSE;
			return TRUE;
		}
		return FALSE;
	}

	public function create_order_sale_credit_memo($ord_id)
	{
		if($sale_data = $this->get_order_sale($ord_id))
		{
			$this->load->model('warehouse/mwarehouses_credit_memo');
			if(!$this->mwarehouses_credit_memo->create_wh_sale_credit_memo($sale_data[self::ID_WH_SALES])) return FALSE;
			$this->load->model('warehouse/mwarehouses_products');
			$this->mwarehouses_products->cancel_wh_products_sale($sale_data[self::ID_WH], $this->get_sale_products($sale_data[self::ID_WH_SALES]));
			return TRUE;
		}
		return FALSE;
	}

	public function is_valid_md5($md5 = '')
	{
		return preg_match('/^[a-f0-9]{32}$/', $md5);
	}

	public function isset_sale($sale_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				 ->from("`".self::WH_SALES."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_WH_SALES."`", $sale_id);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] > 0)
		{
			return TRUE;
		}
		return FALSE;
	}
}
?>