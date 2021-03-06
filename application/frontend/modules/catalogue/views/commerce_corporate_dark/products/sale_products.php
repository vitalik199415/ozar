<?php
if(isset($PRS_sale['products']) && count($PRS_sale['products'])>0)
{
?>
<script>
	var pr_config_highslide_sale_products = {
		slideshowGroup: 'pr_sale_group',
		transitions: ['expand', 'crossfade']
	};
</script>
<div class="products_carousel_block left_block_margin anythingSlider_vertical" align="center">
<div class="base_left_block">
	<div class="base_left_top"><div class="label"><?=$this->lang->line('base_catalogue_sales_text')?></div></div>
	<div class="base_left_center">
		<ul id="sale_products">
			<?php
			foreach($PRS_sale['products'] as $ms)
			{
			?>
			<li>
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
									<a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide" onclick="return hs.expand(this, pr_config_highslide_sale_products)">
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
								<div class="sale_stock">
									<?php
									if($ms['sale'])
									{
										?><div class="not_in_stock"><span><?=$this->lang->line('products_sale')?></span></div><?
									}
									?>
								</div>
								<div class="products_price">
									<?=$ms['price']?>
								</div>
								<div class="to_cart_n_detail">
								<a href="#" class="to_cart"></a>
								<a href="<?=$ms['detail_url']?>" class="detail_link"><span><?=$this->lang->line('base_detail_link_text')?></span></a>
								</div>
							</div>
						</div>
					</div>
					<div class="base_bot">
						<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
					</div>
				</div>
			</li>
			<?
			}
			?>
		</ul>
	</div>
	<div class="base_left_bot"><div class="base_left_bot_repeat"></div><div class="base_left_bot_right"></div></div>
</div>
</div>
<?
}
?>