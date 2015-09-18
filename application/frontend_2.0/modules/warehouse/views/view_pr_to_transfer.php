<div align="center" class="product_view">
<div class="block" style="overflow:hidden;">
<div id="view_create_transfer_pr_block">
<div style="margin:0 0 10px 0; text-align:left;"><a href="<?=set_url('*/warehouses_products/ajax_get_transfer_wh_pr_grid/wh_id/'.$wh_id)?>" class="back_to_wh_pr">К списку продуктов склада</a></div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td valign="top" width="1">
		<div class="product_images">
			<?php
			foreach($images as $key => $ms)
			{
			?>
			<div class="image_div">
				<a href="<?=$ms['bimage']?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$ms['timage']?>"></a>
			</div>
			<?php
			}
			?>
		</div>
	</td>
	<td valign="top" align="left">	
		<div class="name"><span><?=$product['name']?></span></div>
		<div class="sku">Артикул : <span><?=$product['sku']?></span></div>
		<input type="hidden" name="product_id" value="<?=$product['ID']?>">
		<?php
		$def_qty = 1;
		//$def_price = $current_price['price'];
		$def_text = 'Добавить';
		if(isset($qty))
		{
			$def_qty = $qty;
			$def_text = 'Редактировать';
		}
		if(isset($price))
		{
			$def_price = $price;
			$def_text = 'Редактировать';
		}
		?>
		<div class="select_qty">
			<div>Доступное количество : <?=form_input(array('name' => 'current_qty', 'value' => $current_qty, 'readonly' => NULL));?></div>
			<div style="margin:5px 0 0 0;">Количество : <?=form_input(array('name' => 'qty', 'value' => $def_qty));?></div>
			<div style="margin:5px 0 0 0;"><a href="<?=set_url('*/*/ajax_add_pr_to_transfer/wh_id/'.$wh_id.'/pr_id/'.$product['ID'])?>" id="to_transfer" style="font-size:18px;"><?=$def_text?></a></div>
			<div id="to_sale_status"></div>
		</div>
		<script>
			$("#view_create_transfer_pr_block").find("input[name='qty']").inputmask("integer", {allowMinus: false, rightAlignNumerics : false});
		</script>
		<div class="description"><?=$product['full_description']?></div>
	</td>
</tr>
</table>
<div style="margin:10px 0 0 0;text-align:left;"><a href="<?=set_url('*/warehouses_products/ajax_get_transfer_wh_pr_grid/wh_id/'.$wh_id)?>" class="back_to_wh_pr">К списку продуктов склада</a></div>
</div>
</div>