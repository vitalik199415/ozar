<div>
<table cellspacing="0" cellpadding="4" border="1" width="70%" style="color:#FFFFFF;" align="left">
<tr>
	<td width="40%" valign="middle" align="left">
		Номер продажи :
	</td>
	<td valign="middle" align="left">
		<?=$sale_data['sales_number']?>
	</td>
</tr>
<tr>
	<td width="40%" valign="middle" align="left">
		Дата создания :
	</td>
	<td valign="middle" align="left">
		<?=$sale_data['create_date']?>
	</td>
</tr>
<tr>
	<td width="40%" valign="middle" align="left">
		Сумарное количество :
	</td>
	<td valign="middle" align="left">
		<?=$sale_data['total_qty']?>
	</td>
</tr>
<tr>
	<td width="40%" valign="middle" align="left">
		Сумма :
	</td>
	<td valign="middle" align="left">
		<?=$sale_data['total']?>
	</td>
</tr>
<tr>
	<td width="40%" valign="middle" align="left">
		Комментарий :
	</td>
	<td valign="middle" align="left">
		<?=$sale_data['comment']?>
	</td>
</tr>
</table>
</div>
<div style="clear:both; padding:5px 0 0 0;">
<div style="background:#CCCCCC; border:2px solid #999999;">
<?=$sales_pr_html?>
</div>
</div>