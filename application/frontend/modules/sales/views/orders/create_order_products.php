<div class="cart_edit_block">
	
	<div class="form_message_block cart_edit_message_block" id="order_cart_edit_message_block">
		<div class="error_message" id="order_cart_edit_error">
			<div></div>
		</div>
		<div class="success_message" id="order_cart_edit_success">
			<div></div>
		</div>
	</div>
	
	<div class="form_label order_label order_products_label"><span><?=$this->lang->line('c_o_form_order_products')?></span></div>
	<div class="form_item_block total_block order_products_total">
	<div class="form_item_inside_block">
		<span class="field_label"><?=$this->lang->line('cart_total_price');?> : </span><span class="field_value"><?=$order_data['products']['cart_total_price']?></span>
	</div>
	</div>
	<?
	foreach($order_data['products']['cart_products'] as $key => $ms)
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
						if(isset($ms['products']['timage'])) { ?><div class="products_fields prod_img"><a href="<?=$ms['products']['detail_url']?>"><img src="<?=$ms['products']['timage']?>"></a></div><? }
					?>
					<div class="prod_sku"><span class="field_label"><?=$this->lang->line('products_sku')?> : </span><span class="field_value"><?=$ms['products']['sku']?></span></div>
					
					<div class="prices_block">
					<div class="field_label prices_block_label"><span><?=$this->lang->line('products_price')?></span></div>
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
	<?
	}
	?>
	<div class="form_item_block total_block order_products_total">
	<div class="form_item_inside_block">
		<span class="field_label"><?=$this->lang->line('cart_total_price');?> : </span><span class="field_value"><?=$order_data['products']['cart_total_price']?></span>
	</div>
	</div>
</div>