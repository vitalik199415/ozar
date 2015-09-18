<?php
class Discount_coupons extends AG_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->mlangs->load_language_file('modules/products');
	}
	
	public function ajax_activate_code()
	{
		$code = '';
		if(isset($_POST['code'])) {
			$code = $_POST['code'];
		}
			if($code != '')
			{
				$this->load->model('sales/mdiscount_coupons');

				$data = $this->mdiscount_coupons->use_code($code);
				if ($data['success'] == TRUE) {
					echo json_encode(array('success' => 1, 'site_messages' => $data['message']));
				} else {
					echo json_encode(array('success' => 0, 'site_messages' => $data['message']));
				}
			}
			else
			{
				echo json_encode(array('success' => 0, 'site_messages' => '<p>Server error!</p>'));
			}
	}

	public function view_coupon()
	{
		$code = '';
		if(isset($_POST['code']) && trim($_POST['code']) != '') {
			$code = $_POST['code'];
			$this->load->model('catalogue/mproducts');
			$this->load->model('sales/mdiscount_coupons');
			$coupon_info = $this->mdiscount_coupons->get_coupon_info($code);
			$html = '';
			if($coupon_info['result'] == 1) {
				$products_arr = $this->mproducts->get_products_array_by_id($coupon_info['products_arr']);
				$html .= $this->load->view('sales/discount_coupons/discount_coupons_info', array('C_info' => $coupon_info['coupon_info']), TRUE);
				if($products_arr['result'] == 1) {
					$PRS_array = array('products' => $products_arr['products']);
					$html .= $this->load->view('catalogue/products/products_short', array('PRS_array' => $PRS_array), TRUE);

				} else {
					$html .= "<div>".$this->lang->line('')."</div>";
				}
				echo json_encode(array('success' => 1, 'html' => $html));
			} else {
				echo json_encode(array('success' => 0, 'mess' => $this->lang->line('d_c_error')));
			}

		} else {
			$this->template->add_js('jquery.gbc_discount_coupons', 'modules_js/sales');
			$this->template->add_view_to_template('center_block', 'sales/discount_coupons/discount_coupons_form', array());
			$this->template->add_view_to_template('center_block', 'sales/discount_coupons/discount_coupons_form_js', array());
		}
	}
}