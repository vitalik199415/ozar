<?php
class Mdiscount_coupons extends AG_Model
{
	const D_COUP         = 'm_c_discount_coupons';
	const D_COUP_CUST    = 'm_c_discount_coupons_customers';
	const ID_D_COUP      = 'id_m_c_discount_coupons';
	const ID_D_COUP_CUST = 'id_m_c_discount_coupons_customers';
	const DC_MAIL        = 'm_c_discount_coupons_mail';
	const ID_DC_MAIL     = 'id_m_c_discount_coupons_mail';
	const DC_PROD        = 'm_c_discount_coupons_products';
	const ID_DC_PROD     = 'id_m_c_discount_coupons_products';
	const D_CUST         = 'm_u_customers';
	const ID_CUST        = 'id_m_u_customers';
	const TYPE           = 'm_u_types';
	const ID_TYPES       = 'id_m_u_types';
	const PROD           = 'm_c_products';
	const ID_PROD        = 'id_m_c_products';


	public function __construct()
	{
		parent::__construct();
		$this->load->library('cart');
	}

	/**
	 * @param $code
	 * @return array
     */
	public function use_code($code)
	{
		$cart = $this->cart->contents();
		$res = $this->db->select()
			->from('`'.self::D_COUP_CUST.'` as D_CUST')
			->join('`'.self::D_COUP.'` as D_COUP', 'D_CUST.`'.self::ID_D_COUP.'`=D_COUP.`'.self::ID_D_COUP.'`')
			->where('`full_number`', $code)/*->where('is_used', 0)*/
			->where('is_start', 1)->where('id_users', $this->id_users)
			->limit(1)->get()->row_array();

		if(count($res) < 0) {
			return array('success' => FALSE, 'message' => $this->lang->line('c_o_no_coupon'));
		}

		$disc_prod = $this->db->select()
			->from('`' . self::DC_PROD . '`')
			->where('`' . self::ID_D_COUP . '`', $res[self::ID_D_COUP])
			->get()->result_array();

		$i = 0;
		foreach ($disc_prod as $prod) {
			foreach ($cart as $ms) {
				if ($ms['id'] == $prod[self::ID_PROD]) {
					$i++;
				}
			}
		}

		if ($i == 0) {
			return array('success' => FALSE, 'message' => $this->lang->line('c_o_no_products'));
		}

		if ($res['is_used'] == 1) {
			return array('success' => FALSE, 'message' => $this->lang->line('c_o_coupon_activated'));
		}

		if (strtotime($res['date_to']) < strtotime("now")) {
			return array('success' => FALSE, 'message' => $this->lang->line('c_o_error_activate_date'));
		}

		$this->session->set_data('coupon_code', $code);
		return array('success' => TRUE, 'message' => $this->lang->line('c_o_success_activate'));
	}

	public function activate_code() {
		if($code = $this->session->get_data('coupon_code')) {
			$main = array('is_used' => 1);
			$this->db->where('`full_number`', $code)->update('`' . self::D_COUP_CUST . '`', $main);
		}

	}

	public function check_coupon_discount($cart_products) {
		if($code = $this->session->get_data('coupon_code')) {
			$coupon_products = $this->db->select('*')
				->from('`'.self::DC_PROD.'` as D_PROD')
				->join('`'.self::D_COUP_CUST.'` as D_CUST', 'D_PROD.`'.self::ID_D_COUP.'`=D_CUST.`'.self::ID_D_COUP.'`')
				->where('D_CUST.`full_number`', $code)/*->where('D_CUST.`is_used`', 1)*/
				->get()->result_array();

			$coupon_data = $this->db->select('*')
				->from('`'.self::D_COUP.'` as D_COUP')
				->join('`'.self::D_COUP_CUST.'` as D_CUST', 'D_COUP.`'.self::ID_D_COUP.'`=D_CUST.`'.self::ID_D_COUP.'`')
				->where('D_CUST.`full_number`', $code)/*->where('D_CUST.`is_used`', 1)*/
				->limit(1)->get()->row_array();

			$cart_total = $this->cart->total();
			$cart = $this->cart->contents();

			if($cart_total > $coupon_data['order_sum']) {
				if(count($coupon_products) > 0 && $coupon_data['discount_type'] == 1) {
					// TODO перещет стоимости товаров
					$currency = $this->mcurrency->get_current_currency();
					$total_discount = 0;
					foreach($cart_products as $key => $ms) {
						foreach($coupon_products as $prod) {
							$discount = 0;
							if($ms['products']['ID'] == $prod[self::ID_PROD]) {
								if($coupon_data['discount_type'] == 0) {
									$discount = $ms['cart']['qty'] * $coupon_data['discount_sum'];
									$cart_products[$key]['products_prices']['discount'] = $discount;
									$cart_products[$key]['products_prices']['special_total_price'] = $ms['products_prices']['total_price'] - ($discount * $currency['rate']);
									$cart_products[$key]['products_prices']['special_total_price_string'] = number_format($cart_products[$key]['products_prices']['special_total_price'], 2, ',', ' ');
								} else {
									$discount = $ms['products_prices']['total_price'] * $coupon_data['discount_percent'] / 100;
									$cart_products[$key]['products_prices']['discount'] = $discount;
									$cart_products[$key]['products_prices']['special_total_price'] = $ms['products_prices']['total_price'] - ($discount  * $currency['rate']);
									$cart_products[$key]['products_prices']['special_total_price_string'] = number_format($cart_products[$key]['products_prices']['special_total_price'], 2, ',', ' ');
								}
							}
							$total_discount += $discount;
						}
					}

					return array('result' => 2, 'discount' => $total_discount, 'products' => $cart_products);
				} else {
					if($coupon_data['discount_type'] == 0 ) {
						return array('result' => 1, 'discount' => $coupon_data['discount_sum'], 'products' => $cart_products);
					} else {
						$discount_summ = ($cart_total * $coupon_data['discount_percent']) / 100;
						return array('result' => 1, 'discount' => $discount_summ, 'products' => $cart_products);
					}
				}
			}
		} else {
			return array('result' => 0, 'discount' => 0, 'products' => FALSE);
		}
	}

	public function get_coupon_info($code)
	{
		$coupon_products = $this->db->select("D_PROD.`".self::ID_PROD."`")
			->from('`'.self::DC_PROD.'` as D_PROD')
			->join('`'.self::D_COUP_CUST.'` as D_CUST', 'D_PROD.`'.self::ID_D_COUP.'`=D_CUST.`'.self::ID_D_COUP.'`')
			->where('D_CUST.`full_number`', $code)
			->get()->result_array();

		$prod_id = array();
		foreach($coupon_products as $pr_id) {
			array_push($prod_id, $pr_id[self::ID_PROD]);
		}
		$this->load->model('catalogue/mcurrency');
		$currency = $this->mcurrency->get_current_currency();

		$coupon_data = $this->db->select('D_COUP.*, D_CUST.`is_used`')
			->from('`'.self::D_COUP.'` as D_COUP')
			->join('`'.self::D_COUP_CUST.'` as D_CUST', 'D_COUP.`'.self::ID_D_COUP.'`=D_CUST.`'.self::ID_D_COUP.'`')
			->where('D_CUST.`full_number`', $code)
			->limit(1)->get()->row_array();

		$coupon_data['currency'] = $currency;

		if($coupon_data) {
			return array('coupon_info' => $coupon_data, 'products_arr' => $prod_id, 'result' => 1);
		} else {
			return array('result' => 0);
		}
	}

	public function check_coupon_products($cart_prod) {
		if($code = $this->session->get_data('coupon_code')) {
			$coupon_products = $this->db->select('*')
				->from('`' . self::DC_PROD . '` as D_PROD')
				->join('`' . self::D_COUP_CUST . '` as D_CUST', 'D_PROD.`' . self::ID_D_COUP . '`=D_CUST.`' . self::ID_D_COUP . '`')
				->where('D_CUST.`full_number`', $code)/*->where('D_CUST.`is_used`', 1)*/
				->get()->result_array();
			$count = 0;
			foreach($cart_prod as $key => $ms) {
				foreach ($coupon_products as $prod) {
					if ($ms['products']['ID'] == $prod[self::ID_PROD]) {
						$count += 1;
					}
				}
			}
			return $count;
		}
	}
}