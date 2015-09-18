<?php
class Payment_methods extends AG_Controller
{
	public function ajax_get_payment_methods_form()
	{
		if($id = intval($this->input->post('payment_method_id')))
		{
			$this->mlangs->load_language_file('modules/orders_customers');
			$this->load->model('sales/mpayment_methods');
			if($form = $this->mpayment_methods->get_payment_method_description($id))
			{
				echo json_encode(array('status' => 1, 'html' => $form));
			}
			else
			{
				echo json_encode(array('status' => 0));
			}
		}
	}
	
	public function show_pb_ekvaring_status()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		if(isset($URI['invoice_number']) && isset($URI['order_number'])  && strlen($URI['invoice_number']) == 8  && strlen($URI['order_number']) == 8 && isset($URI['account']) && ($URI['account'] == 'pb' || $URI['account'] == 'other') && isset($URI['code']))
		{
			$this->mlangs->load_language_file('modules/invoices');
			$this->load->model('sales/mpayment_methods');
			$status = $this->mpayment_methods->check_pb_ekvaring_status($URI['invoice_number'], $URI['order_number'], $URI['account'], $URI['code']);
			if($status == 2)
			{
				$payment_method_html = "";
				$message = "<h1 align='center' style='color:#006600'>".$this->lang->line('invoice_pm_payment_success')."</h1>";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
			else if($status == 1)
			{
				$payment_method_html = "";
				$message = "<h1 align='center' style='color:#006600'>".$this->lang->line('invoice_pm_payment_wait_secure')."</h1>";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
			else if($status == 0)
			{
				$payment_method_html = "";
				$message = "<h1 align='center' style='color:#990000'>".$this->lang->line('invoice_pm_payment_wait_failure')."</h1>";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
			else if($status === FALSE) echo "<h1 align='center' style='color:#990000'>Wrong payment data!</h1>";
		}
		else
		{
			echo "<h1 align='center' style='color:#990000'>Wrong payment data!</h1>";
		}
	}
	
	public function check_pb_ekvaring()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		if(isset($URI['invoice_number']) && isset($URI['order_number'])  && strlen($URI['invoice_number']) == 8  && strlen($URI['order_number']) == 8 && isset($URI['account']) && ($URI['account'] == 'pb' || $URI['account'] == 'other') && isset($URI['code']))
		{
			$this->load->model('sales/mpayment_methods');
			$this->mpayment_methods->check_pb_ekvaring_data($URI['invoice_number'], $URI['order_number'], $URI['account'], $URI['code']);
		}
	}
}
?>