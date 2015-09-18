<?php
if(isset($shipping))
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
<div>Уважаемый <b><?=$order_addresses['S']['name']?></b>.</div>
<div style="margin:10px 0 0 0;">Заказ <b>#<?=$order['orders_number']?></b> оформленный на сайте <a href="http://<?=$user['domain']?>" target="_blank"><?=$user['domain']?></a> был отправлен по указанным ниже реквизитам.</div>
<div style="margin:10px 0 0 0;">Номер отправки : <b>#<?=$shipping['shippings_number']?></b></div>
<div style="margin:10px 0 0 0;">Номер заказа : <b>#<?=$order['orders_number']?></b></div>
<div style="margin:10px 0 0 0;">Метод доставки : <b><?=$order['shipping_method']?></b></div>
<div style="margin:10px 0 0 0;">
Данные получателя : <br>
Имя : <b><?=$order_addresses['B']['name']?></b>, Страна : <b><?=$order_addresses['B']['country']?></b>, Город : <b><?=$order_addresses['B']['city']?></b>, Индекс : <b><?=$order_addresses['B']['zip']?></b>, Адрес : <b><?=$order_addresses['B']['address']?></b>, Телефон : <b><?=$order_addresses['B']['telephone']?></b>, E-Mail : <b><?=$order_addresses['B']['address_email']?></b>
</div>
<?php
if(trim($shipping['note']) != '')
{
?><div style="margin:10px 0 0 0;"><b>Примечание менеджера :</b> <br><?=$shipping['note']?></div><?
}
?>
<div style="margin:25px 0 0 0;">
С уважением администрация <b><?=$user['domain']?></b>.
</div>
</body>
</html>
<?	
}
?>