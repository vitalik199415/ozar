<div align="center" class="product_view">
<div class="block" style="overflow:hidden;">
<div id="view_transfer_pr_block">
<div style="margin:0 0 10px 0; text-align:left;"><a href="<?=set_url('*/warehouses_logs/ajax_view_transfer/wh_id/'.$wh_id.'/log_id/'.$log_id)?>" class="back_to_wh_transfer">К списку продуктов переноса</a></div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td valign="top" width="1">
		<div class="product_images">
			<?php
			foreach($product['images'] as $key => $ms)
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
		<div class="name"><span><?=$product['product']['name']?></span></div>
		<div class="sku">Артикул : <span><?=$product['product']['sku']?></span></div>
		<input type="hidden" name="product_id" value="<?=$product['product']['ID']?>">
		<?php
		//echo var_dump($prices_array);
		/*
		if(isset($prices))
		{
			echo $prices;
		}
		if(isset($attributes))
		{
			echo $attributes;
		}
		*/
		?>
		<div class="description"><?=$product['product']['full_description']?></div>
	</td>
</tr>
</table>
<div style="margin:0 0 10px 0; text-align:left;"><a href="<?=set_url('*/warehouses_logs/ajax_view_transfer/wh_id/'.$wh_id.'/log_id/'.$log_id)?>" class="back_to_wh_transfer">К списку продуктов переноса</a></div>
</div>
</div>