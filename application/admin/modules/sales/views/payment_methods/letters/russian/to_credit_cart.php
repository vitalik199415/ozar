<?php
if(isset($pm_settings))
{
?>
<div style="margin:15px 0 0 0;">
<b>Данные для оплаты</b>
</div>
<div style="margin:15px 0 0 0;">
Имя получателя : <b><?=$pm_settings['fields_name']['value']?></b><br>
Номер банковской карты : <b><?=$pm_settings['fields_credit_cart_number']['value']?></b><br>
Назначение платежа : <b>Invoice:#<?=$invoice['invoices_number']?>, Order:#<?=$order['orders_number']?></b>
<div>
<?php
}
?>