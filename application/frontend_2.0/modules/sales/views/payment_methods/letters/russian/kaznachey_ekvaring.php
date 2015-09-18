<?php
$this->load->library('Kaznachey');
if(isset($pm_settings))
{

$merchant_id = $pm_settings['fields_merchant_id']['value'];
$signature = $pm_settings['fields_signature']['value'];

$data['id'] = $merchant_id;
$data['sig'] = $signature;

$this->kaznachey->_init($data);
echo var_dump($this->kaznachey->system());
/*
$url = "https://www.liqpay.com/?do=clickNbuy";
$method = 'card';
$order_number = "Invoice:#".$invoice['invoices_number'].", Order:#".$order['orders_number'];

$sum = $order['total']*$order['currency_rate']-$order['discount'];
$amount_pb = round($sum/0.985, 2);
$amount_other = round($sum/0.965, 2);

	$xml_pb = "<request>
		<version>1.2</version>
		<result_url>http://".$_SERVER['HTTP_HOST']."/ajax/sales/payment_methods/show_pb_ekvaring_status/invoice_number/".$invoice['invoices_number']."/order_number/".$order['orders_number']."/account/pb/code/".md5($invoice['id_m_orders'].'-'.$invoice['id_m_orders_invoices'].'-'.$invoice['create_date'])."</result_url>
		<server_url>http://".$_SERVER['HTTP_HOST']."/ajax/sales/payment_methods/check_pb_ekvaring/invoice_number/".$invoice['invoices_number']."/order_number/".$order['orders_number']."/account/pb/code/".md5($invoice['id_m_orders'].'-'.$invoice['id_m_orders_invoices'].'-'.$invoice['create_date'])."</server_url>
		<merchant_id>".$merchant_id."</merchant_id>
		<order_id>".$order_number."</order_id>
		<amount>".$amount_pb."</amount>
		<currency>".$currency['code']."</currency>
		<description></description>
		<default_phone>".$order_addresses['B']['telephone']."</default_phone>
		<pay_way>".$method."</pay_way>
		</request>
		";
	
	$xml_other = "<request>
		<version>1.2</version>
		<result_url>http://".$_SERVER['HTTP_HOST']."/ajax/sales/payment_methods/show_pb_ekvaring_status/invoice_number/".$invoice['invoices_number']."/order_number/".$order['orders_number']."/account/other/code/".md5($invoice['id_m_orders'].'-'.$invoice['id_m_orders_invoices'].'-'.$invoice['create_date'])."</result_url>
		<server_url>http://".$_SERVER['HTTP_HOST']."/ajax/sales/payment_methods/check_pb_ekvaring/invoice_number/".$invoice['invoices_number']."/order_number/".$order['orders_number']."/account/other/code/".md5($invoice['id_m_orders'].'-'.$invoice['id_m_orders_invoices'].'-'.$invoice['create_date'])."</server_url>
		<merchant_id>".$merchant_id."</merchant_id>
		<order_id>".$order_number."</order_id>
		<amount>".$amount_other."</amount>
		<currency>".$currency['code']."</currency>
		<description></description>
		<default_phone>".$order_addresses['B']['telephone']."</default_phone>
		<pay_way>".$method."</pay_way>
		</request>
		";
	
	$xml_encoded_pb = base64_encode($xml_pb); 
	$lqsignature_pb = base64_encode(sha1($signature.$xml_pb.$signature, 1));
	
	$xml_encoded_other = base64_encode($xml_other); 
	$lqsignature_other = base64_encode(sha1($signature.$xml_other.$signature, 1));
}*/
}
?>