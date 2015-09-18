<?php
echo $this->template->get_temlate_view('types_block');
if(isset($PRS_array['products']))
{
	if(isset($PRS_array['pages'])) echo $this->load->view('pagination_pages', $PRS_array['pages'], TRUE);
	?>
	<div class="clear_both"></div>
	<?
	foreach($PRS_array['products'] as $ms)
	{
		?>
		<div class="products_short_block">
			<div class="base_block">
				<div class="base_top">
					<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
				</div>
				<div class="base_center">
					<div class="base_center_left"></div><div class="base_center_right"></div>
					<div class="base_center_repeat">
						<div class="block"><a href="#" rel="<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/favorites/ajax_add_item/id/'.$ms['ID'], 'lang' => $this->mlangs->lang_code));?>" id="to_favorites" class="add_to_favorites"><span></span></a>
							<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
							<td valign="top" width="1">
								<?php
								if(isset($ms['timage']))
								{
									?>
									<div class="images_block">
									<a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide" onclick="return hs.expand(this)">
									<?
									if($ms['bestseller']) echo '<div class="over_top">'.$this->lang->line('products_bestseller').'</div>';
									if($ms['new']) echo '<div class="over_bot">'.$this->lang->line('products_new').'</div>';
									?>
									<img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
									</a>
									</div>
									<?
								}
								?>
							</td>
							<td valign="top">
								<div class="product_name"><a href="<?=$ms['detail_url']?>"><?=$ms['name']?></a></div>
								<div class="product_sku"><?=$this->lang->line('products_sku')?>:<span><?=$ms['sku']?></span></div>
								<div class="product_price">
									<?php
									if(!$ms['in_stock'])
									{
										?><div class="not_in_stock"><span><?=$this->lang->line('products_not_in_store')?></span></div><?
									}
									if($ms['in_stock'] && $ms['sale'])
									{
										?><div class="not_in_stock"><span><?=$this->lang->line('products_sale')?></span></div><?
									}
									?>
									<table cellpadding="0" cellspacing="0" border="0" width="100%" class="price_n_description">
									<tr>
										<td width="50%" valign="top">
											<?=$ms['price']?>
										</td>
										<td width="50%" valign="top">
											<?php
											if(trim($ms['short_description']) != '')
											{
												?><div class="product_description"><?=$ms['short_description']?></div><div class="clear_both"></div><div class="bottom_line"></div><?
											}
											?>
										</td>
									</tr>
									</table>
								</div>
							</td>
							</tr>
							</table>
							<a href="<?=$ms['detail_url']?>" class="to_cart" id="to_cart"></a>
							<div class="delail"><a href="<?=$ms['detail_url']?>" class="detail_link"><span><?=$this->lang->line('base_detail_link_text')?></span></a></div>
						</div>
					</div>
				</div>
				<div class="base_bot">
					<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
				</div>
			</div>
		</div>	
	<?
	}
	?>
	<div class="clear_both"></div>
	<?
	if(isset($PRS_array['pages'])) echo $this->load->view('pagination_pages', $PRS_array['pages'], TRUE);
}
?>
<?=$this->template->get_temlate_view('categories_description_block');?>