<?php
class Morders extends AG_Model
{
	const ORD 				= 'm_orders';
	const ID_ORD 			= 'id_m_orders';
	const INV 				= 'm_orders_invoices';
	const ID_INV 			= 'id_m_orders_invoices';
	const ORD_ADDR 			= 'm_orders_address';
	const ID_ORD_ADDR 		= 'id_m_orders_address';
	
	const CT 				= 'm_u_customers';
	const ID_CT 			= 'id_m_u_customers';
	const CT_ADDR 			= 'm_u_customers_address';
	const CT_TYPE 			= 'm_u_types';
	const ID_CT_TYPE 		= 'id_m_u_types';
	const CT_TYPE_DESC 		= 'm_u_types_description';
	const CT_N_TYPE			= 'm_u_customers_types';
	
	const ORD_PR 			= 'm_orders_products';
	const ID_ORD_PR 		= 'id_m_orders_products';
	const ORD_PR_ATTR 		= 'm_orders_products_attributes';
	const ID_ORD_PR_ATTR 	= 'id_m_orders_products_attributes';
	
	const PR 			= 'm_c_products';
	const ID_PR 		= 'id_m_c_products';
	const PR_DESC 		= 'm_c_products_description';
	const ID_PR_DESC 	= 'id_m_c_products_description';
	
	const PR_IMG 		= 'm_c_products_images';
	const ID_PR_IMG 	= 'id_m_c_products_images';
	
	const PR_PRICE 			= 'm_c_products_price';
	const ID_PR_PRICE 		= 'id_m_c_products_price';
	const PR_PRICE_DESC 	= 'm_c_products_price_description';
	const ID_PR_PRICE_DESC 	= 'id_m_c_products_price_description';
	
	const NATTRIBUTES 	= 'm_c_productsNattributes';
	
	const PR_ATTR = 'm_c_products_attributes';
	const ID_PR_ATTR = 'id_m_c_products_attributes';
	const PR_ATTR_DESC = 'm_c_products_attributes_description';
	
	const PR_ATTR_OPT = 'm_c_products_attributes_options';
	const ID_PR_ATTR_OPT = 'id_m_c_products_attributes_options';
	const PR_ATTR_OPT_DESC = 'm_c_products_attributes_options_description';
	
	const PM 		= 'm_payment_methods';
	const ID_PM 	= 'id_m_payment_methods';
	const UPM 		= 'm_users_payment_methods';
	const ID_UPM 	= 'id_m_users_payment_methods';
	const UPM_DESC 	= 'm_users_payment_methods_description';
	
	const SM 		= 'm_shipping_methods';
	const ID_SM 	= 'id_m_shipping_methods';
	const USM 		= 'm_users_shipping_methods';
	const ID_USM 	= 'id_m_users_shipping_methods';
	const USM_DESC	= 'm_users_shipping_methods_description';
	const SM_FIELDS		= 'm_shipping_methods_fields';
	const ID_SM_FIELDS	= 'id_m_shipping_methods_fields';
	
	const IMG_FOLDER = '/media/catalogue/products/';
	
	const WH 		= 'wh';
	const ID_WH 	= 'id_wh';
	const WH_SH 		= 'wh_shops';
	const ID_WH_SH 		= 'id_wh_shops';
	const WHNSH			= 'wh_whNshops';
	const WH_PR = 'wh_products';

	public $ord_id = 0;
	
	public function __construct()
	{
		parent::__construct();
		$this->img_path = IMG_PATH.ID_USERS.self::IMG_FOLDER;
	}

	public static function get_order_state_collection()
	{
		return array('N' => 'Новый', 'P' => 'В процессе', 'I' => 'Ожидание оплаты', 'IS' => 'Проверка оплаты', 'IC' => 'Оплачен', 'S' => 'Процесс отправки', 'C' => 'Завершен', 'CN' => 'Отменен', 'H' => 'Временно заморожен', 'COD' => 'Наложенный платеж', 'COD_S' => 'Процесс отправки(Н.П.)', 'COD_S_С' => 'Отправлен(Н.П.)', 'CM' => 'Возврат');
	}

	public static function get_order_state_name($key)
	{
		$state_collection = self::get_order_state_collection();
		if(isset($state_collection[$key]))
		{
			return $state_collection[$key];
		}
		return FALSE;
	}

	public function render_orders_collection_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("orders_grid", array('sort' => 'orders_number', 'desc' => 'DESC'));
		
		$this->grid->db
				->select("A.`".self::ID_ORD."` AS ID, A.`orders_number` AS `orders_number`, A.`orders_state`, CONCAT(A.`total`, ' ', A.`base_currency_name`) AS total, A.`id_m_c_currency`,A.`currency_name`, A.`currency_rate`, A.`create_date`, A.`update_date`, B.`name`")
				->from("`".self::ORD."` AS A")
				->join(	"`".self::ORD_ADDR."` AS B",
						"B.`".self::ID_ORD."` = A.`".self::ID_ORD."` && B.`type` = 'B'",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users);
		$this->load->helper("orders");
		helper_orders_grid_build($this->grid);
		
		$this->grid->create_grid_data();
		$arr = $this->grid->get_grid_data();
		$this->grid->set_grid_data($arr);
		$this->grid->update_grid_data("orders_state", self::get_order_state_collection());
		$this->grid->render_grid();
	}

	public function render_not_edited_order_products_grid($ord_id)
	{
		$this->db->select("A.`".self::ID_PR."` AS PR_ID, A.`".self::ID_ORD_PR."` AS `ORD_PR_ID`, A.`sku`, A.`name`, CONCAT('<span class=\'label\'>', A.`price_name`, '</span>', ' ', ROUND(A.`price`, 2)) AS `price`, IF(A.`real_qty` <> 1, CONCAT(A.`qty`,'(<span class=\'label\'>',A.`qty`*A.`real_qty`, '</span>)'), A.`qty`) AS qty_str, A.`qty`, A.`real_qty`, ROUND(A.`total`, 2) AS total, GROUP_CONCAT(CONCAT(B.`attributes_name`, ' : ', B.`attributes_options_name`) SEPARATOR '<BR>') AS attributes")
				 ->from("`".self::ORD_PR."` AS A")
				 ->join(	"`".self::ORD_PR_ATTR."` AS B",
					 "B.`".self::ID_ORD_PR."` = A.`".self::ID_ORD_PR."`",
					 "LEFT")
				 ->where("A.`".self::ID_ORD."`", $ord_id)->group_by("A.`".self::ID_ORD_PR."`");

		$product_array = $this->db->get()->result_array();

		$i = 1;
		foreach($product_array as $key => $ms)
		{
			$product_array[$key]['number'] = $i;
			$i++;
		}
		$this->load->library("nosql_grid");
		$this->nosql_grid->_init_grid("orders_products_grid", array('sort' => "A.".self::ID_ORD_PR, 'url' => FALSE));
		$this->nosql_grid->init_fixed_buttons(FALSE);
		$this->load->helper('orders');
		helper_not_edited_orders_products_grid_build($this->nosql_grid);

		$this->nosql_grid->set_grid_data($product_array);
		return $this->nosql_grid->render_grid(TRUE);
	}

	public function render_order_products_grid($ord_id = 0, $ajax = FALSE)
	{
		$this->load->library("nosql_grid");
		$this->nosql_grid->_init_grid("orders_products_grid", array('sort' => "A.".self::ID_ORD_PR, 'url' => FALSE));
		$this->nosql_grid->init_fixed_buttons(FALSE);
		$this->nosql_grid->ajax_output = $ajax;

		$product_array = $this->prepare_order_view_products_data($ord_id);
		$this->load->helper('orders');
		$wh_id = $this->mwarehouses->get_shop_wh();
		if($wh_id)
		{
			helper_orders_wh_products_grid_build($this->nosql_grid, $ord_id);
		}
		else
		{
			helper_orders_products_grid_build($this->nosql_grid, $ord_id);
		}

		$this->nosql_grid->set_grid_data($product_array);
		return $this->nosql_grid->render_grid(TRUE);
	}

	public function render_shop_products_grid($ord_id = 0)
	{
		$this->load->model('catalogue/mproducts');
		$this->mproducts->prepare_products_grid_query();

		$this->grid->keep_filter_data(FALSE);
		$this->grid->_init_grid("orders_shop_products_grid", array('url' => set_url('sales/orders/ajax_get_shop_products_grid/ord_id/'.$ord_id)));

		$this->load->helper('orders');
		helper_shop_products_grid_build($this->grid, $ord_id);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('in_stock', array('0'=>'Нет', '1'=>'Да'));
		$this->grid->update_grid_data('status', array('0'=>'Нет', '1'=>'Да'));
		return $this->grid->render_grid(TRUE);
	}

	public function render_customers_grid($ord_id = 0)
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
	}

	public function prepare_order_view_products_data($ord_id = 0)
	{
		$product_array = array();
		$this->load->model('warehouse/mwarehouses');
		$wh_id = $this->mwarehouses->get_shop_wh();
		if($ord_id)
		{
			if($wh_id)
			{
				$this->db->select("A.`".self::ID_PR."` AS PR_ID, A.`".self::ID_ORD_PR."` AS `ORD_PR_ID`, A.`sku`, A.`name`, CONCAT('<span class=\'label\'>', A.`price_name`, '</span>', ' ', ROUND(A.`price`, 2)) AS `price`, IF(A.`real_qty` <> 1, CONCAT(A.`qty`,'(<span class=\'label\'>',A.`qty`*A.`real_qty`, '</span>)'), A.`qty`) AS qty_str, A.`qty`, A.`real_qty`, ROUND(A.`total`, 2) AS total, GROUP_CONCAT(CONCAT(B.`attributes_name`, ' : ', B.`attributes_options_name`) SEPARATOR '<BR>') AS attributes")
						 ->from("`".self::ORD_PR."` AS A")
						 ->join(	"`".self::ORD_PR_ATTR."` AS B",
							 "B.`".self::ID_ORD_PR."` = A.`".self::ID_ORD_PR."`",
							 "LEFT")
						 ->where("A.`".self::ID_ORD."`", $ord_id)->group_by("A.`".self::ID_ORD_PR."`");
			}
			else
			{
				$this->db->select("A.`".self::ID_PR."` AS PR_ID, A.`".self::ID_ORD_PR."` AS `ORD_PR_ID`, A.`sku`, A.`name`, CONCAT('<span class=\'label\'>', A.`price_name`, '</span>', ' ', ROUND(A.`price`, 2)) AS `price`, IF(A.`real_qty` <> 1, CONCAT(A.`qty`,'(<span class=\'label\'>',A.`qty`*A.`real_qty`, '</span>)'), A.`qty`) AS qty_str, A.`qty`, A.`real_qty`, ROUND(A.`total`, 2) AS total, GROUP_CONCAT(CONCAT(B.`attributes_name`, ' : ', B.`attributes_options_name`) SEPARATOR '<BR>') AS attributes")
						 ->from("`".self::ORD_PR."` AS A")
						 ->join(	"`".self::ORD_PR_ATTR."` AS B",
							 "B.`".self::ID_ORD_PR."` = A.`".self::ID_ORD_PR."`",
							 "LEFT")
						 ->where("A.`".self::ID_ORD."`", $ord_id)->group_by("A.`".self::ID_ORD_PR."`");
			}
			$product_array = $this->db->get()->result_array();
		}
		if(count($delete_prod_array = $this->get_deleted_order_products($ord_id)) > 0)
		{
			foreach($product_array as $key => $ms)
			{
				if(isset($delete_prod_array[$ms['ORD_PR_ID']])) unset($product_array[$key]);
			}
		}

		if(count($edit_prod_array = $this->get_edited_order_products($ord_id)) > 0 )
		{
			foreach($product_array as $key => $ms)
			{
				if(isset($edit_prod_array[$ms['ORD_PR_ID']]))
				{
					$product_array[$key]['subtotal'] = round(($edit_prod_array[$ms['ORD_PR_ID']]['subtotal']), 2);
					$product_array[$key]['total'] = round(($edit_prod_array[$ms['ORD_PR_ID']]['total']), 2);
					$product_array[$key]['qty'] = $edit_prod_array[$ms['ORD_PR_ID']]['qty'];
					if($product_array[$key]['real_qty'] != 1) $product_array[$key]['qty_str'] = $edit_prod_array[$ms['ORD_PR_ID']]['qty'].'(<span class=\'label\'>'.$edit_prod_array[$ms['ORD_PR_ID']]['qty'] * $edit_prod_array[$ms['ORD_PR_ID']]['real_qty'].'</span>)';
					else $product_array[$key]['qty_str'] = $edit_prod_array[$ms['ORD_PR_ID']]['qty'];
				}
			}
		}

		if(count($cart_array = $this->get_added_order_products($ord_id))>0)
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
					$product_array[$key] = array('PR_ID' => $cart_products_array[$ms['id']]['PR_ID'], 'ORD_PR_ID' => $key, 'sku' => $cart_products_array[$ms['id']]['sku'], 'name' => $cart_products_array[$ms['id']]['name'], 'price' => round($ms['price'], 2), 'qty' => $ms['qty'], 'real_qty' => $ms['options']['real_qty'], 'total' => round(($ms['price'] * $ms['qty']), 2));
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
			if($wh_id)
			{
				$pr_id_array = array();
				foreach($product_array as $ms)
				{
					$pr_id_array[$ms['PR_ID']] = $ms['PR_ID'];
				}
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
			else
			{
				$i = 1;
				foreach($product_array as $key => $ms)
				{
					$product_array[$key]['number'] = $i;
					$i++;
				}
			}
		}
		return $product_array;
	}
	
	public function add_order()
	{
		$this->unset_order_temp(0);
		$order_data = array();

		$this->load->model('sales/mpayment_methods');
		$this->load->model('sales/mshipping_methods');
		$this->load->model('catalogue/mcurrency');

		list($order_data['users_langs']['langs'], $order_data['users_langs']['default']) = $this->mlangs->get_users_languages_to_select();
		list($order_data['users_payment_methods']['payment_methods'], $order_data['users_payment_methods']['default']) = $this->mpayment_methods->get_users_payment_methods_to_select();
		list($order_data['users_shipping_methods']['shipping_methods'], $order_data['users_shipping_methods']['default']) = $this->mshipping_methods->get_users_shipping_methods_to_select();
		list($order_data['users_currency']['currency'], $order_data['users_currency']['default']) = $this->mcurrency->get_users_currency_to_select();
		$order_data['base_currency'] = $this->mcurrency->get_users_base_currency();
		$order_data['default_selected_currency'] = $this->mcurrency->get_users_currency_by_cid($order_data['users_currency']['default']);
		$order_data['orders_states'] = self::get_order_state_collection();
		unset($order_data['orders_states']['CN']);
		unset($order_data['orders_states']['H']);
		unset($order_data['orders_states']['CM']);
		$order_data['order_products_grid'] = $this->render_order_products_grid();
		$this->load->helper('orders');

		$this->_set_order_currency_temp($order_data['default_selected_currency'], 0);
		$this->_set_order_base_currency_temp($order_data['base_currency'], 0);

		helper_orders_form_add($order_data);
	}
	
	public function view_order($ord_id)
	{
		$this->unset_order_temp($ord_id);
		$order_data = array();

		$order = $this->get_order_data($ord_id);

		$this->load->model('sales/mpayment_methods');
		$this->load->model('sales/mshipping_methods');
		$this->load->model('catalogue/mcurrency');

		list($order_data['users_langs']['langs'], $order_data['users_langs']['default']) = $this->mlangs->get_users_languages_to_select();
		list($order_data['users_payment_methods']['payment_methods'], $order_data['users_payment_methods']['default']) = $this->mpayment_methods->get_users_payment_methods_to_select();
		list($order_data['users_shipping_methods']['shipping_methods'], $order_data['users_shipping_methods']['default']) = $this->mshipping_methods->get_users_shipping_methods_to_select();
		list($order_data['users_currency']['currency'], $order_data['users_currency']['default']) = $this->mcurrency->get_users_currency_to_select();
		$order_data['orders_states'] = self::get_order_state_collection();
		if($order['order']['orders_state'] != 'N') $order_data['order_products_grid'] = $this->render_not_edited_order_products_grid($ord_id);
		else $order_data['order_products_grid'] = $this->render_order_products_grid($ord_id);

		$order_data += $order;
		if($order['order']['orders_state'] == 'N')
		{
			$currency_temp = array('id_m_c_currency' => $order['order']['id_m_c_currency'], 'name' => $order['order']['currency_name'], 'rate' => $order['order']['currency_rate']);
			$this->_set_order_currency_temp($currency_temp, $ord_id);
			$base_currency_temp = array('id_m_c_currency' => $order['order']['base_id_m_c_currency'], 'name' => $order['order']['base_currency_name'], 'rate' => 1);
			$this->_set_order_base_currency_temp($base_currency_temp, $ord_id);

			$this->_set_order_discount_temp($order['order']['discount'], $ord_id);
		}
		$this->load->helper('orders');
		helper_orders_form_view($order_data, $ord_id);
	}

	public function get_view_order($ord_id)
	{
		if($order = $this->get_order_data($ord_id))
		{
			$not_edited = FALSE;
			if($order['order']['orders_state'] != 'N') $not_edited = TRUE;
			if($not_edited) $order['products'] = $this->render_not_edited_order_products_grid($ord_id, $not_edited);
			else $order['products'] = $this->render_order_products_grid($ord_id);
			$order['customer'] = FALSE;
			if($order['order']['id_m_u_customers'] != NULL)
			{
				$order['customer'] = $this->get_order_customer($order['order']['id_m_u_customers']);
			}
			return $order;
		}
		return FALSE;
	}

	public function get_order_data($ord_id)
	{
		$order = array();
		if($order['order'] = $this->get_order($ord_id))
		{
			$order['order'] += $this->get_order_sum($ord_id);
			$order['order']['orders_status_name'] = $this->get_order_state_name($order['order']['orders_state']);
			$order['customer'] = $this->get_order_customer($order['order']['id_m_u_customers']);

			$order['addresses'] = $this->get_order_addresses($ord_id);

			$this->load->model('sales/minvoices');
			$order['invoice'] = $this->minvoices->get_order_invoice($order['order'][self::ID_ORD]);
			if($order['invoice'])
			{
				$order['invoice']['invoices_status_name'] = Minvoices::get_invoice_state_name($order['invoice']['invoices_status']);
			}

			$this->load->model('sales/mshippings');
			$order['shipping'] = $this->mshippings->get_order_shipping($order['order'][self::ID_ORD]);
			if($order['shipping'])
			{
				$order['shipping']['shippings_status_name'] = Mshippings::get_shipping_state_name($order['shipping']['shippings_status']);
			}

			if($order['order'][self::ID_UPM] != NULL)
			{
				$order['order']['payment_method'] = $order['order']['payment_method_alias'].' - '.$order['order']['pm_name'];
			}
			if($order['order'][self::ID_USM] != NULL)
			{
				$order['order']['shipping_method'] = $order['order']['shipping_method_alias'].' - '.$order['order']['sm_name'];
			}
			return $order;
		}
		return FALSE;
	}

	public function get_order($ord_id)
	{
		$this->db->select("A.*,
							PM.`".self::ID_UPM."`, PM.`alias` AS pm_alias, PM_DESC.`name` AS pm_name,
							SM.`".self::ID_USM."`, SM.`alias` as sm_alias, SM_DESC.`name` AS sm_name,
							L.`name` AS l_name")
			->from("`".self::ORD."` AS A")
			->join(	"`".self::UPM."` AS PM",
					"PM.`".self::ID_UPM."` = A.`".self::ID_UPM."`",
					"LEFT")
			->join(	"`".self::UPM_DESC."` AS PM_DESC",
					"PM_DESC.`".self::ID_UPM."` = A.`".self::ID_UPM."` && PM_DESC.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`",
					"LEFT")
			->join(	"`".self::USM."` AS SM",
					"SM.`".self::ID_USM."` = A.`".self::ID_USM."`",
					"LEFT")
			->join(	"`".self::USM_DESC."` AS SM_DESC",
					"SM_DESC.`".self::ID_USM."` = A.`".self::ID_USM."` && SM_DESC.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`",
					"LEFT")
			->join(	"`".self::LANGS."` AS L",
					"L.`".self::ID_LANGS."` = A.`".self::ID_LANGS."`",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_ORD."`", $ord_id)->limit(1);
		$order = $this->db->get()->row_array();
		if(count($order)>0)
		{
			return $order;
		}
		return FALSE;
	}

	public function get_order_sum($ord_id)
	{
		$order_sum = array();
		$this->db->select("A.*")
				 ->from("`".self::ORD."` AS A")
				 ->where("A.`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_ORD."`", $ord_id)->limit(1);
		$order = $this->db->get()->row_array();
		if(count($order)>0)
		{
			$total_qty = $order['total_qty'];
			if(($discount = floatval($order['discount'])) > 0)
			{
				$subtotal = $order['subtotal'];
				$total = $order['total'];

				$subtotal_rate = $subtotal * $order['currency_rate'];
				$total_rate = $total * $order['currency_rate'] - $discount;
			}
			else
			{
				$subtotal = $order['subtotal'];
				$total = $order['total'];

				$subtotal_rate = $subtotal * $order['currency_rate'];
				$total_rate = $total * $order['currency_rate'];
			}

			if($order['base_id_m_c_currency'] == $order['id_m_c_currency'])
			{
				$subtotal_string = round($subtotal_rate, 2).' '.$order['currency_name'];
			}
			else
			{
				$subtotal_string = round($subtotal_rate, 2).' '.$order['currency_name'].' ('.round($subtotal, 2).' '.$order['base_currency_name'].')';
			}
			$total_string = round($total_rate, 2).' '.$order['currency_name'];

			$order_sum = array(
				'total_qty_string' => $total_qty, 'subtotal_string' => $subtotal_string, 'total_string' => $total_string,
				'total_qty' => $total_qty,
				'subtotal' => $subtotal, 'total' => $total,
				'subtotal_rate' => $subtotal_rate, 'total_rate' => $total_rate,
				'discount' => $discount
			);
		}
		return $order_sum;
	}

	public function get_order_addresses($ord_id)
	{
		$addresses = array();
		$this->db->select("*")
			  ->from("`".self::ORD_ADDR."`")
			  ->where("`".self::ID_ORD."`", $ord_id)->limit(2);
		$result = $this->db->get()->result_array();
		foreach($result as $ms)
		{
			$addresses[$ms['type']] = $ms;
		}
		return $addresses;
	}

	public function get_order_customer($custome_id)
	{
		$this->load->model('customers/mcustomers');
		return $this->mcustomers->get_customer($custome_id);
	}

	public function get_order_products($ord_id)
	{
		$products = array();
		$this->db->select("*")
			->from("`".self::ORD_PR."` AS A")
			->where("`".self::ID_ORD."`", $ord_id);
		$result = $this->db->get()->result_array();
		$ord_pr_id = array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$ord_pr_id[$ms[self::ID_ORD_PR]] = $ms[self::ID_ORD_PR];
				$products[$ms[self::ID_ORD_PR]] = $ms;
				$products[$ms[self::ID_ORD_PR]]['attributes'] = array();
			}
			$this->db->select("*")
				->from("`".self::ORD_PR_ATTR."`")
				->where_in("`".self::ID_ORD_PR."`", $ord_pr_id);
			$at_result = $this->db->get()->result_array();
			foreach($at_result as $ms)
			{
				$products[$ms[self::ID_ORD_PR]]['attributes'][] = $ms;
			}
			return $products;
		}
		return $products;
	}

	public function get_order_product($ord_id, $ord_pr_id)
	{
		if($ord_id > 0 && $this->isset_order($ord_id))
		{
			$this->db->select("*")
				->from("`".self::ORD_PR."` AS A")
				->where("A.`".self::ID_ORD_PR."`", $ord_pr_id);
			$result = $this->db->get()->row_array();
			if(count($result)>0)
			{
				$product = $result;
				$product['attributes'] = array();
				$this->db->select("*")
					->from("`".self::ORD_PR_ATTR."`")
					->where("`".self::ID_ORD_PR."`", $ord_pr_id);
				$at_result = $this->db->get()->result_array();
				foreach($at_result as $ms)
				{
					$product['attributes'][] = $ms;
				}
				return $product;
			}
			return FALSE;
		}
		return FALSE;
	}

	public function get_order_product_by_pr_id($ord_id, $pr_id)
	{
		if($ord_id > 0 && $this->isset_order($ord_id))
		{
			$products = array();
			$this->db->select("*")
				->from("`".self::ORD_PR."` AS A")
				->where("A.`".self::ID_ORD."`", $ord_id)->where("A.`".self::ID_PR."`", $pr_id);
			$result = $this->db->get()->row_array();
			if(count($result)>0)
			{
				foreach($result as $ms)
				{
					$products[$ms[self::ID_ORD_PR]] = $ms;
					$products[$ms[self::ID_ORD_PR]]['attributes'] = array();
					$this->db->select("*")
						->from("`".self::ORD_PR_ATTR."`")
						->where("`".self::ID_ORD_PR."`", $ms[self::ID_ORD_PR]);
					$at_result = $this->db->get()->result_array();
					foreach($at_result as $ms1)
					{
						$products[$ms1[self::ID_ORD_PR]]['attributes'][] = $ms1;
					}
				}
				return $products;
			}
			return FALSE;
		}
		return FALSE;
	}

	public function get_view_product_data($pr_id, $ord_id = FALSE)
	{
		$this->load->model('catalogue/mproducts_view');
		if($product_array = $this->mproducts_view->get_product($pr_id))
		{
			return $product_array;
		}
		return FALSE;
	}

//ORDER CUSTOMER ACTION
	public function set_order_customer($cm_id, $ord_id = 0)
	{
		$this->load->model('customers/mcustomers');
		if($this->mcustomers->check_isset_ct($cm_id))
		{
			$customer['customer'] = $this->mcustomers->get_customer($cm_id);
			$customer['addresses'] = $this->mcustomers->get_customer_addresses($cm_id);
			$this->_set_order_customer_temp($customer['customer'], $ord_id);
			return $customer;
		}
		return FALSE;
	}

	public function unset_order_customer($ord_id = 0)
	{
		if($ord_id > 0)
		{
			$this->_set_order_customer_temp(array('ID' => NULL), $ord_id);
		}
		else
		{
			$this->_unset_order_customer_temp($ord_id);
		}
		return TRUE;
	}

	protected function _set_order_customer_temp($customer, $ord_id)
	{
		$this->session->set_userdata('order_customer_id_'.$ord_id, $customer);
	}

	protected function _unset_order_customer_temp($ord_id)
	{
		$this->session->unset_userdata('order_customer_id_'.$ord_id);
	}

	protected function _get_order_customer_temp($ord_id)
	{
		return $this->session->userdata('order_customer_id_'.$ord_id);
	}
//----ORDER CUSTOMER ACTION----

//ORDER PRODUCTS ACTIONS
	public function add_product_to_order($ord_id = 0)
	{
		$this->_init_cart($ord_id);
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
			$data['price'] = $price_array['price'];

			$data['options'] = array();
			$data['options']['price_id'] = $POST['price_id'];
			$data['options']['real_qty'] = $price_array['real_qty'];
			foreach($attributes_array as $ms)
			{
				$data['options'][$ms['ID']] = $ms['ID_OP'];
			}

			$this->load->model('warehouse/mwarehouses');
			$wh_id = $this->mwarehouses->get_shop_wh();
			if($ord_id)
			{
				if(!$this->isset_order($ord_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
				if($wh_id)
				{
					$isset_catr_item_qty = 0;
					if($isset_cart_product = $this->cart->isset_cart_item($data))
					{
						$isset_catr_item_qty = $isset_cart_product['qty']*$isset_cart_product['options']['real_qty'];
					}

					$cart_pr_total_qty = $this->calculate_temp_order_product_qty($POST['product_id'], $ord_id);
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

					return array('success' => TRUE, 'message' => $message);
				}
				else
				{
					$isset_cart_product = $this->cart->isset_cart_item($data);
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

					return array('success' => TRUE, 'message' => $message);
				}
			}
			else
			{
				if($wh_id)
				{
					$isset_catr_item_qty = 0;
					if($isset_cart_product = $this->cart->isset_cart_item($data))
					{
						$isset_catr_item_qty = $isset_cart_product['qty']*$isset_cart_product['options']['real_qty'];
					}

					$cart_pr_total_qty = $this->calculate_temp_order_product_qty($POST['product_id']);
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

					$this->_add_product_to_order($data, $ord_id);
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
				else
				{
					$isset_cart_product = $this->cart->isset_cart_item($data);
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

					return array('success' => TRUE, 'message' => $message);
				}
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

	public function edit_order_product_qty($ord_pr_id, $ord_id = 0)
	{
		if(($qty = intval($this->input->post('ord_pr_qty')))>0)
		{
			$this->load->model('warehouse/mwarehouses');
			$wh_id = $this->mwarehouses->get_shop_wh();
			if($ord_id > 0)
			{
				if(!$this->isset_order($ord_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
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
				}
			}
			else
			{
				if(!$this->is_valid_md5($ord_pr_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
				if($wh_id)
				{
					$cart = $this->get_added_order_products($ord_id);
					if(!isset($cart[$ord_pr_id])) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
					$cart_pr_array = $cart[$ord_pr_id];

					$cart_pr_total_qty = $this->calculate_temp_order_product_qty($cart_pr_array['id'], $ord_id);
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

					$this->_edit_order_product($ord_pr_id, $ord_id, $qty);

					if($real_qty_coef > 1) $message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']."(".$cart_pr_array['qty']*$real_qty_coef.") на ".$qty."(".$qty*$real_qty_coef.").";
					else $message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']." на ".$qty.".";

					return array('success' => TRUE, 'message' => $message);
				}
				else
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
			}
		}
		return array('success' => FALSE, 'message' => 'Server Error. Try later!');
	}

	public function delete_product_from_order($ord_pr_id, $ord_id = 0)
	{
		if($this->_delete_order_product($ord_pr_id, $ord_id))
		{
			$message = "Позиция успешно удалена.";
			return array('success' => TRUE, 'message' => $message);
		}
		return array('success' => FALSE, 'message' => 'Server Error. Try later!');
	}

	public function get_temp_order_products_sum($ord_id = 0)
	{
		$product_array = array();
		if($ord_id > 0)
		{
			$this->db->select("*")
				->from("`".self::ORD_PR."`")
				->where("`".self::ID_ORD."`", $ord_id);
			$product_array = $this->db->get()->result_array();
		}
		$total_qty = 0;
		$total_sum = 0;
		$edit_prod_array = $this->get_edited_order_products($ord_id);

		if(count($delete_prod_array = $this->get_deleted_order_products($ord_id)) > 0)
		{
			foreach($product_array as $key => $ms)
			{
				if(isset($delete_prod_array[$ms[self::ID_ORD_PR]])) unset($product_array[$key]);
			}
		}

		foreach($product_array as $ms)
		{
			if(isset($edit_prod_array[$ms[self::ID_ORD_PR]]))
			{
				$total_qty += $edit_prod_array[$ms[self::ID_ORD_PR]]['qty'] * $edit_prod_array[$ms[self::ID_ORD_PR]]['real_qty'];
				$total_sum += $edit_prod_array[$ms[self::ID_ORD_PR]]['total'];
			}
			else
			{
				$total_qty += $ms['qty'] * $ms['real_qty'];
				$total_sum += $ms['total'];
			}
		}

		$cart_array = $this->get_added_order_products($ord_id);
		foreach($cart_array as $ms)
		{
			$total_qty += $ms['qty'] * $ms['options']['real_qty'];
			$total_sum += $ms['price'] * $ms['qty'];
		}

		$currency = $this->_get_order_currency_temp($ord_id);
		$base_currency = $this->_get_order_base_currency_temp($ord_id);
		if($discount = $this->_get_order_discount_temp($ord_id))
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

	protected function get_added_order_products($ord_id = 0)
	{
		$this->_init_cart($ord_id);
		return $this->cart->contents();
	}

	protected function get_edited_order_products($ord_id = 0)
	{
		$products_array = array();
		if($this->session->userdata('order_edit_products_'.$ord_id))
		{
			$products_array = $this->session->userdata('order_edit_products_'.$ord_id);
		}
		return $products_array;
	}

	protected function get_deleted_order_products($ord_id = 0)
	{
		$products_array = array();
		if($this->session->userdata('order_delete_products_'.$ord_id))
		{
			$products_array = $this->session->userdata('order_delete_products_'.$ord_id);
		}
		return $products_array;
	}

	private function _init_cart($ord_id = 0)
	{
		$this->load->library('cart');
		$this->cart->set_cart_key_suffix('_'.$ord_id);
	}

	protected function _add_product_to_order($data, $ord_id)
	{
		$this->_init_cart($ord_id);
		return $this->cart->insert($data);
	}

	protected function _edit_order_product($ord_pr_id, $ord_id, $qty, $order_product_array = array())
	{
		if($qty <= 0) return FALSE;
		if($this->is_valid_md5($ord_pr_id))
		{
			$this->cart->update(array('rowid' => $ord_pr_id, 'qty' => $qty));
			return TRUE;
		}
		else
		{
			$edit_prod_array = $this->get_edited_order_products($ord_id);
			$edit_prod_array[$ord_pr_id] = array('qty' => $qty, 'real_qty' => $order_product_array['real_qty'], 'subtotal' =>$qty*$order_product_array['price'], 'total' => $qty*$order_product_array['price']);
			$this->session->set_userdata('order_edit_products_'.$ord_id, $edit_prod_array);
			return TRUE;
		}
	}

	protected function _delete_order_product($ord_pr_id, $ord_id = 0)
	{
		if($this->is_valid_md5($ord_pr_id))
		{
			$this->_init_cart($ord_id);
			$this->cart->update(array('rowid' => $ord_pr_id, 'qty' => 0));
			return TRUE;
		}
		else if($ord_id && ($ord_pr_id = intval($ord_pr_id))>0)
		{
			if(!$this->isset_order($ord_id)) return FALSE;
			$deleted_products = $this->get_deleted_order_products($ord_id);
			$product = array($ord_pr_id => $ord_pr_id);
			$this->session->set_userdata('order_delete_products_'.$ord_id, $deleted_products + $product);
			return TRUE;
		}
		return FALSE;
	}

	protected function calculate_temp_order_product_qty($pr_id, $ord_id = 0)
	{
		$cart_pr_total_qty = 0;
		if($ord_id)
		{
			$this->db->select("A.`".self::ID_ORD_PR."` AS ORD_PR_ID, A.`".self::ID_PR."` AS PR_ID, A.`".self::ID_ORD_PR."` AS `ORD_PR_ID`, A.`sku`, A.`qty`, A.`real_qty`")
				  ->from("`".self::ORD_PR."` AS A")
				  ->where("A.`".self::ID_ORD."`", $ord_id)->where("A.`".self::ID_PR."`", $pr_id);
			$ord_pr = $this->db->get()->result_array();

			$delete_prod_array = $this->get_deleted_order_products($ord_id);
			$edit_prod_array = $this->get_edited_order_products($ord_id);

			foreach($ord_pr as $ms)
			{
				if(isset($edit_prod_array[$ms['ORD_PR_ID']]))
				{
					$ms['qty'] = $edit_prod_array[$ms['ORD_PR_ID']]['qty'];
					$ms['real_qty'] = $edit_prod_array[$ms['ORD_PR_ID']]['real_qty'];
				}
				if(isset($delete_prod_array[$ms['ORD_PR_ID']])) $ms['qty'] = 0;

				$cart_pr_total_qty += $ms['qty'] * $ms['real_qty'];
			}
		}

		$cart_pr = $this->get_added_order_products($ord_id);
		foreach($cart_pr as $ms)
		{
			if($ms['id'] == $pr_id) $cart_pr_total_qty += $ms['qty'] * $ms['options']['real_qty'];
		}
		return $cart_pr_total_qty;
	}

	public function unset_order_products_temp($ord_id = 0)
	{
		$this->_unset_order_products_temp($ord_id);
		return TRUE;
	}

	protected function _unset_order_products_temp($ord_id)
	{
		$this->session->unset_userdata('order_edit_products_'.$ord_id);
		$this->session->unset_userdata('order_delete_products_'.$ord_id);
		$this->_init_cart($ord_id);
		$this->cart->destroy();
	}

	public function get_order_product_qty($ord_id, $ord_pr_id)
	{
		if($this->is_valid_md5($ord_pr_id))
		{
			$cart_products = $this->get_added_order_products($ord_id);
			if(isset($cart_products[$ord_pr_id]))
			{
				return array('qty' => $cart_products[$ord_pr_id]['qty'], 'ord_id' => $ord_id, 'ord_pr_id' => $ord_pr_id);
			}
			return FALSE;
		}
		$this->db->select("`qty`, `real_qty`")
			->from("`".self::ORD_PR."` AS A")
			->where("A.`".self::ID_ORD_PR."`", $ord_pr_id)->limit(1);
		$result = $this->db->get()->row_array();
		if(count($result)>0)
		{
			return array('qty' => $result['qty'], 'real_qty' => $result['real_qty'], 'ord_id' => $ord_id, 'ord_pr_id' => $ord_pr_id);
		}
		return FALSE;
	}
//---ORDER PRODUCTS ACTIONS---

//ORDER CURRENCY ACTIONS
	public function set_order_currency($currency_id, $ord_id)
	{
		$this->load->model('catalogue/mcurrency');
		if($currency = $this->mcurrency->get_users_currency_by_cid($currency_id))
		{
			$this->_set_order_currency_temp($currency, $ord_id);
			return $currency;
		}
		return FALSE;
	}

	public function set_order_currency_rate($currency_rate, $ord_id)
	{
		$currency_rate = floatval($currency_rate);
		$currency = $this->_get_order_currency_temp($ord_id);
		$base_currency = $this->_get_order_base_currency_temp($ord_id);

		if($currency_rate <= 0) return $currency;

		if($currency['id_m_c_currency'] != $base_currency['id_m_c_currency'])
		{
			$currency['rate'] = $currency_rate;
			$this->_set_order_currency_temp($currency, $ord_id);
			return $currency;
		}
		return $currency;
	}

	protected function _set_order_currency_temp($currency, $ord_id)
	{
		$this->session->set_userdata('order_currency_'.$ord_id, $currency);
	}

	protected function _get_order_currency_temp($ord_id)
	{
		return $this->session->userdata('order_currency_'.$ord_id);
	}

	protected function _set_order_base_currency_temp($currency, $ord_id)
	{
		$this->session->set_userdata('order_base_currency_'.$ord_id, $currency);
	}

	protected function _get_order_base_currency_temp($ord_id)
	{
		return $this->session->userdata('order_base_currency_'.$ord_id);
	}

	protected function _unset_order_currency_temp($ord_id)
	{
		$this->session->unset_userdata('order_currency_'.$ord_id);
		$this->session->unset_userdata('order_base_currency_'.$ord_id);
	}
//---ORDER CURRENCY ACTIONS---

//ORDER DISCOUNT
	public function set_order_discount($sum, $ord_id)
	{
		$sum = round(floatval($sum), 2);
		$sum_array = $this->get_temp_order_products_sum($ord_id);
		if($sum < $sum_array['subtotal_rate'])
		{
			$this->_set_order_discount_temp($sum, $ord_id);
			return TRUE;
		}
		else
		{
			$this->_set_order_discount_temp(0, $ord_id);
		}
		return FALSE;
	}

	protected function _set_order_discount_temp($sum, $ord_id)
	{
		$this->session->set_userdata('order_discount_'.$ord_id, $sum);
	}

	protected function _get_order_discount_temp($ord_id)
	{
		return $this->session->userdata('order_discount_'.$ord_id);
	}

	protected function _unset_order_discount_temp($ord_id)
	{
		$this->session->unset_userdata('order_discount_'.$ord_id);
	}
//---ORDER DISCOUNT---
	public function unset_order_temp($ord_id = 0)
	{
		$this->_unset_order_products_temp($ord_id);
		$this->_unset_order_customer_temp($ord_id);
		$this->_unset_order_currency_temp($ord_id);
		$this->_unset_order_discount_temp($ord_id);
		return TRUE;
	}

//ORDER SAVE
	protected function save_validation($ord_id = FALSE)
	{
		if($ord_id)
		{
			if(!$this->isset_order($ord_id)) return FALSE;
			$this->ord_id = $ord_id;
		}
		return FALSE;
	}

	public function save($ord_id = FALSE)
	{
		if($ord_id)
		{
			if(($ord_id = intval($ord_id)) <= 0) return FALSE;

			$this->db->select("*")
				->from("`".self::ORD."`")
				->where("`".self::ID_ORD."`", $ord_id)->limit(1);
			$order = $this->db->get()->row_array();
			if(count($order) == 0) return FALSE;

			$ORDER_POST = $this->input->post('order');
			$ORDER_POST[self::ID_LANGS] = $order[self::ID_LANGS];

			list($added_products, $added_products_attributes) = $this->_prepare_save_order_added_products($ord_id, $ORDER_POST);

			$edited_products = $this->_prepare_save_order_edited_products($ord_id);
			$deleted_products = $this->_prepare_save_order_deleted_products($ord_id);

			$this->db->trans_start();


			$order_total = $this->get_temp_order_products_sum($ord_id);
			if(count($deleted_products) > 0)
			{
				$dpr_array = array();
				foreach($deleted_products as $ms)
				{
					$dpr_array[] = $ms;
				}
				$this->db->where("`".self::ID_ORD."`", $ord_id)->where_in("`".self::ID_ORD_PR."`", $dpr_array)->delete("`".self::ORD_PR."`");
			}
			if(count($edited_products) > 0)
			{
				foreach($edited_products as $key => $ms)
				{
					$this->sql_add_data(array('qty' => $ms['qty'], 'real_qty' => $ms['real_qty'],  'subtotal' => $ms['subtotal'], 'total' => $ms['total']))->sql_save(self::ORD_PR, $key);
				}
			}
			if(count($added_products) > 0)
			{
				foreach($added_products as $key => $ms)
				{
					$product_data = $ms + array(self::ID_ORD => $ord_id);
					$order_product_id = $this->sql_add_data($product_data)->sql_save(self::ORD_PR);
					foreach($added_products_attributes[$key] as $attr)
					{
						$product_attr_data = $attr + array(self::ID_ORD_PR => $order_product_id, self::ID_ORD => $ord_id);
						$this->sql_add_data($product_attr_data)->sql_save(self::ORD_PR_ATTR);
					}
				}
			}

			if(isset($ORDER_POST[self::ID_UPM])) $order_data[self::ID_UPM] = intval($ORDER_POST[self::ID_UPM]);
			if(isset($ORDER_POST[self::ID_USM])) $order_data[self::ID_USM] = intval($ORDER_POST[self::ID_USM]);
			//PAYMENT N SHIPPING METHODS
			if(isset($order_data[self::ID_UPM]) && $order_data[self::ID_UPM] > 0)
			{
				$this->load->model('sales/mpayment_methods');
				if($pdata = $this->mpayment_methods->get_users_payment_method_base_data($ORDER_POST[self::ID_UPM]))
				{
					$order_data['payment_method_alias'] = $pdata['upm_alias'];
				}
				else
				{
					unset($order_data[self::ID_UPM]);
				}
			}

			if(isset($order_data[self::ID_USM]) && $order_data[self::ID_USM] > 0)
			{
				$this->load->model('sales/mshipping_methods');
				if($sdata = $this->mshipping_methods->get_users_shipping_method_base_data($ORDER_POST[self::ID_USM]))
				{
					$order_data['shipping_method_alias'] = $sdata['usm_alias'];
				}
				else
				{
					unset($order_data[self::ID_USM]);
				}
			}

			//---PAYMENT N SHIPPING METHODS---

			if(($order_customer = $this->_get_order_customer_temp($ord_id)))
			{
				$order_data[self::ID_CT] = $order_customer['ID'];
			}

			$order_data['admin_note'] = $ORDER_POST['admin_note'];
			$order_data['total_qty'] = $order_total['total_qty'];
			$order_data['subtotal'] = $order_total['subtotal'];
			$order_data['total'] = $order_total['total'];

			$selected_currency = $this->_get_order_currency_temp($ord_id);

			$this->load->model('catalogue/mcurrency');
			$order_data['id_m_c_currency'] = $selected_currency[Mcurrency::ID_CUR];
			$order_data['currency_name'] = $selected_currency['name'];
			$order_data['currency_rate'] = $selected_currency['rate'];

			$order_data['discount'] = $this->_get_order_discount_temp($ord_id);

			$this->sql_add_data($order_data)->sql_using_user()->sql_update_date()->sql_save(self::ORD, $ord_id);
			if($addresses = $this->input->post('addresses'))
			{
				foreach($addresses as $key => $ms)
				{
					$adata = $ms;
					$this->sql_add_data($adata)->sql_save(self::ORD_ADDR, array(self::ID_ORD => $ord_id, 'type' => $key));
				}
			}

			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				$this->unset_order_temp($ord_id);
				return $ord_id;
			}
			return FALSE;
		}
		else
		{
			$order_data = array();
			$ORDER_POST = $this->input->post('order');

			list($added_products, $added_products_attributes, $order_total) = $this->_prepare_save_order_added_products(0, $ORDER_POST);
			if(count($added_products) == 0) return FALSE;

			$order_data['status'] = 1;
			if(isset($ORDER_POST[self::ID_UPM])) $order_data[self::ID_UPM] = intval($ORDER_POST[self::ID_UPM]);
			if(isset($ORDER_POST[self::ID_UPM])) $order_data[self::ID_USM] = intval($ORDER_POST[self::ID_USM]);

			//PAYMENT N SHIPPING METHODS
			if(isset($order_data[self::ID_UPM]) && $order_data[self::ID_UPM] > 0)
			{
				$this->load->model('sales/mpayment_methods');
				if($pdata = $this->mpayment_methods->get_users_payment_method_base_data($ORDER_POST[self::ID_UPM]))
				{
					$order_data['payment_method_alias'] = $pdata['upm_alias'];
				}
				else
				{
					unset($order_data[self::ID_UPM]);
				}
			}

			if(isset($order_data[self::ID_USM]) && $order_data[self::ID_USM] > 0)
			{
				$this->load->model('sales/mshipping_methods');
				if($sdata = $this->mshipping_methods->get_users_shipping_method_base_data($ORDER_POST[self::ID_USM]))
				{
					$order_data['shipping_method_alias'] = $sdata['usm_alias'];
				}
				else
				{
					unset($order_data[self::ID_USM]);
				}
			}

			//---PAYMENT N SHIPPING METHODS---

			if($order_customer = $this->_get_order_customer_temp(0))
			{
				$order_data[self::ID_CT] = $order_customer['ID'];
			}
			$order_data['admin_note'] = $ORDER_POST['admin_note'];
			$order_data['discount'] = floatval($ORDER_POST['discount']);
			$order_data['total_qty'] = $order_total['total_qty'];
			$order_data['subtotal'] = $order_total['subtotal'];
			$order_data['total'] = $order_total['total'];

			$s_currency = $this->_get_order_currency_temp(0);
			$this->load->model('catalogue/mcurrency');
			$base_currency = $this->mcurrency->get_users_base_currency();
			$order_data['base_id_m_c_currency'] = $base_currency[Mcurrency::ID_CUR];
			$order_data['base_currency_name'] = $base_currency['name'];
			$selected_currency = $this->mcurrency->get_users_currency_by_cid($ORDER_POST[Mcurrency::ID_CUR]);
			$order_data['id_m_c_currency'] = $selected_currency[Mcurrency::ID_CUR];
			$order_data['currency_name'] = $selected_currency['name'];
			$order_data['currency_rate'] = $s_currency['rate'];
			$order_data['id_langs'] = $ORDER_POST['id_langs'];


			$this->db->select("MAX(orders_number) AS orders_number")
				 ->from("`".self::ORD."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
			$orders_number = $this->db->get()->row_array();
			$max = 1;
			if(count($orders_number)>0)
			{
				$max = intval($orders_number['orders_number']) + 1;
			}
			$max = str_repeat("0", 8-strlen($max)).($max);
			$order_data['orders_number'] = $max;

			$this->db->trans_start();
			$order_id = $this->sql_add_data($order_data)->sql_using_user()->sql_update_date()->sql_save(self::ORD);

			$ORDER_ADDRESSES = array();
			if($ORDER_ADDRESSES = $this->input->post('addresses'))
			{
				foreach($ORDER_ADDRESSES as $key => $ms)
				{
					$address_data = $ms + array(self::ID_ORD => $order_id, 'type' => $key);
					$this->sql_add_data($address_data)->sql_save(self::ORD_ADDR);
				}
			}

			foreach($added_products as $key => $ms)
			{
				$product_data = $ms + array(self::ID_ORD => $order_id);
				$order_product_id = $this->sql_add_data($product_data)->sql_save(self::ORD_PR);
				foreach($added_products_attributes[$key] as $attr)
				{
					$product_attr_data = $attr + array(self::ID_ORD_PR => $order_product_id, self::ID_ORD => $order_id);
					$this->sql_add_data($product_attr_data)->sql_save(self::ORD_PR_ATTR);
				}
			}

			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				$this->unset_order_temp(0);
				return $order_id;
			}
			return FALSE;
		}
	}

	protected function _prepare_save_order_added_products($ord_id, $order_post)
	{
		$added_order_products = $this->get_added_order_products($ord_id);
		$order_total = array('subtotal' => 0, 'total' => 0, 'total_qty' => 0);
		$added_products = array();
		$added_products_attributes = array();
		foreach($added_order_products as $key => $ms)
		{
			$this->db->select("A.`".self::ID_PR."`, A.`sku`, B.`name`")
					 ->from("`".self::PR."` AS A")
					 ->join("`".self::PR_DESC."` AS B",
						 "B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$order_post['id_langs']."'",
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
						'price' => $price_array['price'],
						'qty' => $ms['qty'],
						'real_qty' => $price_array['real_qty'],
						'subtotal' => $ms['qty'] * $price_array['price'],
						'total' => $ms['qty'] * $price_array['price'],
						'price_alias' => $price_array['price_alias'],
						'price_name' => $price_array['price_name']
					);
					$order_total['subtotal'] += $price_array['price'] * $ms['qty'];
					$order_total['total'] += $price_array['price'] * $ms['qty'];
					$order_total['total_qty'] += $ms['qty'] * $ms['options']['real_qty'];

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
		return array($added_products, $added_products_attributes, $order_total);
	}

	protected function _prepare_save_order_edited_products($ord_id)
	{
		$edited_order_products = $this->get_edited_order_products($ord_id);
		return $edited_order_products;
	}

	protected function _prepare_save_order_deleted_products($ord_id)
	{
		$deleted_order_products = $this->get_deleted_order_products($ord_id);
		return $deleted_order_products;
	}
//---ORDER SAVE---

//CHECK POSSIBILITY
	public function check_possibility_creating_invoice($ord_id)
	{
		$this->unset_order_temp($ord_id);
		$order = $this->get_order($ord_id);
		if($order['orders_state'] != 'N') return FALSE;
		$this->load->model('warehouse/mwarehouses');
		$wh_id = $this->mwarehouses->get_shop_wh();
		if(!$wh_id) return TRUE;
		$check_possibility_creating = TRUE;
		$check_product_array = $this->prepare_order_view_products_data($ord_id);
		foreach($check_product_array as $ms)
		{
			if($ms['wh_qty'] < 0)
			{
				$this->messages->add_error_message("Количества позиции <b>".$ms['sku']."</b> недостаточно, недостающее к-во ".($ms['wh_qty']*-1)." !");
				$check_possibility_creating = FALSE;
			}
		}
		return $check_possibility_creating;
	}
	
	public function check_possibility_cancel_invoice($ord_id)
	{
		$order = $this->get_order($ord_id);
		if($order['orders_state'] != 'IC' || $order['orders_state'] != 'COD_S' || $order['orders_state'] != 'COD_S_С' || $order['orders_state'] != 'CM') return TRUE;
		return FALSE;
	}
	
	public function check_possibility_creating_credit_memo($ord_id)
	{
		if(!$order = $this->get_order($ord_id)) return FALSE;
		if($order['orders_state'] == 'IC' || $order['orders_state'] == 'S' || $order['orders_state'] == 'C' || $order['orders_state'] == 'COD' || $order['orders_state'] == 'COD_S' || $order['orders_state'] == 'COD_S_С')
		{
			return TRUE;
		}
		return FALSE;
	}
//---CHECK POSSIBILITY---
	
	public function cancel_order($ord_id)
	{
		if(!$order = $this->get_order($ord_id)) return FALSE;
		$this->load->model('sales/minvoices');
		$invoice = $this->minvoices->get_order_invoice($ord_id);
		if($invoice)
		{
			if($invoice['invoices_status'] == 'N' || $invoice['invoices_status'] == 'P')
			{
				$this->db->trans_start();
				$this->sql_add_data(array('invoices_status' => 'CN'))->sql_update_date()->sql_save(self::INV, $invoice[self::ID_INV]);
				$this->sql_add_data(array('orders_state' => 'CN'))->sql_update_date()->sql_save(self::ORD, $order[self::ID_ORD]);
				$this->db->trans_complete();
				
				if($this->db->trans_status())
				{
					return TRUE;
				}
				return FALSE;
			}
			return FALSE;
		}
		else
		{
			$this->db->trans_start();
			$this->sql_add_data(array('orders_state' => 'CN'))->sql_update_date()->sql_save(self::ORD, $order[self::ID_ORD]);
			$this->db->trans_complete();
			
			if($this->db->trans_status())
			{
				return TRUE;
			}
			return FALSE;
		}
	}

	public function complete_order($ord_id, $COD_S = FALSE)
	{
		if(!$order = $this->get_order($ord_id)) return FALSE;
		$this->load->model('sales/minvoices');
		if(!$invoice = $this->minvoices->get_order_invoice($ord_id)) return FALSE;
		if($invoice['invoices_status'] == 'C' || $invoice['invoices_status'] == 'COD')
		{

			$this->load->model('warehouse/mwarehouses_sales');
			if($COD_S)
			{
				$this->sql_add_data(array('invoices_status' => 'COD'))->sql_update_date()->sql_save(self::INV, $invoice[self::ID_INV]);
				$this->sql_add_data(array('orders_state' => 'COD_S_С'))->sql_update_date()->sql_save(self::ORD, $ord_id);
				$this->mwarehouses_sales->change_order_sale_state($ord_id, 'COD_S_С');
				$this->mwarehouses_sales->change_order_sale_invoice_state($ord_id, 'COD');
			}
			else
			{
				$this->sql_add_data(array('invoices_status' => 'C'))->sql_update_date()->sql_save(self::INV, $invoice[self::ID_INV]);
				$this->sql_add_data(array('orders_state' => 'C'))->sql_update_date()->sql_save(self::ORD, $ord_id);
				$this->mwarehouses_sales->change_order_sale_invoice_state($ord_id, 'C');
				$this->mwarehouses_sales->change_order_sale_state($ord_id, 'C');
			}
			return $ord_id;
		}
		return FALSE;
	}
	
	public function order_COD_paid($ord_id)
	{
		$this->db->trans_start();
		if($this->complete_order($ord_id))
		{
			$this->db->trans_complete();
			if($this->db->trans_status())
			{
				return $ord_id;
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function get_order_products_with_photo($id_ord)
	{
		$order_products_array = array();
		$this->db->select("ORD.`orders_number`, ORD.`".self::ID_USERS."`, OR_PR.`".self::ID_ORD_PR."`, OR_PR.`".self::ID_PR."` AS PR_ID, OR_PR.`name`, OR_PR.`sku`, OR_PR.`qty`, CONCAT(OR_PR.`price_name`, ' ', ROUND(OR_PR.`price` * ORD.`currency_rate`, 2), ' ', ORD.`currency_name`) AS price, CONCAT(ROUND(OR_PR.`total` * ORD.`currency_rate`, 2), ' ', ORD.`currency_name`) AS total, GROUP_CONCAT(CONCAT(OR_PR_AT.`attributes_name`, ' : ', OR_PR_AT.`attributes_options_name`) SEPARATOR '<BR>') AS attributes")
			->from("`".self::ORD_PR."` AS OR_PR")
			->join("`".self::ORD."` AS ORD",
					"ORD.`".self::ID_ORD."` = OR_PR.`".self::ID_ORD."`",
					"LEFT")
			->join("`".self::ORD_PR_ATTR."` AS OR_PR_AT",
					"OR_PR_AT.`".self::ID_ORD_PR."` = OR_PR.`".self::ID_ORD_PR."`",
					"LEFT")
			->where("OR_PR.`".self::ID_ORD."`", $id_ord)->group_by("OR_PR.`".self::ID_ORD_PR."`");
		$products_array = $this->db->get()->result_array();
		$pr_id = array();
		foreach($products_array as $ms)
		{
			$pr_id[$ms['PR_ID']] = $ms['PR_ID'];
		}
		$this->load->model('catalogue/mproducts_view');
		$products = $this->mproducts_view->get_products_by_id($pr_id);
		foreach($products_array as $ms)
		{
			if(isset($products[$ms['PR_ID']]))
			{
				$order_products_array[] = $ms + array('timage' => $products[$ms['PR_ID']]['timage']);
			}
			else
			{
				$order_products_array[] = $ms;
			}
		}
		return $order_products_array;
	}

	public function get_print_order_data($ord_id)
	{
		if(!$order = $this->get_order($ord_id)) return FALSE;

		$this->template->add_title(' | '.$order['orders_number']);
		$this->template->add_navigation($order['orders_number']);

		$order['orders_state'] = $this->get_order_state_name($order['orders_state']);
		$order['payment_method_name'] = '';
		$order['shipping_method_name'] = '';

		$products = array();
		$products_temp = $this->get_order_products($ord_id);
		foreach($products_temp as $ms)
		{
			if(trim($ms['price_alias']) != '')
			{
				$ms['price_alias'] = '('.$ms['price_alias'].')';
			}
			$ms['qty_str'] = $ms['qty'];
			if($ms['real_qty'] != 1)
			{
				$ms['qty_str'] = $ms['qty'].'('.$ms['qty'] * $ms['real_qty'].')';
			}
			$products[$ms[self::ID_ORD_PR]] = $ms;
			$products[$ms[self::ID_ORD_PR]]['attributes'] = $ms['attributes'];
		}
		$addresses = $this->get_order_addresses($ord_id);

		if($order[self::ID_UPM] != NULL)
		{
			$this->load->model('sales/mpayment_methods');
			$pdata = $this->mpayment_methods->get_users_payment_method_data($order[self::ID_UPM]);
			$order['payment_method_name'] = $pdata['name'];
		}

		if($order[self::ID_USM] != NULL)
		{
			$this->load->model('sales/mshipping_methods');
			$sdata = $this->mshipping_methods->get_users_shipping_method_data($order[self::ID_USM]);
			$order['shipping_method_name'] = $sdata['name'];
		}
		return array('order' => $order, 'products' => $products, 'addresses' => $addresses);
	}
	
	public function isset_order($ord_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
			->from("`".self::ORD."`")
			->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_ORD."`", $ord_id);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] > 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function is_valid_md5($md5 = '')
	{
		return preg_match('/^[a-f0-9]{32}$/', $md5);
	}
}
?>