<?php
if(isset($data))
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
div{
text-align:left;
}
</style>
</head>
<body>
<div>У вас нове замовлення <b><?=$data['orders_number']?></b> з сайту <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b>.</div>

<?=$data['payment_html']?>
<?=$data['shipping_html']?>

<div style="margin:15px 0 0 0;">Опис замовлення :</div>
<div>Номер замовлення : <b><?=$data['orders_number']?></b></div>
<div>Кількість одиниць : <b><?=$data['total_qty']?></b></div>
<div>Сума : <b><?=$data['total']?></b></div>
<div><b>Примітка до замовлення :</b></div>
<div><?=$data['note']?></div>

<div style="margin:15px 0 0 0;">Продукти замовлення : </div>
<div style="margin:0 0 0 20px;">
	<table border="1" bordercolor = "#333333" bgcolor = "#EEEEEE" width="80%" cellpadding = "3" cellspacing = "0">
	<?php
	foreach($data['products'] as $ms)
	{
	?>
		<tr>
			<td align="left" valign="top">
				<div><a href="<?=$ms['products']['detail_url']?>" target="_blank"><?=$ms['products']['name']?></a></div>
				<div>Артикул : <b><?=$ms['products']['sku']?></b></div>
			</td>
			<td align="left" valign="middle">
				<div><div>Ціна :</div><?=$ms['products_prices']['price_name']?> <b><?=$ms['products_prices']['cart_price_rate_string']?></b></div>
				<div>Кількість : <b><?=$ms['cart']['qty']?></b></div>
				<div>Сума : <b><?=$ms['products_prices']['total_price_string']?></b></div>
				<?php
				if(isset($ms['products_attributes']) && count($ms['products_attributes'])>0)
				{
					?><div><?
					foreach($ms['products_attributes'] as $attr)
					{
					?>
						<div><?=$attr['a_name']?> : <?=$attr['o_name']?></div>
					<?php
					}
				?></div><?
				}
				?>
			</td>
		</tr>
	<?php
	}
	?>
	<tr>
		<td colspan="2" align="right"><?=$data['total']?></td>
	</tr>
	</table>
</div>
</body>
</html>
<?	
}
?>