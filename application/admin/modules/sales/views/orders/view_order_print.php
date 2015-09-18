<?php
if(isset($order))
{
	?>
	<div align="center">
	<div style="width:850px; text-align:left; font-size:18px; color:#000000; margin:0 0 30px 0; background:#FFFFFF; padding:5px;" id="print_order" class="print_block">
		<div style="margin:10px 0;">Заказ <b><?=$order['orders_number']?></b> создан <?=$order['create_date']?>, статус <b><?=$order['orders_state']?></b></div>
		<div style="margin:10px 0;">Предварительная сумма <b><?=$order['subtotal']*$order['currency_rate'].' '.$order['currency_name']?></b> скидка <b><?=$order['discount'].' '.$order['currency_name']?></b>, Сумма <b><?=$order['total']*$order['currency_rate']-$order['discount'].' '.$order['currency_name']?></b></div>
		<div style="margin:10px 0;">Метод оплаты <b><?=$order['payment_method_name']?></b>, Метод доставки <b><?=$order['shipping_method_name']?></b></div>
		<div style="margin:10px 0;"><b>Адрес плательщика</b><br>
		Имя : <b><?=$addresses['B']['name']?></b>, Страна : <b><?=$addresses['B']['country']?></b>, Город : <b><?=$addresses['B']['city']?></b>, Индекс : <b><?=$addresses['B']['zip']?></b>, Адрес : <b><?=$addresses['B']['address']?></b>, Телефон : <b><?=$addresses['B']['telephone']?></b>, E-Mail : <b><?=$addresses['B']['address_email']?></b>
		</div>
		<div style="margin:10px 0;"><b>Адрес получателя</b><br>
		Имя : <b><?=$addresses['S']['name']?></b>, Страна : <b><?=$addresses['S']['country']?></b>, Город : <b><?=$addresses['S']['city']?></b>, Индекс : <b><?=$addresses['S']['zip']?></b>, Адрес : <b><?=$addresses['S']['address']?></b>, Телефон : <b><?=$addresses['S']['telephone']?></b>, E-Mail : <b><?=$addresses['S']['address_email']?></b>
		</div>
		<?
		if($order['note'] != '')
		{
		?>
		<div style="margin:10px 0;"><b>Примечание к заказу</b><br>
			<?=$order['note']?>
		</div>
		<?php
		}
		?>
		<table width="100%" cellpadding="2" cellspacing="0" border="1" bordercolor = "#333333">
			<tr>
				<td width="9%"><b>Артикул</b></td><td width="20%"><b>Название</b></td><td width="15%"><b>Н. цены</b></td><td width="11%"><b>Цена</b></td><td width="4%"><b>К-во</b></td><td width="11%"><b>Сумма</b></td><td><b>Атрибуты</b></td>
			</tr>	
			<?php
			foreach($products as $ms)
			{
				?>
				<tr>
				<td valing="middle"><?=$ms['sku']?></td><td valing="middle"><?=$ms['name']?></td><td valing="middle"><?=$ms['price_name']?></td><td valing="middle"><?=$ms['price']*$order['currency_rate'].' '.$order['currency_name']?></td><td valing="middle"><?=$ms['qty_str']?></td><td valing="middle"><?=$ms['total']*$order['currency_rate'].' '.$order['currency_name']?></td>
				<td valing="middle">
				<?php
				foreach($ms['attributes'] as $at)
				{
					echo $at['attributes_name'].' : '.$at['attributes_options_name'].'<br>';
				}
				?>
				</td>
				</tr>
				<?
			}
			?>
		</table>
	</div>
	<div><a href="#" id="href_print_order" style="background:#333333; border:2px solid #000000; padding:3px 5px;">Напечатать заказ</a></div>
	<br><br>
	<script>
		$('#href_print_order').click(function()
		{
			$('#print_order').printElement();
			return false;
		});
	</script>
	</div>
	<?
}
?>