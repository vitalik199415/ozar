<?php
if(isset($prod_array))
{
?>
<div align="center">
<a href="#" id="print_products_with_photo_a" style="font-size:18px;">Печать с фото</a>
<div style="width:900px; background:#FFFFFF; color:#000000; font-size:18px; padding:5px; margin:10px 0 0 0;" class="print_block" id="print_products_with_photo">
	<div style="font-size:20px; margin:10px 0;" align="center" class="18pt">Заказ #<?=$prod_array['0']['orders_number']?></div>
	<table width="900" cellspacing="0" cellpadding="2" border="1">
	<?php
	foreach($prod_array as $ms)
	{
		?>
		<tr>
			<td width="65%" valign="top" align="left">
				<?php
				if(isset($ms['timage']) && $ms['timage'])
				{
				?>
				<div style="float:left; padding:0 5px 0 0;"><img src="<?=$ms['timage']?>" style="max-width:250px;max-height:300px;"></div>
				<?php
				}
				?>
				<div><?=$ms['name']?></div>
				<div>Артикул : <b><?=$ms['sku']?></b></div>
				<div style="clear:both;"></div>
			</td>
			<td width="35%" valign="middle" align="left">
				<div><?=$ms['price']?></div>
				<div>Количество : <?=$ms['qty']?></div>
				<div>Сумма : <?=$ms['total']?></div>
				<div><?=$ms['attributes']?></div>
			</td>
		</tr>
		<?
	}
	?>
	</table>
</div>
</div>
<script>
	$('#print_products_with_photo_a').click(function()
	{
		$('#print_products_with_photo').printElement();
		return false;
	});
</script>
<?
}
?>