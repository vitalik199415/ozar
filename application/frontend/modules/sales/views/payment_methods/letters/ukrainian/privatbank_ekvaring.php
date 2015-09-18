<?php
if(isset($pm_settings))
{
$merchant_id = $pm_settings['fields_merchant_id']['value'];
$signature = $pm_settings['fields_signature']['value'];

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
}
?>
<div align="center" style="margin:15px 0 0 0;"><img src="/design/logo_oplatu.png" /></div>
<div style="margin:15px 0 0 0;">
При оплаті за допомогою карти або рахунку "ПриватБанк" комісія дорівнює 1.5% від суми інвойсу, з урахуванням комісїї сума до оплати <b><?=$amount_pb?> <?=$order['currency_name']?></b>
</div>
<div style="margin:15px 0 0 0;" align="center">
<form action='<?=$url?>' method='POST'>
<input type='hidden' name='operation_xml' value='<?=$xml_encoded_pb?>' />
<input type='hidden' name='signature' value='<?=$lqsignature_pb?>' />
<input type='submit' value='Оплатить с помощью карты или счета "ПриватБанк"'/>
</form>
</div>

<div style="margin:30px 0 0 0;">
При оплаті за допомогою карти або рахунків інших банків комісія дорівнює 3.5% від суми інвойсу, з урахуванням комісїї сума до оплати <b><?=$amount_other?> <?=$order['currency_name']?></b>
</div>
<div style="margin:15px 0 0 0;" align="center">
<form action='<?=$url?>' method='POST'>
<input type='hidden' name='operation_xml' value='<?=$xml_encoded_other?>' />
<input type='hidden' name='signature' value='<?=$lqsignature_other?>' />
<input type='submit' value='Оплатить с помощью карты или счета другого банка'/>
</form>
<div>