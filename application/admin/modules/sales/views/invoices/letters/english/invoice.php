<?php
if(isset($invoice))
{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body{
font-family:Arial, Helvetica, sans-serif;
padding:0;
margin:0;
font-size:14px;
color:#333333;
background:#FFFFFF;
text-align:left;
}
</style>
</head>
<body>
<div>Уважаемый <b><?=$order_addresses['B']['name']?></b>.</div>
<div style="margin:10px 0 0 0;">Данное письмо является запросом на оплату(инвойс) заказа <b>#<?=$order['orders_number']?></b> оформленного на сайте <a href="http://<?=$user['domain']?>" target="_blank"><?=$user['domain']?></a>.</div>
<div style="margin:10px 0 0 0;">Номер инвойса : <b>#<?=$invoice['invoices_number']?></b></div>
<div style="margin:10px 0 0 0;">Номер заказа : <b>#<?=$order['orders_number']?></b></div>
<div style="margin:10px 0 0 0;">Метод оплаты : <b><?=$order['payment_method']?></b></div>
<div style="margin:10px 0 0 0;">Метод доставки : <b><?=$order['shipping_method']?></b></div>
<div style="margin:10px 0 0 0;">
Данные плательщика : <br>
Имя : <b><?=$order_addresses['B']['name']?></b>, Страна : <b><?=$order_addresses['B']['country']?></b>, Город : <b><?=$order_addresses['B']['city']?></b>, Индекс : <b><?=$order_addresses['B']['zip']?></b>, Адрес : <b><?=$order_addresses['B']['address']?></b>, Телефон : <b><?=$order_addresses['B']['telephone']?></b>, E-Mail : <b><?=$order_addresses['B']['address_email']?></b>
</div>
<div style="margin:10px 0 0 0;">
Данные получателя : <br>
Имя : <b><?=$order_addresses['B']['name']?></b>, Страна : <b><?=$order_addresses['B']['country']?></b>, Город : <b><?=$order_addresses['B']['city']?></b>, Индекс : <b><?=$order_addresses['B']['zip']?></b>, Адрес : <b><?=$order_addresses['B']['address']?></b>, Телефон : <b><?=$order_addresses['B']['telephone']?></b>, E-Mail : <b><?=$order_addresses['B']['address_email']?></b>
</div>
<div style="margin:10px 0 0 0;">
<b>Продукты заказа</b><br><br>
<table border="1" bordercolor = "#333333" bgcolor = "#EEEEEE" width = "80%" cellpadding = "3" cellspacing = "0" style="font-size:14px;">
	<?php
	foreach($order_products as $ms)
	{
	?>
	<tr>
		<td align="left" valign="top" width="30%">
			<div><?=$ms['name']?></div>
			<div>Артикул : <b><?=$ms['sku']?></b></div>
		</td>
		<td align="left" valign="middle" width="30%">
			<div><?=$ms['price_name']?> <b><?=$ms['price']*$order['currency_rate'].' '.$order['currency_name']?></b></div>
			<div>Количество : <b><?=$ms['qty']?></b></div>
			<div>Сумма : <b><?=$ms['total']*$order['currency_rate'].' '.$order['currency_name']?></b></div>
		</td>
		<td align="left" valign="middle" width="40%">
			<?php
			foreach($ms['attributes'] as $attr)
			{
			?>
				<div><?=$attr['attributes_name']?> : <?=$attr['attributes_options_name']?></div>
			<?php
			}
			?>
		</td>
	</tr>
	<?php
	}
	?>
</table>
</div>
<div style="margin:10px 0 0 0;">Предварительная сумма : <b><?=$order['subtotal']*$order['currency_rate'].' '.$order['currency_name']?></b>, Скидка : <b><?=$order['discount']?></b>, Сумма : <b><?=$order['total']*$order['currency_rate']-$order['discount'].' '.$order['currency_name']?></b></div>
<?php
if(trim($invoice['note']) != '')
{
?><div style="margin:10px 0 0 0;"><b>Примечание менеджера :</b> <br><?=$invoice['note']?></div><?
}
?>
<div style="margin:10px 0 0 0;">Просим Вас подтвердить данный инвойс кликнув по ссылке <a href="<?=$confirm_invoice_link?>" target="_blank">Инвойс подтверждаю</a>, которая находится ниже. Это проинформирует менеджера, что вы получили инвойс и готовы совершить оплату по предоставленному счету.</div>
<div align="center" style="margin:5px 0 0 0; text-align:center;"><a href="<?=$confirm_invoice_link?>" style="font-size:18px;" target="_blank">Инвойс подтверждаю</a></div>
<div style="margin:20px 0 0 0;">
<?=$payment_method_html?>
</div>
<div style="margin:25px 0 0 0;">
С уважением администрация <b><?=$user['domain']?></b>.
</div>
</body>
</html>
<?	
}
?>