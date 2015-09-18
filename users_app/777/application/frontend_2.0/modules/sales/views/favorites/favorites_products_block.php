<div class="form_block favorites_products_block" id="favorites_products_block">

<div class="form_message_block favorites_edit_message_block" id="favorites_edit_message_block">
	<div class="error_message">
		<div></div>
	</div>
	<div class="success_message">
		<div></div>
	</div>
</div>
<?php
if(isset($favorites_products) && count($favorites_products)>0)
{
?>
	<div class="form_label favorites_label"><span><?=$this->lang->line('favorites_label')?></span></div>
		<div class="form_item_block favorites_product_block">
		<div class="form_item_inside_block">
			<table cellspacing="0" cellpadding="0" class="form_table favorites_products_table">
			<?
			foreach($favorites_products as $ms)
			{
				?>
				<tr>
					<td valign="middle" align="left" class="product_data_td">
						<div class="prod_name"><span class="field_label"><a href="<?=$ms['detail_url']?>"><span><?=$ms['name']?></span></a></span></div>
						<input type="hidden" name="rowid" value="<?=$ms['rowid']?>"> 
						<?php
							if(isset($ms['timage'])) { ?><div class="prod_img"><a href="<?=$ms['detail_url']?>"><img src="<?=$ms['timage']?>"></a></div><? }
						?>
						<div class="prod_sku"><span class="field_label"><?=$this->lang->line('products_sku')?> : </span><span class="field_value"><?=$ms['sku']?></span></div>
						<div class="prod_price"><?=$ms['price']?></div>
					</td>
					<td valign="middle" width="25%" class="cart_edit_buttons">
						<div class="form_button cart_edit_button"><a href="<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/favorites/ajax_delete_favorites_product/rowid/'.$ms['rowid'], 'lang' => $this->mlangs->lang_code));?>" class="cart_edit_delete_item" id="favorites_delete_item"><span><?=$this->lang->line('favorites_delete_item')?></span></a></div>
					</td>
				</tr>
				<?
			}
			?>
			</table>
		</div>	
		</div>
	</div>	
<?
}
else
{
	?><div class="form_label favorites_label"><span><?=$this->lang->line('favorites_is_empty')?></span></div><?
}
?>