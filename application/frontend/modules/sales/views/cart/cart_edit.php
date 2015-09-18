<div class="form_block cart_edit_block" id="<?=$cart_edit_block_id?>">

<div class="form_message_block cart_edit_message_block" id="cart_edit_message_block">
	<div class="error_message">
		<div></div>
	</div>
	<div class="success_message">
		<div></div>
	</div>
</div>
<?php
if(isset($cart_edit['cart_products']) && count($cart_edit['cart_products'])>0)
{
?>
	<div class="form_label cart_edit_label"><span><?=$this->lang->line('cart')?></span></div>
	<div class="form_item_block total_block cart_edit_total">
	<div class="form_item_inside_block">
		<span class="field_label"><?=$this->lang->line('cart_total_price');?> : </span><span class="field_value"><?=$cart_edit['cart_total_price']?></span> <span class="form_button"><a href="#" class="cart_edit_checkout"><span><?=$this->lang->line('cart_create_order');?></span></a></span>
	</div>
	</div>
		<?php
		foreach($cart_edit['cart_products'] as $key => $ms)
		{
		?>
		<div class="form_item_block cart_product_block">
		<div class="form_item_inside_block">
			<table cellspacing="0" cellpadding="0" class="form_table">
				<tr>
					<td valign="top" align="left" class="product_data_td">
						<input type="hidden" name="rowid" value="<?=$key?>"> 
						<div class="prod_name"><span class="field_label"><a href="<?=$ms['products']['detail_url']?>" target="_blank"><span><?=$ms['products']['name']?></span></a></span></div>
						<?php
							if(isset($ms['products']['timage'])) { ?><div class="prod_img"><a href="<?=$ms['products']['detail_url']?>" target="_blank"><img src="<?=$ms['products']['timage']?>"></a></div><? }
						?>
						<div class="prod_sku"><span class="field_label"><?=$this->lang->line('products_sku')?> : </span><span class="field_value"><?=$ms['products']['sku']?></span></div>
						
						<div class="prices_block">
						<div class="field_label"><span><?=$this->lang->line('products_price')?></span></div>
						<?php
							if($ms['products_prices']['special_price_rate'])
							{
								?>
									<div class="price_block"><span class="field_label"><?=$ms['products_prices']['price_name']?></span><span class="field_value"><s><?=$ms['products_prices']['price_rate_string']?></s></span> <span class="field_value"><?=$ms['products_prices']['special_price_rate_string']?> <?=$ms['products_prices']['currency_name']?></span></div>
								<?
							}
							else
							{
								?><div class="price_block"><span class="field_label"><?=$ms['products_prices']['price_name']?></span><span class="field_value"><?=$ms['products_prices']['price_rate_string']?> <?=$ms['products_prices']['currency_name']?></span></div><?
							}	
						?>
						</div>
						<div class="attributes_block">
						<?php
						foreach($ms['products_attributes'] as $at)
						{
							?><div><span class="field_label"><?=$at['a_name']?> : </span><span class="field_value"><?=$at['o_name']?></span></div><?
						}
						?>
						</div>
					</td>
					<td valign="middle" width="30%" class="product_qty_td">
						<div class="form_field"><label><?=$this->lang->line('products_qty')?> :</label><input type="text" name="qty" value="<?=$ms['cart']['qty']?>" class="product_qty cart_edit_input"><div class="clear_both"></div></div>
						<div class="form_button"><a href="#" class="cart_edit_edit_item"><span><?=$this->lang->line('cart_edit_item_qty')?></span></a></div>
					</td>
					<td valign="middle" width="30%" class="product_total_td">
						<div class="form_field"><label><?=$this->lang->line('cart_total_price')?> :</label><input type="text" name="price" value="<?=$ms['products_prices']['total_price_string'].' '.$ms['products_prices']['currency_name']?>" readonly class="product_total cart_edit_input"><div class="clear_both"></div></div>
						<div class="form_button"><a href="#" class="cart_edit_delete_item"><span><?=$this->lang->line('cart_delete_item')?></span></a></div>
					</td>
				</tr>
			</table>
		</div>
		</div>
		<?php
		}
		?>
		<div class="form_item_block total_block cart_edit_total">
		<div class="form_item_inside_block">
			<span class="field_label"><?=$this->lang->line('cart_total_price');?> : </span><span class="field_value"><?=$cart_edit['cart_total_price']?></span> <span class="form_button"><a href="#" class="cart_edit_checkout"><span><?=$this->lang->line('cart_create_order');?></span></a></span>
		<div>
		</div>	
<?
}
else
{
?><div class="form_label cart_edit_label"><span><?=$this->lang->line('cart_is_empty')?></span></div><?
}
?>