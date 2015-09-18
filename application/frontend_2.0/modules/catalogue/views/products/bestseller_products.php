<?php
if(isset($PRS_bestseller['products']) && count($PRS_bestseller['products'])>0)
{
?>
<div class="products_carousel_vertical_block left_block_margin">
	<div class="base_left_block">
		<div class="base_left_top"><div class="label"><?=$this->lang->line('base_catalogue_bestseller_text')?></div></div>
		<div class="base_left_center">
			<div class="carousel_block">
				<div class="vertical_scroll_block">
					<div class="vertical_scroll">
					<div class="scroll_items">
						<?php
						foreach($PRS_bestseller['products'] as $ms)
						{
							?>
							<div class="item">
								<div class="base_block">
									<div class="base_top">
										<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
									</div>
									<div class="base_center">
										<div class="base_center_left"></div><div class="base_center_right"></div>
										<div class="base_center_repeat">
											<div class="carousel_product_block">
												<?php
												if(isset($ms['timage']))
												{
													?>
													<div class="image_block">
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
												<div class="product_name"><a href="<?=$ms['detail_url']?>"><?=$ms['name']?></a></div>
												<div class="product_sku"><?=$this->lang->line('products_sku')?> :<span><?=$ms['sku']?></span></div>
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
													<?=$ms['price']?>
												</div>
												<a href="#" class="to_cart"></a>
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
					</div>
					</div>
					<a class="arrow_up" id="products_carousel_vertical_block_up"></a>
					<a class="arrow_down" id="products_carousel_vertical_block_down"></a>
				</div>
			</div>
		</div>
		<div class="base_left_bot"><div class="base_left_bot_repeat"></div><div class="base_left_bot_right"></div></div>
	</div>	
</div>
<?
}
?>