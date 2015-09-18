<?php
if(isset($pm_settings))
{
?>
<div style="margin:15px 0 0 0;">
<b>Квитанция для оплаты</b>
</div>
<div align="center" style="margin:15px 0 0 0;text-align:center;">
	<div style="border:solid 2px #000; width:680px; margin:10px 0 0 0">
		<table width="680" style="margin:10px 0 0 0;border-bottom:solid 1px black" cellpadding="1" cellspacing="0">
			<tr><td></td></tr>
		</table>
		<table height="120" width="680" style="margin:0;border-bottom:solid 1px #000;" cellpadding="0" cellspacing="0">
			<tr style="font-size:14px;">
				<td width="30%" style="border-bottom:solid 1px #000" align="right"><b>Сумма:&nbsp;</b></td>
				<td style="border-left:solid 1px #000;border-bottom:solid 1px #000"><b>&nbsp;<?=$order['total']*$order['currency_rate']-$order['discount'].' '.$order['currency_name']?></b></td>
			</tr>
			<tr style="font-size:14px">
				<td align="right"><b>Плательщик:&nbsp;</b></td>
				<td style="border-left:solid 1px #000"><b>&nbsp;<?=$order_addresses['B']['name']?></b></td>
			</tr>
			<tr>
				<td style="border-right:solid 1px #000;border-top:solid 1px #000" align="center" valign="middle"><b>Место проживания</b></td>
				<td style="border-top:solid 1px #000; font-size:13px" height="50" valign="middle">
					&nbsp;<?=$order_addresses['B']['country']?>, <?=$order_addresses['B']['city']?>, <?=$order_addresses['B']['address']?>
				</td>
			</tr>
		</table>
		<table width="680" style="margin:0;border-bottom:solid 1px black" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20%" style="border-right:solid 1px #000;" align="center" valign="middle"><b>Получатель:</b></td>
				<td style="line-height:30px; font-size:13px">
					<div style="height:25px;border-bottom:solid 1px #000">&nbsp;<?=$pm_settings['fields_company_name']['value']?></div>
					<div style="height:25px;">&nbsp;<?=$pm_settings['fields_bank_name']['value']?></div>
				</td>
			</tr>
		</table>
		<table width="680" style="margin:0;border-bottom:solid 1px black" cellpadding="0" cellspacing="0">
			<tr align="center">
				<td style="border-right:solid 1px #000; line-height:30px">
					<div style="height:25px;border-bottom:solid 1px #000">&nbsp;<b>Код ЕДРПОУ:</b></div>
					<div style="height:25px;">&nbsp;<?=$pm_settings['fields_ERDPOY']['value']?></div>
				</td>
				<td style="border-right:solid 1px #000; line-height:30px">
					<div style="height:25px;border-bottom:solid 1px #000">&nbsp;<b>Расчетный счет:</b></div>
					<div style="height:25px;">&nbsp;<?=$pm_settings['fields_payment_account']['value']?></div>
				</td>
				<td style="line-height:30px">
					<div style="height:25px;border-bottom:solid 1px #000">&nbsp;<b>МФО банка:</b></div>
					<div style="height:25px;">&nbsp;<?=$pm_settings['fields_bank_code']['value']?></div>
				</td>
			</tr>
		</table>
		<table width="680" style="margin:0;border-bottom:solid 1px black" cellpadding="0" cellspacing="0">
			<tr align="center">
				<td width="30%" style="border-right:solid 1px #000;font-weight:bold;" align="center" valign="middle">Предназначение платежа:</td>
				<td valign="middle">
					<div style="font-size:14px;" align="left">
						<div>&nbsp;<b>Invoice:#<?=$invoice['invoices_number']?>, Order:#<?=$order['orders_number']?></b></div>
					</div>
				</td>
			</tr>
		</table>
		<table width="680" height="50" style="margin:0;" cellpadding="0" cellspacing="0">
			<tr valign="middle" align="center">
				<td width="25%" style="border-right:solid 1px #000; font-weight:bold; line-height:30px">
					Примечание:
				</td>
				<td width="75%" style="line-height:30px">
					
				</td>
			</tr>
		</table>
	</div>
</div>
<div align="left" style="margin:15px 0 0 0;">
Просим Вас обратить внимание на поле <b>"Предназначение платежа"</b> при оплате. Очень важно не совершить ошибку в номерах заказа и инвойса во избежание дальнейших трудностей индификации оплаты.
</div>
<div align="center" style="margin:15px 0 0 0;text-align:center;">
<a href="javascript:window.print();" style="font-size:18px;" class="no_print">Печать квитанции</a>
</div>
<?
}
?>