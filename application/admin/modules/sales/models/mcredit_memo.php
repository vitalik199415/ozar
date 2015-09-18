<?php
class Mcredit_memo extends AG_Model
{
	const CM		= 'm_orders_credit_memo';
	const ID_CM		= 'id_m_orders_credit_memo';
	const ORD 		= 'm_orders';
	const ID_ORD 	= 'id_m_orders';
	const ORD_PR 	= 'm_orders_products';
	const ID_ORD_PR = 'id_m_orders_products';
	const INV 		= 'm_orders_invoices';
	const ID_INV 	= 'id_m_orders_invoices';
	const SHP 		= 'm_orders_shippings';
	const ID_SHP 	= 'id_m_orders_shippings';
	
	public function render_credit_memo_grid()
	{
		$this->load->library("grid");
		$this->grid->_init_grid("invoices_grid", array('sort' => 'credit_memo_number', 'desc' => 'DESC'));
		
		$this->grid->db
				->select("A.`".self::ID_CM."` AS ID, A.`credit_memo_number`, A.`create_date`, A.`admin_note`, B.`orders_number`, CONCAT(B.`total`, ' ', B.`base_currency_name`) AS total, C.`invoices_number`, D.`shippings_number`")
				->from("`".self::CM."` AS A")
				->join(	"`".self::ORD."` AS B",
						"B.`".self::ID_ORD."` = A.`".self::ID_ORD."`",
						"LEFT")
				->join(	"`".self::INV."` AS C",
						"C.`".self::ID_INV."` = A.`".self::ID_INV."`",
						"LEFT")
				->join(	"`".self::SHP."` AS D",
						"D.`".self::ID_SHP."` = A.`".self::ID_SHP."`",
						"LEFT")
				->where("A.`".self::ID_USERS."`", $this->id_users);
		$this->load->helper("credit_memo");
		helper_credit_memo_grid_build($this->grid);
		$this->grid->create_grid_data();
		$this->grid->render_grid();
	}
	
	public function view_credit_memo($cm_id)
	{
		$cm_data = $this->get_credit_memo_data($cm_id);
		$this->load->helper("credit_memo");
		helper_credit_memo_view_credit_memo($cm_data);
		return TRUE;
	}

	public function get_credit_memo_data($shp_id)
	{
		if(!$cm = $this->get_credit_memo($shp_id)) return FALSE;
		$this->load->model('sales/morders');
		$order_data = $this->morders->get_order_data($cm[self::ID_ORD]);
		$order_data['order_products_grid'] = $this->morders->render_not_edited_order_products_grid($cm[self::ID_ORD]);
		$cm_data = $order_data;
		$cm_data['credit_memo'] = $cm;
		return $cm_data;
	}

	public function get_credit_memo($cm_id)
	{
		$this->db->select("*")
				 ->from("`".self::CM."`")
				 ->where("`".self::ID_CM."`", $cm_id)->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		if(count($cm = $this->db->get()->row_array()) == 0) return FALSE;
		return $cm;
	}
	
	public function create_credit_memo($ord_id)
	{
		$this->load->model('sales/morders');
		if(!$this->morders->check_possibility_creating_credit_memo($ord_id)) return FALSE;
		$order_data = $this->morders->get_order_data($ord_id);
		$order_data['order_products_grid'] = $this->morders->render_not_edited_order_products_grid($ord_id);
		$this->load->helper("credit_memo");
		helper_credit_memo_create_credit_memo($order_data);
		return TRUE;
	}
	
	public function save_credit_memo($ord_id)
	{
		$ord_id = intval($ord_id);
		if($ord_id <= 0) return FALSE;
		$this->load->model('sales/morders');
		if(!$this->morders->isset_order($ord_id)) return FALSE;
		if(!$this->morders->check_possibility_creating_credit_memo($ord_id)) return FALSE;
		
		if(!$CM_POST = $this->input->post('credit_memo')) return FALSE;
		
		$order_data = $this->morders->get_order_data($ord_id);
		
		$this->db->select("MAX(credit_memo_number) AS credit_memo_number")
			->from("`".self::CM."`")
			->where("`".self::ID_USERS."`", $this->id_users)->limit(1);
		$cm_number = $this->db->get()->row_array();
		$max = 1;
		if(count($cm_number)>0)
		{
			$max = intval($cm_number['credit_memo_number']) + 1;
		}
		$cm_number = str_repeat("0", 8-strlen($max)).($max);
		
		$this->db->trans_start();

		$this->load->model('warehouse/mwarehouses_sales');
		$this->mwarehouses_sales->create_order_sale_credit_memo($ord_id);
		
		$this->sql_add_data(array('invoices_status' => 'CM'))->sql_update_date()->sql_save(self::INV, $order_data['invoice'][self::ID_INV]);
		$shp_id = NULL;
		if($order_data['shipping'])
		{
			$this->sql_add_data(array('shippings_status' => 'CM'))->sql_update_date()->sql_save(self::SHP, $order_data['shipping'][self::ID_SHP]);
			$shp_id = $order_data['shipping'][self::ID_SHP];
		}
		$this->sql_add_data(array('orders_state' => 'CM'))->sql_update_date()->sql_save(self::ORD, $ord_id);
		
		$id_cm = $this->sql_add_data(array(self::ID_ORD => $ord_id, self::ID_INV => $order_data['invoice'][self::ID_INV], self::ID_SHP => $shp_id, 'credit_memo_number' => $cm_number, 'admin_note' => $CM_POST['admin_note']))->sql_using_user()->sql_update_date()->sql_save(self::CM);
		$this->db->trans_complete();
		if($this->db->trans_status())
		{
			return $id_cm;
		}
		return FALSE;
	}
}
?>