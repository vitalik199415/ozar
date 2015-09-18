<?php
class Mdiscounts extends AG_Model
{
	const DISC              = 'm_c_discounts';
    const ID_DISC           = 'id_m_c_discounts';
	
	protected $DISC_TYPES = array(
		'SUM' => 0,
		'PERCENT' => 1
	);
	
	protected $discounts_array = FALSE;
	
	function __construct()
    {
        parent::__construct();
		$this->load->library('cart');
		$this->get_discounts_array();
    }
	
	public function get_discounts_array()
	{
		if($this->discounts_array) return $this->discounts_array;
		$disc_array = array();
		$this->db->select("*")
				->from("`".self::DISC."`")
				->where("`".self::ID_USERS."`", $this->id_users)->where("`active`", 1);
		$result = $this->db->get()->result_array();
		foreach($result as $ms) {
			$disc_array[] = $ms;
		}
		$this->discounts_array = $disc_array;
		return $this->discounts_array;
	}
	
	public function get_discount_sum($products)
	{
		$sum = 0;
		foreach($products as $ms) {
			if($ms['products']['sale'] != 1) $sum += $ms['cart']['subtotal'];
		}
		if(!$disc_row = $this->get_discount_row($sum)) return 0;
		return $this->calculate_discount_sum($sum, $disc_row);
		//return $this->get_discount($sum);
	}
	
	protected function get_discount_row($sum)
	{
		$row = FALSE;
		$disc_array = $this->get_discounts_array();
		foreach($disc_array as $ms)
		{
			$sum_from = (float) $ms['sum_from'];
			$sum_to = (float) $ms['sum_to'];
			if($sum_to == 0) $sum_to = $sum + 1;
			if($sum >= $sum_from && $sum < $sum_to) {
				$row = $ms;
				break;
			}
		}
		return $row;
	}
	
	protected function calculate_discount_sum($sum, $row)
	{
		if($row['type_discounts'] == $this->DISC_TYPES['SUM'])
		{
			return $row['discount_sum'];
		}
		return $sum * $row['discount_percent'] / 100;
	}


	public function get_discount($cart_products) {
		if($code = $this->session->get_data('coupon_code')) {
			$this->load->model('sales/mdiscount_coupons');
			return $this->mdiscount_coupons->check_coupon_discount($cart_products);
		} else {
			return $this->check_discount();
		}
	}

	public function check_discount() {
		$order_summ = $this->cart->total();
		$discount = $this->db ->select('*')
			->from('`'.self::DISC.'`')
			->where('`sum_from` < '.$order_summ)
			->where("`".self::ID_USERS."`", $this->id_users)->where('`active`', 1)
			->order_by('`sum_from`', 'DESC')->limit(1)->get()->row_array();

		$disc = 0;
		if(count($discount) > 0) {
			if($discount['type_discounts'] == 0) {
				$disc = $discount['discount_sum'];
			} else {
				$disc = $order_summ * $discount['discount_percent'] / 100;
			}
		}

		if($disc > 0) {
			return array('result' => 1, 'discount' => $disc, 'products' => FALSE);
		} else {
			return array('result' => 0, 'discount' => 0, 'products' => FALSE);
		}
	}
}
?>