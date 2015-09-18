<div align="center" class="product_view">
<div class="block" style="overflow:hidden;">
<div id="view_product_block">
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
		if(isset($prices))
		{
			echo $prices;
		}
		if(isset($attributes))
		{
			echo $attributes;
		}
		?>
		<div class="description"><?=$product['full_description']?></div>
	</td>
</tr>
</table>
</div>
</div>
</div>