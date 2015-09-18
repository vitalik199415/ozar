<?php
class Mwarehouses_transfers extends AG_Model
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
	const WH_TR			= 'wh_log_transfer';
	const ID_WH_TR		= 'id_wh_log_transfer';
	const WH_TR_PR		= 'wh_log_transfer_products';

	const create_sale_session = 'wh_create_sale_session';

	public function __construct()
	{
		parent::__construct();
	}

	public function render_wh_transfers_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("wh_log_transfers_grid", array('sort' => 'A.'.self::ID_WH_TR, 'desc' => 'DESC'));

		$this->grid->db
			->select("A.`".self::ID_WH_TR."` AS ID, A.`wh_transfer_number`, A.`total_qty`, A.`create_date`, A.`admin_note`, WH_FROM.`alias` AS wh_from_alias, WH_TO.`alias` AS wh_to_alias")
			->from("`".self::WH_TR."` AS A")
			->join(
			"`".self::WH_LOGS."` AS B",
				"B.`".self::ID_WH_LOGS."` = A.`".self::ID_WH_LOGS."`",
				"INNER")
			->join(
			"`".self::WH."` AS WH_FROM",
				"WH_FROM.`".self::ID_WH."` = A.`".self::ID_WH."_from`",
				"LEFT")
			->join(
			"`".self::WH."` AS WH_TO",
				"WH_TO.`".self::ID_WH."` = A.`".self::ID_WH."_to`",
				"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users);

		$this->load->helper("warehouses_transfers");
		helper_wh_transfers_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->render_grid();
	}

	public function render_not_edited_transfer_products_grid($tr_id = 0)
	{
		$this->db->select("A.`".self::ID_PR."` AS PR_ID, A.`sku`, A.`name`, A.`qty`")
				 ->from("`".self::WH_TR_PR."` AS A")
				 ->where("A.`".self::ID_WH_TR."`", $tr_id);

		$product_array = $this->db->get()->result_array();

		$i = 1;
		foreach($product_array as $key => $ms)
		{
			$product_array[$key]['number'] = $i;
			$i++;
		}
		$this->load->library("nosql_grid");
		$this->nosql_grid->_init_grid("wh_transfer_products_grid");
		$this->nosql_grid->init_fixed_buttons(FALSE);
		$this->load->helper('warehouses_transfers');
		helper_not_edited_wh_transfer_products_grid_build($this->nosql_grid);

		$this->nosql_grid->set_grid_data($product_array);
		return $this->nosql_grid->render_grid(TRUE);
	}

	public function render_transfer_products_grid($wh_id_from = 0, $ajax = FALSE)
	{
		$this->load->library("nosql_grid");
		$this->nosql_grid->_init_grid("wh_transfer_products_grid");
		$this->nosql_grid->init_fixed_buttons(FALSE);
		$this->nosql_grid->ajax_output = $ajax;

		$product_array = $this->prepare_transfer_view_products_data($wh_id_from);

		$this->load->helper('warehouses_transfers');
		helper_wh_transfer_products_grid_build($this->nosql_grid, $wh_id_from);

		$this->nosql_grid->set_grid_data($product_array);
		return $this->nosql_grid->render_grid(TRUE);
	}

	public function render_wh_shop_products_grid($wh_id_from)
	{
		$this->load->model('catalogue/mproducts');
		$this->mproducts->prepare_products_grid_query();
		$this->grid->db->join(
					   "`".self::WH_PR."` AS WH_PR",
						   "WH_PR.`".self::ID_WH."` = '".$wh_id_from."' && WH_PR.`".self::ID_PR."` = A.`".self::ID_PR."`",
						   "INNER")
					   ->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, WH_PR.`qty`, A.`status`, A.`create_date`");
		$this->grid->keep_filter_data(TRUE);
		$this->grid->_init_grid('wh_'.$wh_id_from.'_transfer_add_view', array('sort' => 'A.'.self::ID_PR, 'desc' => 'DESC', 'url' => set_url('warehouse/warehouses_transfers/ajax_get_wh_shop_products_grid/wh_id_from/'.$wh_id_from)));

		$this->load->helper('warehouses_transfers');
		helper_transfers_wh_shop_products_grid_build($this->grid, $wh_id_from);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		return $this->grid->render_grid(TRUE);
	}

	public function prepare_transfer_view_products_data($wh_id, $tr_id = false)
	{
		$product_array = array();

		if($tr_id)
		{
			$this->db->select("B.*")
				->from("`".self::WH_TR."` AS A")
				->join("`".self::WH_TR_PR."` AS B",
				"B.`".self::ID_WH_TR."` = A.`".self::ID_WH_TR."`",
				"LEFT")
				->where("A.`".self::ID_WH_TR."`", $tr_id);
			$product_array = $this->db->get()->result_array();
		}

		if(!$tr_id)
		{
			if(count($cart_array = $this->get_added_transfer_products($wh_id))>0)
			{
				$cart_products_id = array();
				foreach($cart_array as $ms)
				{
					$cart_products_id[$ms['id']] = $ms['id'];
				}
				$this->load->model('catalogue/mproducts');
				$cart_products_temp_array = $this->mproducts->get_product($cart_products_id);
				$cart_products_array = array();
				foreach($cart_products_temp_array as $ms)
				{
					$cart_products_array[$ms['PR_ID']] = $ms;
				}
				unset($cart_products_temp_array);
				foreach($cart_array as $key => $ms)
				{
					if(isset($cart_products_array[$ms['id']]))
					{
						$product_array[$key] = array('PR_ID' => $cart_products_array[$ms['id']]['PR_ID'], 'TR_PR_ID' => $key, 'sku' => $cart_products_array[$ms['id']]['sku'], 'name' => $cart_products_array[$ms['id']]['name'], 'qty' => $ms['qty']);
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
						$product_array[$key]['wh_qty'] = $wh_qty_array[$ms['PR_ID']]['qty'] - $pr_qty_array[$ms['PR_ID']] - $ms['qty'];
						$product_array[$key]['wh_qty_str'] = $product_array[$key]['wh_qty'];
						$pr_qty_array[$ms['PR_ID']] += $ms['qty'];
					}
					else
					{
						$product_array[$key]['wh_qty'] = $wh_qty_array[$ms['PR_ID']]['qty'] - $ms['qty'];
						$product_array[$key]['wh_qty_str'] = $product_array[$key]['wh_qty'];
						$pr_qty_array[$ms['PR_ID']] = $ms['qty'];
					}
					if($product_array[$key]['wh_qty'] < 0) $product_array[$key]['wh_qty_str'] = '<span class=\'error\'>'.$product_array[$key]['wh_qty'].'</span>';
				}
			}
		}
		else
		{
			$i = 1;
			foreach($product_array as $key => $ms)
			{
				$product_array[$key]['number'] = $i;
			}
		}
		return $product_array;
	}

	public function prepare_add_transfer()
	{
		$data = array();
		$this->load->model('warehouse/mwarehouses');
		$wh = $this->mwarehouses->get_wh_to_select();
		if(count($wh) > 0)
		{
			$data['wh_collection'] = $wh;
			$this->load->helper('warehouses_transfers');
			helper_wh_transfer_prepare_add($data);
			return TRUE;
		}
		return FALSE;
	}

	public function add_transfer($wh_id_from)
	{
		$this->unset_transfer_temp($wh_id_from);
		$tranfers_data = array();
		$tranfers_data['transfer_products_grid'] = $this->render_transfer_products_grid($wh_id_from);
		return $tranfers_data;
	}

	public function view_transfer($tr_id)
	{
		if($transfer_data = $this->get_transfer_data($tr_id))
		{
			$transfer_data['transfer_products_grid'] = $this->render_not_edited_transfer_products_grid($tr_id);
			$this->load->model("warehouse/mwarehouses");
			$tr_wh_from = $this->mwarehouses->get_wh($transfer_data['transfer']['id_wh_from']);
			$tr_wh_to = $this->mwarehouses->get_wh($transfer_data['transfer']['id_wh_to']);
			$transfer_data['transfer']['warehouse_from_alias'] = $tr_wh_from['alias'];
			$transfer_data['transfer']['warehouse_to_alias'] = $tr_wh_to['alias'];
			return $transfer_data;
		}
		return FALSE;
	}

	public function get_transfer_data($tr_id)
	{
		$transfer = array();
		$transfer['transfer'] = $this->get_transfer($tr_id);
		return $transfer;
	}

	public function get_transfer($tr_id)
	{
		$this->db->select("A.*")
				 ->from("`".self::WH_TR."` AS A")
				 ->where("A.`".self::ID_WH_TR."`", $tr_id)->where("A.`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($transfer = $this->db->get()->row_array()) > 0)
		{
			return $transfer;
		}
		return FALSE;
	}

	public function get_transfer_products($tr_id)
	{
		$products = array();
		$this->db->select("*")
				 ->from("`".self::WH_TR_PR."` AS A")
				 ->where("`".self::ID_WH_TR."`", $tr_id);
		$result = $this->db->get()->result_array();
		if(count($result)>0)
		{
			foreach($result as $ms)
			{
				$products[] = $ms;
			}
		}
		return $products;
	}

	public function get_view_product_data($wh_id_from, $pr_id)
	{
		$this->load->model('catalogue/mproducts_view');
		if($product_array = $this->mproducts_view->get_product($pr_id))
		{
			return $product_array;
		}
		return FALSE;
	}

	//SALE PRODUCTS ACTIONS
	public function add_product_to_transfer($wh_id_from)
	{
		$this->load->model('warehouse/mwarehouses');
		$this->_init_cart($wh_id_from);
		$POST = $this->input->post();
		if(isset($POST['qty']) && intval($POST['qty']) > 0)
		$data['id'] = $POST['product_id'];
		$data['qty'] = intval($POST['qty']);
		$data['name'] = $POST['product_id'];
		$data['price'] = 1;

		$isset_catr_item_qty = 0;
		if($isset_cart_product = $this->cart->isset_cart_item($data))
		{
			$isset_catr_item_qty = $isset_cart_product['qty'];
		}

		$wh_pr_total_qty = $this->mwarehouses->get_wh_product_total_qty($wh_id_from, $POST['product_id']);
		if($data['qty'] > $wh_pr_total_qty)
		{
			$message = "";
			if($isset_catr_item_qty > 0)
			{
				$message .= "Внимание! Позиция с указанной комплектацией в количестве ".$isset_catr_item_qty." уже была добавлена.<BR>";
			}

			$available_qty = $wh_pr_total_qty;
			$message .= "Количества на складе не достаточно. Доступное количество ".$available_qty;

			return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message);
		}

		$this->_add_product_to_transfer($wh_id_from, $data);
		if($isset_cart_product)
		{
			$message = "Позиция с указанной комплектацией уже была добавлена. Количество изменено с ".$isset_catr_item_qty." на ".$data['qty'].".";
		}
		else
		{
			$message = "Позиция в количестве ".$data['qty']." успешно добавлена.";
		}
		return array('success' => TRUE, 'message' => $message);
	}

	public function edit_transfer_product_qty($wh_id_from, $tr_pr_id)
	{
		$this->load->model('warehouse/mwarehouses');

		if(($qty = intval($this->input->post('edit_pr_qty')))>0)
		{
			if(!$this->is_valid_md5($tr_pr_id)) return array('success' => FALSE, 'message' => 'Server Error. Try later!');

			$cart = $this->get_added_transfer_products($wh_id_from);
			if(!isset($cart[$tr_pr_id])) return array('success' => FALSE, 'message' => 'Server Error. Try later!');
			$cart_pr_array = $cart[$tr_pr_id];

			$wh_pr_total_qty = $this->mwarehouses->get_wh_product_total_qty($wh_id_from, $cart_pr_array['id']);
			$cart_pr_total_qty = $cart_pr_array['qty'];

			if($qty > $wh_pr_total_qty)
			{
				$available_qty = $wh_pr_total_qty;
				$message = "Количества позиции на складе не достаточно. Доступное количество ".$available_qty;

				return array('success' => FALSE, 'available_qty' => $available_qty, 'message' => $message);
			}

			$this->_edit_transfer_product($wh_id_from, $tr_pr_id, $qty);
			$message = "Количество позиции успешно отредактировано с ".$cart_pr_array['qty']." на ".$qty.".";
			return array('success' => TRUE, 'message' => $message);
		}
		return array('success' => FALSE, 'message' => 'Server Error. Try later!');
	}

	public function delete_product_from_transfer($wh_id_from, $tr_pr_id)
	{
		if($this->_delete_transfer_product($wh_id_from, $tr_pr_id))
		{
			$message = "Позиция успешно удалена.";
			return array('success' => TRUE, 'message' => $message);
		}
		return array('success' => FALSE, 'message' => 'Server Error. Try later!');
	}

	protected function get_added_transfer_products($wh_id)
	{
		$this->_init_cart($wh_id);
		return $this->cart->contents();
	}

	private function _init_cart($wh_id)
	{
		$this->load->library('cart');
		$this->cart->set_cart_key_suffix('_wh_'.$wh_id.'_transfer');
	}

	protected function _add_product_to_transfer($wh_id, $data)
	{
		$this->_init_cart($wh_id);
		return $this->cart->insert($data);
	}

	protected function _edit_transfer_product($wh_id, $tr_pr_id, $qty)
	{
		if($qty <= 0) return FALSE;
		if($this->is_valid_md5($tr_pr_id))
		{
			$this->_init_cart($wh_id);
			$this->cart->update(array('rowid' => $tr_pr_id, 'qty' => $qty));
			return TRUE;
		}
	}

	protected function _delete_transfer_product($wh_id, $tr_pr_id)
	{
		if($this->is_valid_md5($tr_pr_id))
		{
			$this->_init_cart($wh_id);
			$this->cart->update(array('rowid' => $tr_pr_id, 'qty' => 0));
			return TRUE;
		}
		return FALSE;
	}

	public function unset_transfer_products_temp($wh_id)
	{
		$this->_unset_transfer_products_temp($wh_id);
		return TRUE;
	}

	protected function _unset_transfer_products_temp($wh_id)
	{
		$this->_init_cart($wh_id);
		$this->cart->destroy();
	}

	public function get_transfer_product_qty($wh_id_from, $tr_pr_id)
	{
		if($this->is_valid_md5($tr_pr_id))
		{
			$cart_products = $this->get_added_transfer_products($wh_id_from);
			if(isset($cart_products[$tr_pr_id]))
			{
				return array('qty' => $cart_products[$tr_pr_id]['qty'], 'wh_id_from' => $wh_id_from, 'tr_pr_id' => $tr_pr_id);
			}
			return FALSE;
		}
		return FALSE;
	}
	//---SALE PRODUCTS ACTIONS---

	public function unset_transfer_temp($wh_id)
	{
		$this->_unset_transfer_products_temp($wh_id);
		return TRUE;
	}

	public function save_transfer()
	{
		$tr_data = array();
		$TR_POST = $this->input->post('transfer');

		$wh_id_from = $TR_POST['wh_id_from'];
		$wh_id_to = $TR_POST['wh_id_to'];

		list($added_products, $transfer_total) = $this->_prepare_save_transfer_added_products($wh_id_from);
		if($added_products === FALSE || count($added_products) == 0) return FALSE;

		$tr_data['id_wh_from'] = $wh_id_from;
		$tr_data['id_wh_to'] = $wh_id_to;
		$tr_data['admin_note'] = $TR_POST['admin_note'];

		$tr_data['total_qty'] = $transfer_total['total_qty'];

		$this->db->select("MAX(wh_transfer_number) AS wh_transfer_number")
				 ->from("`".self::WH_TR."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$sale_number = $this->db->get()->row_array();
		$max = 1;
		if(count($sale_number)>0)
		{
			$max = intval($sale_number['wh_transfer_number']) + 1;
		}
		$max = str_repeat("0", 8-strlen($max)).($max);
		$tr_data['wh_transfer_number'] = $max;

		$this->load->model('warehouse/mwarehouses_logs');

		$this->db->trans_start();
		$log_id = $this->mwarehouses_logs->add_log($wh_id_from, 'TRANSFER', $TR_POST['admin_note']);

		$tr_id = $this->sql_add_data($tr_data + array(self::ID_WH_LOGS => $log_id))->sql_using_user()->sql_update_date()->sql_save(self::WH_TR);

		foreach($added_products as $key => $ms)
		{
			$product_data = $ms + array(self::ID_WH_TR => $tr_id);
			$this->sql_add_data($product_data)->sql_save(self::WH_TR_PR);
		}

		$this->transfer_products($wh_id_from, $wh_id_to, $added_products);

		$this->db->trans_complete();
		if($this->db->trans_status())
		{
			$this->unset_transfer_temp($wh_id_from);
			return $tr_id;
		}
		return FALSE;
	}

	protected function _prepare_save_transfer_added_products($wh_id_from)
	{
		$PR = $this->prepare_transfer_view_products_data($wh_id_from);
		foreach($PR as $ms)
		{
			if($ms['wh_qty'] < 0) return array(FALSE, FALSE);
		}
		$added_products_temp = $this->get_added_transfer_products($wh_id_from);
		$transfer_total = array('total_qty' => 0);
		$added_products = array();
		foreach($added_products_temp as $key => $ms)
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
				$added_products[$key] = array(
					self::ID_PR => $product[self::ID_PR],
					'sku' => $product['sku'],
					'name' => $product['name'],
					'qty' => $ms['qty'],
				);
				$transfer_total['total_qty'] += $ms['qty'];
			}
		}
		return array($added_products, $transfer_total);
	}

	public function transfer_products($wh_id_from, $wh_id_to, $products)
	{
		$this->load->model('warehouse/mwarehouses_products');

		$this->mwarehouses_products->edit_transferred_products($wh_id_from, $products);
		$this->mwarehouses_products->add_transferred_products($wh_id_to, $products);
	}

	public function is_valid_md5($md5 = '')
	{
		return preg_match('/^[a-f0-9]{32}$/', $md5);
	}

	public function isset_transfer($tr_id)
	{
		$this->db->select("COUNT(*) AS COUNT")
				 ->from("`".self::WH_TR."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)->where("`".self::ID_WH_TR."`", $tr_id);
		$result = $this->db->get()->row_array();
		if($result['COUNT'] > 0)
		{
			return TRUE;
		}
		return FALSE;
	}
}
?>