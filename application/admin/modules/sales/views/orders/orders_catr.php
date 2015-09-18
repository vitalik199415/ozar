<?php
if(isset($products) && is_array($products))
{
?>
<div id="order_cart" align="center">
<table border="0" cellpadding="2" cellspacing="0" style="font-size:11px; color:#FFFFFF; border:#999999 3px solid;" width="100%" rules="all">
<thead>
	<tr>
		<td width="30"></td>
		<td width="130"><b>ID : SKU</b></td>
		<td><b>Название</b></td>
		<td width="110"><b>Цена</b></td>
		<td width="70"><b>Количество</b></td>
		<td width="200"><b>Атрибуты</b></td>
		<td width="100"><b>Сумма</b></td>
	</tr>
</thead>
<tbody>	
	<?php
	$total = 0;
	foreach($products as $ms)
	{
		?>
		<tr>
		<td valign="middle" align="center"><input type="checkbox" class="cart_products" name="cart_products[]" value="<?=$ms['rowid']?>"></td>
		<td valign="middle" align="left"><?=$ms['ID'].' : '.$ms['sku']?></td>
		<td valign="middle" align="left"><?=$ms['name']?></td>
		<td valign="middle" align="left"><?=number_format($ms['price'] * $currency['rate'], 2, ',', ' ').' '.$currency['name']?></td>
		<td valign="middle" align="center"><?=$ms['qty']?></td>
		<td valign="middle" align="left">
		<?php
			foreach($ms['attributes'] as $at_key => $at_ms)
			{
			?>
			<div><?=$at_ms['a_name'].' : '.$at_ms['o_name']?></div>
			<?php
			}
		?>
		</td>
		<td valign="middle" align="left"><?=number_format($ms['price'] * $ms['qty'] * $currency['rate'], 2, ',', ' ').' '.$currency['name']?></td>
		</tr>
		<?
		$total += $ms['price'] * $ms['qty'];
	}
	?>
	<tr>
		<td colspan="6" align="right" style="padding:10px 6px 10px 0;">Полная стоимость заказа</td>
		<td><b><?=number_format($total * $currency['rate'], 2, ',', ' ').' '.$currency['name']?></b></td>
	</tr>
</tbody>	
</table>
<div class="def_buttons" align="center" style="margin:5px 0 0 0;"><a href="<?=set_url('*/*/delete_cart_products')?>" id="delete_cart_items">Удалить выбраные</a></div>
</div>
<script>
	$('#order_cart').gbc_products_cart('init_cart');
</script>
<?php
}