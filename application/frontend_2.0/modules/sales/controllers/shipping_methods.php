<?php
class Shipping_methods extends AG_Controller
{
	public function ajax_get_shipping_methods_form()
	{
		if($id = intval($this->input->post('shipping_method_id')))
		{
			$this->mlangs->load_language_file('modules/orders_customers');
			$this->load->model('sales/mshipping_methods');

			if($data = $this->mshipping_methods->get_form_shipping_method_data($id))
			{
				$this->load->model('sales/msales_settings');
				$data['sales_settings'] = $this->msales_settings->get_sales_settings();

				echo json_encode(array('status' => 1, 'html' =>
				$this->load->view('sales/orders/shipping_methods/default_form', array('order_data' => $data), TRUE).
				$this->load->view('sales/orders/shipping_methods/default_form_js', array('order_data' => $data), TRUE)
				));
			}
			else
			{
				echo json_encode(array('status' => 0));
			}
		}
	}
}
?>