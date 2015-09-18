<?php
class Mwarehouses extends AG_Model
{
	const WH = 'wh';
	const ID_WH = 'id_wh';
	const WH_SH = 'wh_shops';
	const ID_WH_SH = 'id_wh_shops';
	const WHNSH = 'wh_whNshops';

	const WH_PR = 'wh_products';

	const PR = 'm_c_products';
	const ID_PR = 'id_m_c_products';
	const PR_DESC = 'm_c_products_description';

	public $id_wh = FALSE;
	public $id_wh_shop = FALSE;

	private $shop_wh = FALSE;
	private $shop_wh_id = FALSE;
	private $first_get_wh_shop_query = TRUE;

	public function __construct()
	{
		parent::__construct();
	}

	public function view($wh_id)
	{
		$wh_id = intval($wh_id);
		if(!$this->check_isset_wh($wh_id)) return FALSE;
		$this->load->model('warehouse/mwarehouses');
		$wh = $this->mwarehouses->get_wh($wh_id);
		$this->template->add_title(' | '.$wh['alias']);
		$this->template->add_navigation($wh['alias']);

		$data['all_wh'] = $this->mwarehouses->get_wh_to_select();

		$this->load->model('warehouse/mwarehouses_products');
		$data['wh_products'] = $this->mwarehouses_products->get_wh_pr_grid($wh_id);

		$this->load->helper('warehouses');
		helper_wh_actions_form_build($data, $wh_id);
		return TRUE;
	}

	public function get_shop_wh()
	{
		$this->load->model('warehouse/mwh_settings');
		$wh_settings = $this->mwh_settings->get_settings();
		$wh_id = FALSE;
		if ($wh_settings['wh_on'] && $wh_settings['wh_active'])
		{
			$this->db->select("`" . self::ID_WH . "`")->from("`" . self::WH . "`")
				->where("`" . self::ID_USERS . "`", $this->id_users)->where("`active`", 1)
				->where("`i_s_wh`", 1)->limit(1);
			$SHOP_WH = $this->db->get()->row_array();
			if (count($SHOP_WH) > 0)
			{
				$wh_id = $SHOP_WH[self::ID_WH];
			}
		}
		return $wh_id;
	}

	public function get_shop_wh_id()
	{
		if($this->first_get_wh_shop_query === FALSE) return $this->shop_wh_id;
		$this->first_get_wh_shop_query = FALSE;
		$user = $this->musers->get_user();
		if($user['warehouse'] == 0) return FALSE;

		$this->db->select("*")
				 ->from("`".self::WH."`")
				 ->where("`".self::ID_USERS."`", $this->id_users)
				 ->where("`i_s_wh`", 1)->limit(1);
		if(count($WH = $this->db->get()->row_array()) > 0)
		{
			$this->shop_wh = $WH;
			$this->shop_wh_id = $WH[self::ID_WH];
			return $this->shop_wh_id;
		}
		return FALSE;
	}

	public function get_wh_product_total_qty($wh_id, $pr_id)
	{
		$qty = 0;
		$this->db->select("`qty`")
			->from("`" . self::WH_PR . "`")
			->where("`" . self::ID_WH . "`", $wh_id)
			->where("`" . self::ID_PR . "`", $pr_id)->limit(1);
		if(count($result = $this->db->get()->row_array()) > 0)
		{
			$qty = $result['qty'];
		}
		return $qty;
	}

	public function get_wh_product_total_qty_array($wh_id, array $pr_id)
	{
		$qty_array = array();
		foreach ($pr_id as $ms)
		{
			$qty_array[$ms] = array(self::ID_PR => $ms, 'qty' => 0);
		}

		$this->db->select("`qty`, `" . self::ID_PR . "`")
			->from("`" . self::WH_PR . "`")
			->where("`" . self::ID_WH . "`", $wh_id)
			->where_in("`" . self::ID_PR . "`", $pr_id);
		$result = $this->db->get()->result_array();
		foreach ($result as $ms)
		{
			$qty_array[$ms[self::ID_PR]] = array(self::ID_PR => $ms[self::ID_PR], 'qty' => $ms['qty']);
		}
		return $qty_array;
	}

	public function get_wh($wh_id)
	{
		$this->db->select("*")
			->from("`" . self::WH . "`")
			->where("`" . self::ID_WH . "`", $wh_id)
			->where("`" . self::ID_USERS . "`", $this->id_users)->limit(1);
		if (count($result = $this->db->get()->row_array()) > 0)
		{
			return $result;
		}
		return FALSE;
	}

	public function get_wh_to_select()
	{
		$array = array();
		$query = $this->db->select("`" . self::ID_WH . "` AS ID, `alias`")->from("`" . self::WH . "`")
						  ->where("`" . self::ID_USERS . "`", $this->id_users);
		$result = $query->get()->result_array();
		foreach ($result as $ms)
		{
			$array[$ms['ID']] = $ms['alias'];
		}
		return $array;
	}

	public function render_wh_grid()
	{
		$this->load->library('grid');
		$this->grid->_init_grid('warehouses_grid');
		$this->grid->db->select("`" . self::ID_WH . "` AS ID, `alias`, `active`, `i_s_wh`")->from("`" . self::WH . "`")
					   ->where("`" . self::ID_USERS . "`", $this->id_users);

		$this->load->helper('warehouses');
		helper_wh_grid_build($this->grid);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->update_grid_data('i_s_wh', array('0' => 'Нет', '1' => 'Да'));
		$this->grid->render_grid();
	}

	public function render_wh_shops_grid()
	{
		$data['wh'] = $this->get_wh_to_select();

		$this->load->library('grid');
		$this->grid->_init_grid('warehouses_shops_grid', array(), TRUE);

		if ($extra_search = $this->grid->get_options('search'))
		{
			if (isset($extra_search[self::ID_WH]))
			{
				$temp_extra_search = $extra_search;
				unset($temp_extra_search[self::ID_WH]);
				$this->grid->set_options('search', $temp_extra_search);
				$update_select_wh = $extra_search[self::ID_WH];
			}
		}

		$qty_query = clone $this->db;
		$qty_query->select("COUNT(*) AS numrows")->from("`" . self::WH_SH . "` AS A")
				  ->where("A.`" . self::ID_USERS . "`", $this->id_users);

		$this->grid->db->select("A.`" . self::ID_WH_SH . "` AS ID, A.`alias`, A.`active`, GROUP_CONCAT(B.`alias` ORDER BY B.`" . self::ID_WH . "` SEPARATOR '<BR>') AS `" . self::ID_WH . "`")
					   ->from("`" . self::WH_SH . "` AS A")
					   ->join("`" . self::WHNSH . "` AS N", "N.`" . self::ID_WH_SH . "` = A.`" . self::ID_WH_SH . "`", "INNER")
					   ->join("`" . self::WH . "` AS B", "B.`" . self::ID_WH . "` = N.`" . self::ID_WH . "`", "LEFT")
					   ->where("A.`" . self::ID_USERS . "`", $this->id_users)->order_by("A.`" . self::ID_WH_SH . "`")
					   ->group_by("A.`" . self::ID_WH_SH . "`");

		if (isset($update_select_wh))
		{
			$update_select_wh = intval($update_select_wh);
			if ($update_select_wh > 0)
			{
				$this->grid->db->join("`" . self::WHNSH . "` AS NN", "NN.`" . self::ID_WH_SH . "` = A.`" . self::ID_WH_SH . "` && NN.`" . self::ID_WH . "` = '" . $update_select_wh . "'", "INNER");
				$qty_query->join("`" . self::WHNSH . "` AS N", "N.`" . self::ID_WH_SH . "` = A.`" . self::ID_WH_SH . "` && N.`" . self::ID_WH . "` = '" . $update_select_wh . "'", "INNER");
			}
		}

		$this->grid->set_extra_select_qty_object($qty_query);
		unset($qty_query);

		$this->load->helper('warehouses');
		helper_wh_shops_grid_build($this->grid, $data);

		$this->grid->create_grid_data();
		$this->grid->update_grid_data('active', array('0' => 'Нет', '1' => 'Да'));

		if (isset($update_select_wh))
		{
			$extra_search = $this->grid->get_options('search');
			$extra_search[self::ID_WH] = $update_select_wh;
			$this->grid->set_search_manualy(self::ID_WH, $update_select_wh);
			$this->grid->set_options('search', $extra_search);
		}

		$this->grid->render_grid();
	}

	public function check_isset_wh($wh_id)
	{
		$query = $this->db->select("COUNT(*) AS COUNT")->from("`" . self::WH . "`")
						  ->where("`" . self::ID_WH . "`", $wh_id)->where("`" . self::ID_USERS . "`", $this->id_users);
		$result = $query->get()->row_array();
		if ($result['COUNT'] == 1) return TRUE;
		return FALSE;
	}
}
?>