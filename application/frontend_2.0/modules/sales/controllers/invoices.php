<?php
class Invoices extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function confirm_invoice()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		$this->mlangs->load_language_file('modules/invoices');
		if(isset($URI['id']) && isset($URI['code']) && strlen($URI['id']) == 8)
		{
			$this->load->model('sales/minvoices');
			$result = $this->minvoices->confirm_invoice($URI['id'], $URI['code']);
			if($result == 3)
			{ 
				$payment_method_html = $this->minvoices->get_payment_method_html($URI['id']);
				$message = "<h1 align='center' style='color:#006600' class='no_print'>".$this->lang->line('invoice_success_confirm_invoice')."</h1>";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
			else if($result == 2)
			{
				$payment_method_html = $this->minvoices->get_payment_method_html($URI['id']);
				$message = "<h1 align='center' style='color:#006600' class='no_print'>".$this->lang->line('invoice_success_already_confirmed')."</h1>";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
			else if($result == 1)
			{
				$payment_method_html = "";
				$message = "<h1 align='center' style='color:#990000'>".$this->lang->line('invoice_error_wrong_confirm_data')."</h1>";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
			else
			{
				$payment_method_html = "";
				$message = "<h1 align='center' style='color:#990000'>".$this->lang->line('invoice_error_wrong_confirm_data')."</h1>";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
		}
		else
		{
			echo "<h1 align='center' style='color:#990000'>Wrong invoice confirmation data!</h1>";
		}
	}
	
	public function show_invoice_payment_method_data()
	{
		$URI = $this->uri->ruri_to_assoc(3);
		$this->mlangs->load_language_file('modules/invoices');
		if(isset($URI['id']) && isset($URI['code']) && strlen($URI['id']) == 8)
		{
			$this->load->model('sales/minvoices');
			$result = $this->minvoices->confirm_invoice($URI['id'], $URI['code']);
			if($result > 1)
			{ 
				$payment_method_html = $this->minvoices->get_payment_method_html($URI['id']);
				$message = "";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
			else
			{
				$payment_method_html = "";
				$message = "<h1 align='center' style='color:#990000'>Wrong invoice data!</h1>";
				echo $this->load->view('sales/payment_methods/letters/template', array('payment_method_html' => $payment_method_html, 'message' => $message), TRUE);
			}
		}
		else
		{
			echo "<h1 align='center' style='color:#990000'>Wrong invoice data!</h1>";
		}
	}
}
?>