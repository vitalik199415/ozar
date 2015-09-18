<?php
class Mwarehouses_products extends AG_Model
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
	
	public $id_wh = FALSE;
	public $id_wh_shop = FALSE;
	
	const PR_ADDEDIT_FORM_ID = 'products_add_edit_from';
	const ADD_PR_QTY_FORM_ID = 'add_pr_qty_form';
	const CREATE_SALE_FORM_ID = 'create_sale_form';
	const CREATE_TRANFER_FORM_ID = 'create_transfer_from';
	
	const create_sale_session = 'wh_create_sale_session';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_warehouses_all_pr_grid()
	{
		$query = $this->db->select("`".self::ID_WH."`, `alias`")
				->from("`".self::WH."`")
				->where("`".self::ID_USERS."`", $this->id_users)->order_by('sort');
		$result = $query->get()->result_array();
		$wh_array = array();
		foreach($result as $ms)
		{
			$wh_array[$ms[self::ID_WH]] = $ms['alias'];
		}
		
		$this->load->library('grid');
		$this->grid->_init_grid('warehouses_all_products_grid', array(), FALSE);
		
		if($extra_search = $this->grid->get_options('search'))
		{
			if(isset($extra_search['WH_QTY']))
			{
				$temp_extra_search = $extra_search;
				unset($temp_extra_search['WH_QTY']);
				$this->grid->set_options('search', $temp_extra_search);
				$update_select_wh = $extra_search['WH_QTY'];
			}
		}
		
		$qty_query = clone $this->db;
		$qty_query->select("COUNT(DISTINCT(A.".self::ID_PR.")) AS numrows")
				->from("`".self::WH_PR."` AS A")
				->join("`".self::WH."` AS B",
					"B.`".self::ID_USERS."` = '".$this->id_users."' && B.`".self::ID_WH."` = A.`".self::ID_WH."`",
					"INNER");
		
		$this->grid->db
			->select("A.`".self::ID_WH_PR."` AS ID, A.`".self::ID_PR."` AS PR_ID, A.`sku`, C.`name`, GROUP_CONCAT(CONCAT(B.`alias`,' qty: ', A.`qty`) ORDER BY(B.`sort`) SEPARATOR '<br>') AS WH_QTY")
			->from("`".self::WH_PR."` AS A")
			->join("`".self::WH."` AS B",
					"B.`".self::ID_USERS."` = '".$this->id_users."' && B.`".self::ID_WH."` = A.`".self::ID_WH."`",
					"INNER")
			->join("`".self::PR_DESC."` AS C",
					"C.`".self::ID_PR."` = A.`".self::ID_PR."` && C.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"LEFT")
			->group_by("A.`".self::ID_PR."`");
		
		if(isset($update_select_wh))
		{
			$update_select_wh = intval($update_select_wh);
			if($update_select_wh > 0)
			{
				$this->grid->db->where("A.`".self::ID_WH."`", $update_select_wh);
				$qty_query->where("A.`".self::ID_WH."`", $update_select_wh);
				 
			}
		}
		
		
		$this->grid->set_extra_select_qty_object($qty_query);
		unset($qty_query);
		
		$this->load->helper('warehouses/warehouses_products_helper');
		helper_warehouses_all_pr_grid_build($this->grid, $wh_array);
		
		$this->grid->create_grid_data();
		if(isset($update_select_wh))
		{
			$extra_search = $this->grid->get_options('search');
			$extra_search['WH_QTY'] = $update_select_wh;
			$this->grid->set_search_manualy('WH_QTY', $update_select_wh);
			$this->grid->set_options('search', $extra_search);
		}
		$this->grid->render_grid();
	}
	
	public function get_wh_pr_qty($pr_id = 0)
	{
		$array = array();
		$query = $this->db->select("A.`".self::ID_WH."`, A.`alias`, B.qty")
				->from("`".self::WH."` AS A")
				->join("`".self::WH_PR."` AS B",
						"B.`".self::ID_WH."` = A.`".self::ID_WH."` && B.`".self::ID_PR."` = '".$pr_id."'",
						"LEFT")
				->where("A.`i_s_wh`", 1);
		$result = $query->get()->result_array();
		foreach($result as $ms)
		{
			$array[$ms[self::ID_WH]] = $ms;
		}
		return $array;
	}
	
	public function edit_wh_pr_qty($wh_id, $pr_id, $sku, $qty)
	{
		$query = $this->db->select("`".self::ID_WH_PR."`")
				->from("`".self::WH_PR."`")
				->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
		$result = $query->get()->row_array();
		if(count($result)>0)
		{
			$data = array('qty' => $qty);
			$this->sql_add_data($data)->sql_save(self::WH_PR, $result[self::ID_WH_PR]);
		}
		else
		{
			$data = array(self::ID_WH => $wh_id, self::ID_PR => $pr_id, 'sku' => $sku, 'qty' => $qty);
			$this->sql_add_data($data)->sql_save(self::WH_PR);
		}
	}
	
	public function not_in_wh_pr_grid($wh_id)
	{
		$this->load->library('grid');
		$this->grid->_init_grid('not_in_wh_pr_grid', array('url' => set_url('warehouse/warehouses_products/ajax_get_not_exists_pr/wh_id/'.$wh_id)), TRUE);
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, A.`status`, B.`name`")
			->from("`".self::PR."` AS A")
			->join("`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = '".$this->id_langs."'",
					"LEFT")
			->where("A.`".self::ID_USERS."`", $this->id_users)->where("A.`".self::ID_PR."` NOT IN(SELECT `".self::ID_PR."` FROM `".self::WH_PR."` WHERE `".self::ID_WH."` = '".$wh_id."')");
		
		$this->load->helper('warehouses/warehouses_products_helper');
		helper_not_in_wh_pr_grid_build($this->grid, $wh_id);
		
		$this->grid->create_grid_data();
		$this->grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		return $this->grid->render_grid(TRUE);
	}
	
	public function add_pr_to_wh($wh_id)
	{
		$this->template->add_css('overlay', 'jquery_tools/overlay');
		$this->template->add_js('jquery.gbc_products_addedit', 'modules_js/catalogue');
		
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		$wh = $this->mwarehouses->get_wh($wh_id);
		
		$this->template->add_title(' | Список складов | '.$wh['alias'].' | Добавить продукт');
		$this->template->add_navigation('Список складов', set_url('*/*'))->add_navigation($wh['alias'], set_url('*/*/wh_actions/wh_id/'.$wh[self::ID_WH]))->add_navigation('Добавить продукт');
		
		$this->load->model('catalogue/mproducts_save');
		$data = $this->mproducts_save->add_edit_base();
		
		$data['data_wh'] = $wh;
		$this->load->helper('warehouses/warehouses_products_helper.php');
		helper_wh_products_form_build($data);
		return TRUE;
	}
	
	public function save_pr_to_wh($wh_id)
	{
		$this->load->model('catalogue/mproducts_save');
		if($pr_id = $this->mproducts_save->save_pr(FALSE, $wh_id))
		{
			$this->load->model('warehouse/mwarehouses_logs');
			$POST_P = $this->input->post('products');
			$POST_W = $this->input->post('warehouse');
			
			$this->mwarehouses_logs->create_add_pr_log($wh_id, $pr_id, $POST_P['sku']);
			$this->mwarehouses_logs->create_edit_pr_log($wh_id, $pr_id, $POST_P['sku'], $POST_W[$wh_id]['qty'], 0);
			//create_edit_pr_log($wh_id, $pr_id, $sku, $qty, $was_qty = FALSE)
			return TRUE;
		}
		return FALSE;
	}
	
	public function add_exist_pr_to_wh($wh_id, $pr_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$wh = $this->mwarehouses->get_wh($wh_id);
		
		$this->template->add_title('Склад | Список складов | '.$wh['alias'].' | Добавить существующий продукт');
		$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('warehouse/warehouses'))->add_navigation($wh['alias'], set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh[self::ID_WH]))->add_navigation('Добавить существующий продукт');
		
		$query = $this->db->select("`".self::ID_PR."`, `sku`")
				->from("`".self::PR."`")
				->where("`".self::ID_PR."`", $pr_id)->where("`".self::ID_USERS."`", $this->id_users)->where("(SELECT COUNT(*) FROM `".self::WH_PR."` WHERE `".self::ID_WH."` = '".$wh_id."' && `".self::ID_PR."` = '".$pr_id."') = 0")->limit(1);
		$pr = $query->get()->row_array();
		if(count($pr) == 0) return FALSE;
		
		$data['data_wh'] = $wh;
		$data['data_pr'] = $pr;
		
		$this->load->helper('warehouses/warehouses_products_helper.php');
		helper_wh_exist_pr_form_build($data);
		return TRUE;
	}
	
	public function save_exist_pr_to_wh($wh_id, $pr_id)
	{
		if(!$POST = $this->input->post('warehouse')) return FALSE;
		if(isset($POST[$wh_id]['qty']) && ($qty = intval($POST[$wh_id]['qty'])) >= 0)
		{
			$this->load->model('warehouse/mwarehouses');
			if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
			
			$query = $this->db->select("`".self::ID_PR."`, `sku`")
					->from("`".self::PR."`")
					->where("`".self::ID_PR."`", $pr_id)->where("`".self::ID_USERS."`", $this->id_users)->where("(SELECT COUNT(*) FROM `".self::WH_PR."` WHERE `".self::ID_WH."` = '".$wh_id."' && `".self::ID_PR."` = '".$pr_id."') = 0")->limit(1);
			$pr = $query->get()->row_array();
			if(count($pr) == 0) return FALSE;
			
			$this->sql_add_data(array(self::ID_WH => $wh_id, self::ID_PR => $pr_id, 'sku' => $pr['sku'], 'qty' => $qty))->sql_save(self::WH_PR);
			$this->load->model('warehouse/mwarehouses_logs');
			$this->mwarehouses_logs->create_add_pr_log($wh_id, $pr_id, $pr['sku']);
			$this->mwarehouses_logs->create_edit_pr_log($wh_id, $pr_id, $pr['sku'], $qty, 0);
			return TRUE;
		}
		return FALSE;
	}
	
	public function add_pr_qty($wh_id, $pr_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$wh = $this->mwarehouses->get_wh($wh_id);
		
		$this->template->add_title('Склад | Список складов | '.$wh['alias'].' | Добавить количество продукта');
		$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('warehouse/warehouses'))->add_navigation($wh['alias'], set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh[self::ID_WH]))->add_navigation('Добавить количество продукта');
		
		$query = $this->db->select("`sku`, `qty`")
					->from("`".self::WH_PR."`")
					->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
		$pr = $query->get()->row_array();
		if(count($pr) == 0) return FALSE;
		
		$data['warehouse_product'] = $pr;
		$this->load->helper('warehouses/warehouses_products_helper');
		helper_add_pr_qty_form_build($data, $wh_id, $pr_id);
		return TRUE;
	}
	
	public function save_add_pr_qty($wh_id, $pr_id)
	{
		if(($POST = $this->input->post('warehouse_product')) && isset($POST['add_qty']) && ($add_qty = intval($POST['add_qty']))>0)
		{
			$this->load->model('warehouse/mwarehouses');
			if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
			
			$query = $this->db->select("`".self::ID_WH_PR."`, `sku`, `qty`")
						->from("`".self::WH_PR."`")
						->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
			$wh_pr = $query->get()->row_array();
			if(count($wh_pr) == 0) return FALSE;
			
			$this->load->model('warehouse/mwarehouses_logs');
			$this->db->trans_start();
			$this->sql_add_data(array('qty' => ($wh_pr['qty']+$add_qty)))->sql_save(self::WH_PR, $wh_pr[self::ID_WH_PR]);
			$this->mwarehouses_logs->create_edit_pr_log($wh_id, $pr_id, $wh_pr['sku'], $wh_pr['qty']+$add_qty, $wh_pr['qty']);
			$this->db->trans_complete();	
			if($this->db->trans_status())
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function reject_pr_qty($wh_id, $pr_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$wh = $this->mwarehouses->get_wh($wh_id);
		
		$this->template->add_title('Склад | Список складов | '.$wh['alias'].' | Списать количество продукта');
		$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('warehouse/warehouses'))->add_navigation($wh['alias'], set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh[self::ID_WH]))->add_navigation('Списать количество продукта');
		
		$query = $this->db->select("`sku`, `qty`")
					->from("`".self::WH_PR."`")
					->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
		$pr = $query->get()->row_array();
		if(count($pr) == 0) return FALSE;
		
		$data['warehouse_product'] = $pr;
		$this->load->helper('warehouses/warehouses_products_helper');
		helper_reject_pr_qty_form_build($data, $wh_id, $pr_id);
		return TRUE;
	}
	
	public function save_reject_pr_qty($wh_id, $pr_id)
	{
		if(($POST = $this->input->post('warehouse_product')) && isset($POST['reject_qty']) && ($reject_qty = intval($POST['reject_qty']))>0)
		{
			$this->load->model('warehouse/mwarehouses');
			if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
			
			$query = $this->db->select("`".self::ID_WH_PR."`, `sku`, `qty`")
						->from("`".self::WH_PR."`")
						->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
			$wh_pr = $query->get()->row_array();
			if(count($wh_pr) == 0) return FALSE;
			if($wh_pr['qty'] < $reject_qty) return FALSE;
			
			$this->load->model('warehouse/mwarehouses_logs');
			$this->db->trans_start();
			$this->sql_add_data(array('qty' => ($wh_pr['qty'] - $reject_qty)))->sql_save(self::WH_PR, $wh_pr[self::ID_WH_PR]);
			$this->mwarehouses_logs->create_reject_pr_log($wh_id, $pr_id, $wh_pr['sku'], $wh_pr['qty'] - $reject_qty, $wh_pr['qty'], $POST['comment']);
			$this->db->trans_complete();	
			if($this->db->trans_status())
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function create_sale($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$wh = $this->mwarehouses->get_wh($wh_id);
		
		$this->template->add_title('Склад | Список складов | '.$wh['alias'].' | Создание продажи');
		$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('warehouse/warehouses'))->add_navigation($wh['alias'], set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh[self::ID_WH]))->add_navigation('Создание продажи');
		
		$this->load->library('cart');
		$this->cart->destroy();
		$data['sale_products'] = $this->get_create_sale_products($wh_id);
		
		$this->load->helper('warehouses/warehouses_products_helper');
		helper_create_sale_form_build($data, $wh_id);
		//self::create_sale_session;
		return TRUE;
	}
	
	public function get_create_sale_products($wh_id)
	{
		$this->load->library("nosql_grid");
		$this->nosql_grid->_init_grid("wh_create_sale_products_grid");
		$this->nosql_grid->init_fixed_buttons(FALSE);
		
		$product_array = array();
		$this->load->library('cart');
		if(count($cart_array = $this->cart->contents())>0)
		{
			$cart_products_id = array();
			$cart_products_price_id = array();
			$cart_products_attributes = array();
			foreach($cart_array as $ms)
			{
				$cart_products_id[$ms['id']] = $ms['id'];
				/*$cart_products_price_id[$ms['options']['price_id']] = $ms['options']['price_id'];
				unset($ms['options']['price_id']);
				foreach($ms['options'] as $at_key => $at)
				{
					$cart_products_attributes_id[$at_key] = $at_key;
					$cart_products_attributes_options_id[$at] = $at;
				}*/
			}
			$this->load->model('catalogue/mproducts');
			$cart_products_temp_array = $this->mproducts->get_product($cart_products_id);
			$cart_products_array = array();
			foreach($cart_products_temp_array as $ms)
			{
				$cart_products_array[$ms['PR_ID']] = $ms;
			}
			unset($cart_products_temp_array);
			//$attributes_n_options = $this->mproducts->get_product_attributes($cart_products_id);
			
			foreach($cart_array as $key => $ms)
			{
				if(isset($cart_products_array[$ms['id']]))
				{
					$product_array[$key] = array('rowid' => $ms['rowid'], 'PR_ID' => $cart_products_array[$ms['id']]['PR_ID'], 'sku' => $cart_products_array[$ms['id']]['sku'], 'name' => $cart_products_array[$ms['id']]['name'], 'qty' => $ms['qty'], 'price' => $ms['price']);
					/*$product_array[$key]['attributes'] = '';
					unset($ms['options']['price_id']);
					foreach($ms['options'] as $at_key => $at)
					{
						$product_array[$key]['attributes'] .= @$attributes_n_options[$at_key][$at]['a_name'].' : '.@$attributes_n_options[$at_key][$at]['o_name'].'<BR>';
					}*/
				}	
			}
		}
		
		$this->load->helper('warehouses/warehouses_products_helper');
		helper_sale_products_grid_build($this->nosql_grid, $wh_id);
		
		$this->nosql_grid->set_grid_data($product_array);
		return $this->nosql_grid->render_grid(TRUE);
	}
	
	public function add_pr_to_sale($wh_id, $pr_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$this->load->library('cart');
		$POST = $this->input->post();
		
		$query = $this->db->select("`qty`")
				->from("`".self::WH_PR."`")
				->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
		$cur_qty = $query->get()->row_array();
		if(count($cur_qty) == 0) return FALSE;
		$cur_qty = $cur_qty['qty'];
		if($cur_qty == 0) return FALSE;
		if($POST['qty'] > $cur_qty) return FALSE;
		
		$product = array('id' => $pr_id, 'qty' => $POST['qty'], 'price' => $POST['price'], 'name' => $pr_id, 'options' => array());
		$rowid = $this->cart->insert($product);
		$grid = $this->get_create_sale_products($wh_id);
		return array('rowid' => $rowid, 'grid' => $grid);
	}
	
	public function delete_pr_from_sale($wh_id, $rowid)
	{
		$this->load->library('cart');
		$data = array(
			'rowid' => $rowid,
			'qty'	=> 0
		);
		$this->cart->update($data);
		$grid = $this->get_create_sale_products($wh_id);
		return array('rowid' => $rowid, 'grid' => $grid);
	}
	
	public function view_edit_pr_sale_qty($wh_id, $rowid)
	{
		$this->load->library('cart');
		if(count($cart_array = $this->cart->contents())>0)
		{
			if(!isset($cart_array[$rowid])) return FALSE;
			$cart_row = $cart_array[$rowid];
			$pr_data = array();
			$this->load->model('catalogue/mproducts');
			if($pr_data = $this->mproducts->get_view_product($cart_row['id']))
			{
				$query = $this->db->select("`qty`")
					->from("`".self::WH_PR."`")
					->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $cart_row['id'])->limit(1);
				$cur_qty = $query->get()->row_array();
				return $pr_data + array('wh_id' => $wh_id, 'current_qty' => $cur_qty['qty'], 'qty' => $cart_row['qty'], 'price' => $cart_row['price']);
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function get_wh_pr_grid($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$this->load->library('grid');
		$this->grid->_init_grid('wh_products_grid_'.$wh_id, array('limit' => 50, 'url' => set_url('*/warehouses_products/ajax_get_wh_pr_grid/wh_id/'.$wh_id)));
		
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, C.`qty`")
			->from("`".self::WH_PR."` AS C")
			->join("`".self::PR."` AS A",
					"A.`".self::ID_PR."` = C.`".self::ID_PR."`",
					"INNER")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("C.`".self::ID_WH."`", $wh_id);

		$this->load->helper('warehouses/warehouses_products_helper');
		helper_wh_products_grid_build($this->grid, $wh_id);
	
		$this->grid->create_grid_data();
		
		$this->grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		return $this->grid->render_grid(TRUE);
	}
	
	public function get_create_sale_wh_pr_grid($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$this->load->library('grid');
		$this->grid->_init_grid('wh_create_sale_products_grid_'.$wh_id, array('limit' => 50, 'url' => set_url('*/warehouses_products/ajax_get_create_sale_wh_pr_grid/wh_id/'.$wh_id)));
		
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, C.`qty`")
			->from("`".self::WH_PR."` AS C")
			->join("`".self::PR."` AS A",
					"A.`".self::ID_PR."` = C.`".self::ID_PR."`",
					"INNER")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("C.`".self::ID_WH."`", $wh_id);

		$this->load->helper('warehouses/warehouses_products_helper');
		helper_sale_wh_products_grid_build($this->grid, $wh_id);
	
		$this->grid->create_grid_data();
		
		$this->grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		return $this->grid->render_grid(TRUE);
	}
	
	public function get_pr_to_sale($wh_id, $pr_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$this->load->model('catalogue/mproducts');
		if($pr_data = $this->mproducts->get_view_product($pr_id))
		{
			$query = $this->db->select("`qty`")
				->from("`".self::WH_PR."`")
				->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
			$cur_qty = $query->get()->row_array();
			return $pr_data + array('wh_id' => $wh_id, 'current_qty' => $cur_qty['qty']);
		}
		return FALSE;
	}
	
	public function save_wh_sale($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$product_array = array();
		$this->load->library('cart');
		$pr_array = array();
		$pr_array_qty = array();
		$pr_log_array = array();
		
		if(count($cart_array = $this->cart->contents())==0) return FALSE;
		foreach($cart_array as $ms)
		{
			$pr_array[$ms['id']] = $ms['id'];
			$pr_array_qty[$ms['id']] = $ms['qty'];
			$pr_array_price[$ms['id']] = $ms['price'];
		}
		
		$this->load->model('catalogue/mcurrency');
		$currency = $this->mcurrency->get_users_default_currency();
		
		$this->db->trans_start();
		$query = $this->db->select("`".self::ID_WH_PR."`, `".self::ID_PR."`, `sku`, `qty`")
				->from("`".self::WH_PR."`")
				->where("`".self::ID_WH."`", $wh_id)->where_in("`".self::ID_PR."`", $pr_array);
		$sale_log_array = array();
		$sale_log_array['total_qty'] = 0;
		$sale_log_array['subtotal'] = 0;
		$sale_log_array['total'] = 0;
		$sale_log_array['id_m_c_currency'] = 0;
		foreach($query->get()->result_array() as $ms)
		{
			if(isset($pr_array_qty[$ms[self::ID_PR]]) && $pr_array_qty[$ms[self::ID_PR]] > 0 && $pr_array_qty[$ms[self::ID_PR]] <= $ms['qty'])
			{
				$this->sql_add_data(array('qty' => ($ms['qty'] - $pr_array_qty[$ms[self::ID_PR]])))->sql_save(self::WH_PR, $ms[self::ID_WH_PR]);
				$pr_log_array[] = array(self::ID_PR => $ms[self::ID_PR], 'sku' => $ms['sku'], 'qty' => $pr_array_qty[$ms[self::ID_PR]], 'price' => $pr_array_price[$ms[self::ID_PR]], 'id_m_c_currency' => $currency['id_m_c_currency']);
				
				$sale_log_array['total_qty'] += $pr_array_qty[$ms[self::ID_PR]];
				$sale_log_array['subtotal'] += $pr_array_price[$ms[self::ID_PR]]*$pr_array_qty[$ms[self::ID_PR]];
				$sale_log_array['total'] += $pr_array_price[$ms[self::ID_PR]]*$pr_array_qty[$ms[self::ID_PR]];;
				$sale_log_array['id_m_c_currency'] = $currency['id_m_c_currency'];
			}
			else
			{
				return FALSE;
			}
		}
		$this->load->model('warehouse/mwarehouses_logs');
		$this->mwarehouses_logs->create_sale_log($wh_id, $sale_log_array, $pr_log_array);
		
		$this->db->trans_complete();	
		if($this->db->trans_status())
		{
			$this->cart->destroy();
			return TRUE;
		}
		return FALSE;
	}
	
	public function create_transfer($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$wh = $this->mwarehouses->get_wh($wh_id);
		
		$this->load->library('cart');
		$this->cart->destroy();
		
		$this->template->add_title('Склад | Список складов | '.$wh['alias'].' | Перенос продуктов');
		$this->template->add_navigation('Склад')->add_navigation('Список складов', set_url('warehouse/warehouses'))->add_navigation($wh['alias'], set_url('warehouse/warehouses/wh_actions/wh_id/'.$wh[self::ID_WH]))->add_navigation('Перенос продуктов');
		
		$data['wh_to_array'] = $this->mwarehouses->get_wh_to_select();
		unset($data['wh_to_array'][$wh_id]);
		$data['wh'] = $wh;
		
		$data['transfer_products'] = $this->get_create_transfer_prosucts($wh_id);
		
		$this->load->helper('warehouses/warehouses_products_helper');
		helper_create_transfer_form_build($data, $wh_id);
		
		return TRUE;
	}
	
	public function get_transfer_wh_pr_grid($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$this->load->library('grid');
		$this->grid->_init_grid('wh_create_transfer_products_grid_'.$wh_id, array('limit' => 50, 'url' => set_url('*/warehouses_products/ajax_get_transfer_wh_pr_grid/wh_id/'.$wh_id)));
		
		$this->grid->db
			->select("A.`".self::ID_PR."` AS ID, A.`sku`, B.`name`, A.`status`, C.`qty`")
			->from("`".self::WH_PR."` AS C")
			->join("`".self::PR."` AS A",
					"A.`".self::ID_PR."` = C.`".self::ID_PR."`",
					"INNER")
			->join(	"`".self::PR_DESC."` AS B",
					"B.`".self::ID_PR."` = A.`".self::ID_PR."` && B.`".self::ID_LANGS."` = ".$this->id_langs,
					"LEFT")
			->where("C.`".self::ID_WH."`", $wh_id);

		$this->load->helper('warehouses/warehouses_products_helper');
		helper_transfer_wh_products_grid_build($this->grid, $wh_id);
	
		$this->grid->create_grid_data();
		
		$this->grid->update_grid_data('status', array('0' => 'Нет', '1' => 'Да'));
		return $this->grid->render_grid(TRUE);
	}
	
	public function get_create_transfer_prosucts($wh_id)
	{
		$this->load->library("nosql_grid");
		$this->nosql_grid->_init_grid("wh_create_transfer_products_grid");
		$this->nosql_grid->init_fixed_buttons(FALSE);
		
		$product_array = array();
		$this->load->library('cart');
		if(count($cart_array = $this->cart->contents())>0)
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
					$product_array[$key] = array('rowid' => $ms['rowid'], 'PR_ID' => $cart_products_array[$ms['id']]['PR_ID'], 'sku' => $cart_products_array[$ms['id']]['sku'], 'name' => $cart_products_array[$ms['id']]['name'], 'qty' => $ms['qty'], 'price' => $ms['price']);
				}	
			}
		}
		
		$this->load->helper('warehouses/warehouses_products_helper');
		helper_transfer_products_grid_build($this->nosql_grid, $wh_id);
		
		$this->nosql_grid->set_grid_data($product_array);
		return $this->nosql_grid->render_grid(TRUE);
	}
	
	public function get_pr_to_transfer($wh_id, $pr_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$this->load->model('catalogue/mproducts');
		if($pr_data = $this->mproducts->get_view_product($pr_id))
		{
			$query = $this->db->select("`qty`")
				->from("`".self::WH_PR."`")
				->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
			$cur_qty = $query->get()->row_array();
			return $pr_data + array('wh_id' => $wh_id, 'current_qty' => $cur_qty['qty']);
		}
		return FALSE;
	}
	
	public function add_pr_to_transfer($wh_id, $pr_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$this->load->library('cart');
		$POST = $this->input->post();
		
		$query = $this->db->select("`qty`")
				->from("`".self::WH_PR."`")
				->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $pr_id)->limit(1);
		$cur_qty = $query->get()->row_array();
		if(count($cur_qty) == 0) return FALSE;
		$cur_qty = $cur_qty['qty'];
		if($cur_qty == 0) return FALSE;
		if($POST['qty'] > $cur_qty) return FALSE;
		
		$product = array('id' => $pr_id, 'qty' => $POST['qty'], 'price' => 1, 'name' => $pr_id, 'options' => array());
		$rowid = $this->cart->insert($product);
		$grid = $this->get_create_transfer_prosucts($wh_id);
		return array('rowid' => $rowid, 'grid' => $grid);
	}
	
	public function delete_pr_from_transfer($wh_id, $rowid)
	{
		$this->load->library('cart');
		$data = array(
			'rowid' => $rowid,
			'qty'	=> 0
		);
		$this->cart->update($data);
		$grid = $this->get_create_transfer_prosucts($wh_id);
		return array('rowid' => $rowid, 'grid' => $grid);
	}
	
	public function view_edit_pr_transfer_qty($wh_id, $rowid)
	{
		$this->load->library('cart');
		if(count($cart_array = $this->cart->contents())>0)
		{
			if(!isset($cart_array[$rowid])) return FALSE;
			$cart_row = $cart_array[$rowid];
			$pr_data = array();
			$this->load->model('catalogue/mproducts');
			if($pr_data = $this->mproducts->get_view_product($cart_row['id']))
			{
				$query = $this->db->select("`qty`")
					->from("`".self::WH_PR."`")
					->where("`".self::ID_WH."`", $wh_id)->where("`".self::ID_PR."`", $cart_row['id'])->limit(1);
				$cur_qty = $query->get()->row_array();
				return $pr_data + array('wh_id' => $wh_id, 'current_qty' => $cur_qty['qty'], 'qty' => $cart_row['qty']);
			}
			return FALSE;
		}
		return FALSE;
	}
	
	public function save_wh_transfer($wh_id)
	{
		$this->load->model('warehouse/mwarehouses');
		if(!$this->mwarehouses->check_isset_wh($wh_id)) return FALSE;
		
		$POST = $this->input->post('wh');
		if(!$this->mwarehouses->check_isset_wh($POST['to'])) return FALSE;
		
		$comment = NULL;
		if(trim($POST['comment']) != '') $comment = $POST['comment'];
		$product_array = array();
		$this->load->library('cart');
		$pr_array = array();
		$pr_array_qty = array();
		$pr_log_array = array();
		
		if(count($cart_array = $this->cart->contents())==0) return FALSE;
		foreach($cart_array as $ms)
		{
			$pr_array[$ms['id']] = $ms['id'];
			$pr_array_qty[$ms['id']] = $ms['qty'];
		}
		
		$query = $this->db->select("`".self::ID_WH_PR."`, `".self::ID_PR."`, `sku`, `qty`")
				->from("`".self::WH_PR."`")
				->where("`".self::ID_WH."`", $POST['to'])->where_in("`".self::ID_PR."`", $pr_array);
		$tr_pr_temp = $query->get()->result_array();
		$tr_to_pr = array();
		foreach($tr_pr_temp as $ms)
		{
			$tr_to_pr[$ms[self::ID_PR]] = $ms;
		}
		
		$query = $this->db->select("`".self::ID_WH_PR."`, `".self::ID_PR."`, `sku`, `qty`")
				->from("`".self::WH_PR."`")
				->where("`".self::ID_WH."`", $wh_id)->where_in("`".self::ID_PR."`", $pr_array);
		$tr_pr_temp = $query->get()->result_array();
		$tr_pr_from = array();
		foreach($tr_pr_temp as $ms)
		{
			if($ms['qty'] < $pr_array_qty[$ms[self::ID_PR]]) return FALSE;
			$tr_pr_from[$ms[self::ID_PR]] = $ms;
		}
		
		$transfer_log_array = array();
		$transfer_log_array['id_wh_from'] = $wh_id;
		$transfer_log_array['id_wh_to'] = $POST['to'];
		$transfer_log_array['total_qty'] = 0;
		
		$this->db->trans_start();
		foreach($tr_pr_from as $key => $ms)
		{
			if(isset($tr_to_pr[$key]))
			{
				$this->sql_add_data(array('qty' => ($ms['qty'] - $pr_array_qty[$ms[self::ID_PR]])))->sql_save(self::WH_PR, $ms[self::ID_WH_PR]);
				$this->sql_add_data(array('qty' => ($tr_to_pr[$ms[self::ID_PR]]['qty'] + $pr_array_qty[$ms[self::ID_PR]])))->sql_save(self::WH_PR, $tr_to_pr[$ms[self::ID_PR]][self::ID_WH_PR]);
			}
			else
			{
				$this->sql_add_data(array('qty' => ($ms['qty'] - $pr_array_qty[$ms[self::ID_PR]])))->sql_save(self::WH_PR, $ms[self::ID_WH_PR]);
				$this->sql_add_data(array(self::ID_WH => $POST['to'], self::ID_PR => $ms[self::ID_PR], 'sku' => $ms['sku'], 'qty' => $pr_array_qty[$ms[self::ID_PR]]))->sql_save(self::WH_PR);
			}
			$transfer_log_array['total_qty'] += $pr_array_qty[$ms[self::ID_PR]];
			$pr_log_array[] = array(self::ID_PR => $ms[self::ID_PR], 'sku' => $ms['sku'], 'qty' => $pr_array_qty[$ms[self::ID_PR]]);
		}
		$this->load->model('warehouse/mwarehouses_logs');
		$this->mwarehouses_logs->create_transfer_log($wh_id, $transfer_log_array, $pr_log_array, $comment);
		
		$this->db->trans_complete();
		if($this->db->trans_status())
		{
			$this->cart->destroy();
			return TRUE;
		}
		return FALSE;
	}
}
?>