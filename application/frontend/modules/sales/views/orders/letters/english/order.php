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
<div>Hello <b><?=$data['name']?></b>.</div>
<div style="margin:15px 0 0 0;">You place your order on the site <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b>.</div>
<div style="margin:15px 0 0 0;">To confirm your order, please go to this link : <a href="<?=$data['link']?>" target="_blank"><?=$data['link']?></a><div>
<div>If successful confirmation manager receives notice of a new order and will contact you shortly.</div>

<?=@$data['payment_html']?>
<?=@$data['shipping_html']?>

<div style="margin:15px 0 0 0;">Your order :</div>
<div>Order number : <b><?=$data['orders_number']?></b></div>
<div>Number of units : <b><?=$data['total_qty']?></b></div>
<div>Total : <b><?=$data['total']?></b></div>
<div><b>Note to the order :</b></div>
<div><?=$data['note']?></div>
<div style="margin:15px 0 0 20px;">Order products : </div>
<div style="margin:0 0 0 20px;">
	<table border="1" bordercolor = "#333333" bgcolor = "#EEEEEE" width="80%" cellpadding = "3" cellspacing = "0">
	<?php
	foreach($data['products'] as $ms)
	{
	?>
		<tr>
			<td align="left">
				<div><a href="<?=$ms['products']['detail_url']?>" target="_blank"><?=$ms['products']['name']?></a></div>
				<div>SKU : <b><?=$ms['products']['sku']?></b></div>
			</td>
			<td align="left" valign="middle">
				<div><div>Price :</div><?=$ms['products_prices']['price_name']?><b><?=$ms['products_prices']['cart_price_rate_string']?></b></div>
				<div>QTY : <b><?=$ms['cart']['qty']?></b></div>
				<div>Total : <b><?=$ms['products_prices']['total_price_string']?></b></div>
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

<div style="margin:15px 0 0 0;">
Sincerely administration <b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>