<?php
if(isset($PRD_array) && isset($PRD_block_id))
{
?>
	<div class="clear_both"></div>
	<div class="product_detail_block" id="<?=$PRD_block_id?>">
		<div class="base_block">		
			<div class="base_top">
				<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
			</div>
			<div class="base_center">
				<div class="base_center_left"></div><div class="base_center_right"></div>
				<div class="base_center_repeat">
					<div class="block">
						<a href="#" class="add_to_favorites" id="to_favorites"><span></span></a>
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
						<td valign="top" width="1">
						<?=$this->template->get_temlate_view('PRD_images_'.$PRD_ID);?>
						</td>
						<td valign="top">
							<div class="product_name"><?=$PRD_array['product']['name']?></div>
							<div class="product_sku"><?=$this->lang->line('products_sku')?>:<span><?=$PRD_array['product']['sku']?></span></div>
							<?
							if($PRD_array['product']['bestseller']) echo '<div class="products_bestseller"><span>'.$this->lang->line('products_bestseller').'</span></div>';
							if($PRD_array['product']['new']) echo '<div class="products_new"><span>'.$this->lang->line('products_new').'</span></div>';
							if(!$PRD_array['product']['in_stock'])
							{
								?><div><div class="not_in_stock"><span><?=$this->lang->line('products_not_in_store')?></span></div></div><?
							}
							if($PRD_array['product']['in_stock'] && $PRD_array['product']['sale'])
							{
								?><div><div class="not_in_stock"><span><?=$this->lang->line('products_sale')?></span></div></div><?
							}
							?>
							<?=$this->template->get_temlate_view('PRD_albums_'.$PRD_ID);?>
							<table cellspacing="0" cellpadding="0" border="0">
							<tr>
							<td width="70%" valign="top">
								<div class="product_price">
									<?=$this->template->get_temlate_view('PRD_prices_'.$PRD_ID);?>
								</div>
							</td>
							<td width="30%" valign="bottom" align="right">
								<div class="products_attributes">
									<?=$this->template->get_temlate_view('PRD_attributes_'.$PRD_ID);?>
								</div>
								<div class="add_to_cart_bot">
									<div class="add_to_cart"><div class="add_to_cart_input"><?=$this->lang->line('products_qty')?> : <input type="text" name="qty" value="1" autocomplete="off"><div class="real_qty_block" id="real_qty_block"></div></div><a href="#" class="to_cart" id="to_cart"></a></div>
								</div>
							</td>
							</tr>
							</table>
							<?=$this->template->get_temlate_view('PRD_description_'.$PRD_ID);?>
						</td>
						</tr>
						</table>
						<?php if(isset($PRD_array['product']['back_url'])) echo '<div class="back"><a href="'.$PRD_array['product']['back_url'].'" class="back_link"><span>'.$this->lang->line('base_back_link_text').'</span></a></div>';?>
					</div>
					
					<div class="clear_both"></div>
					<?=$this->template->get_temlate_view('PRD_comments_block_'.$PRD_ID);?>
					<div class="clear_both"></div>
				</div>
			</div>
			<div class="base_bot">
				<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
			</div>
		</div>
	</div>
	<div class="clear_both"></div>
<?
}
?>