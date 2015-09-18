<table cellspacing="0" cellpadding="0" width="100%" border="1" class="order_edit_products" style="color:#ffffff">
<?
$total = 0;
foreach($products as $ms)
{
	?>
	<tr>
		<td valign="middle" align="left">
			<div class="prod_name"><?=$ms['name']?></div>
			<div class="prod_sku">Артикул : <span><?=$ms['sku']?></span></div>
			<div class="prod_price"><?=$ms['price_name']?> : <span><?=number_format($ms['price']*$currency_rate, 2, ',', ' ').' '.$currency_name?></span></div>
		</td>
		
		<td valign="middle" class="cart_edit_attributes">
			<div>Количество : <input type="text" name="qty" value="<?=$ms['qty']?>" disabled="disabled" class="cart_edit_input"></div>
			<div>Сумма : <input type="text" name="price" value="<?=number_format($ms['price']*$ms['qty']*$currency_rate, 2, ',', ' ').' '.$currency_name?>" disabled="disabled" class="cart_edit_input"></div>
			<?php
			if(isset($products_attr[$ms['id_m_orders_products']]))
			{
				foreach($products_attr[$ms['id_m_orders_products']] as $attr)
				{
					?><div><?=$attr['attributes_name']?> : <?=$attr['attributes_options_name']?></div><?
				}
			}	
			?>
		</td>
	</tr>
	<?
	$total += $ms['price']*$ms['qty']*$currency_rate;
}
?>
<tr>
	<td colspan="2" align="right">Сумма : <?=number_format($total, 2, ',', ' ').' '.$currency_name?></td>
</tr>
</table>