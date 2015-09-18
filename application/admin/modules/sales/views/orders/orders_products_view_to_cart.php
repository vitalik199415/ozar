<div align="center" class="product_view">
<div class="block" style="overflow:hidden;">
<div id="products_to_cart">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td valign="top" width="1">
		<div class="product_images">
			<?php
			foreach($img as $key => $ms)
			{
			?>
			<div class="image_div">
				<a href="<?=$ms['bimage']?>" class="highslide" onclick="return hs.expand(this)">999<img src="<?=$ms['timage']?>"></a>
			</div>
			<?php
			}
			?>
		</div>
	</td>
	<td valign="top" align="left">	
		<input type="hidden" name="id" value="<?=$products['ID']?>">
		<div class="sku">Артикул : 888<span class="value"><?=$products['sku']?></span></div>
		<div class="name">Название : <span class="value"><?=$products['name']?></span></div>
		<script>
		var price_attributes = {};
		</script>
		<?php
		if(isset($price))
		{
			$i = TRUE;
			foreach($price as $key => $ms)
			{
			
				if($ms['special_price'])
				{
					?>
					<div class="price"><input type="radio" name="price" <?php if($i) echo 'checked="checked"';?> value="<?=$ms['ID_PRICE']?>"><?php if($ms['price_name'] != '') echo $ms['price_name']; else echo "Цена";?> <span><s><?=number_format($ms['price'], 2, ',', ' ')?></s></span>&nbsp;<span style="color:#FFFFFF"><?=number_format($ms['special_price'], 2, ',', ' ').' '.$ms['currency_name']?></span></div>
					<div style="font-size:11px;"><?=$ms['price_description']?></div>
					<?
				}
				else
				{
					?>
					<div class="price"><input type="radio" name="price" <?php if($i) echo 'checked="checked"';?> value="<?=$ms['ID_PRICE']?>"><?php if($ms['price_name'] != '') echo $ms['price_name']; else echo "Цена";?> <span style="color:#FFFFFF"><?=number_format($ms['price'], 2, ',', ' ').' '.$ms['currency_name']?></span></div>
					<div style="font-size:11px;"><?=$ms['price_description']?></div>
					<?
				}
				?>		
				<script>
					price_attributes[<?=$ms['ID_PRICE']?>] = {};
					price_attributes[<?=$ms['ID_PRICE']?>]['show_attributes'] = '<?=$ms['show_attributes']?>';
				<?php
					if($ms['show_attributes'] == 2)
					{
					?>
						price_attributes[<?=$ms['ID_PRICE']?>]['id_attributes'] = {};
					<?php
						$ar = $ms['id_attributes'];
						foreach(explode(',', $ar) as $ms1)
						{
							if($ms != '')
							{
							?>
								price_attributes[<?=$ms['ID_PRICE']?>]['id_attributes'][<?=$ms1?>] = '<?=$ms1?>';
							<?php
							}
						}
					}
				?>
				</script>
				<?php
				if($i)
				{	
					$show_attributes = $ms['show_attributes'];
					$id_attributes = explode(',', $ms['id_attributes']);
				}
				$i = FALSE;
			}
		}
		?>
		
		<?php
			if(isset($attributes) && is_array($attributes))
			{
				foreach($attributes as $ms)
				{
					$select_array = array();
					foreach($ms as $op)
					{
						$select_text = $op['a_name'];
						$select_name = 'attributes['.$op['ID'].']';
						$select_id = $op['ID'];
						$select_array[$op['ID_OP']] = $op['o_name'];
					}
					if(($show_attributes == 0) || ($show_attributes == 2 && !in_array($select_id, $id_attributes)))
					{
						$style = 'style="display:none"';
					}
					else if(($show_attributes == 1) || ($show_attributes == 2 && in_array($select_id, $id_attributes)))
					{
						$style = 'style="display:block"';
					}
					echo '<div class="name attributes_select" '.$style.' rel="'.$select_id.'">'.$select_text.' : '.form_dropdown($select_name, $select_array, '','rel="'.$select_id.'"').'</div>';
				}
			}
		?>
		<div class="def_buttons name">
			Количество : <?=form_input(array('name' => 'qty', 'value' => '1'));?> <a href="<?=setUrl('*/*/add_product_to_catr')?>" id="to_cart">В Корзину</a>
		</div>
		<div class="description"><?=$products['full_description']?></div>
		
		<div>
			
		</div>
	</td>
</tr>
</table>
</div>
</div>
<script language="javascript">
	$('#products_to_cart').gbc_products_cart('init_add_product', {price_attributes : price_attributes});
</script>