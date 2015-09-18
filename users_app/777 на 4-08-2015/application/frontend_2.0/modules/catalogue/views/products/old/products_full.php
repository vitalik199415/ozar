<?php
if(isset($product))
{
?>	
	<div class="product_full_block" id="product_full_block">
		<div class="emblems">
			<?php
				if($product['bestseller'])
				{
				?>
					<div class="full_bestseller">
					</div>
					<?
				}
				?>
				<?
				if($product['new'])
				{
				?>
					<div class="new">
					</div>
				<?
				}
				
				if($product['sale'])
				{
				?>
					<div class="sale">
					</div>
				<?
				}
				?>
		</div>
		<div class="fblock">
			<div class="img_block">
				<div style="clear:both;"></div>
				<?php
					if(isset($images))
					{
						foreach($images as $ms)
						{
							?>
							<a oncontextmenu="return false;" href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide" onclick="return hs.expand(this)"><img onclick="contextmenu();" src="<?=$ms['timage']?>" class="prod_img_src" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" /></a>
							<div style="clear:both;"></div>
							<?
							
						}
					}
				?>
			</div>
			<div class="desc_block">
				<div class="name">
					<?=$product['name']?>
				</div>
				<div style="color:#CC0000;font-size:17px;font-weight:bold;">
					Товар продается только оптом.
				</div>
				<?php
					if(!$product['in_stock'])
					{
						?>
					<div class="not_in_stok">
						Нет в наличии
					</div>
					<?
					}
				?>
				<div class="sku">
					<?=$this->lang->line('products_sku')?> : <span><?=$product['sku']?>
				</div>
				<div class="product_price">
					<?=$prices?>
				</div>
				<div style="clear:both;"></div>
				<div class="products_attributes">
					<?=$attributes?>
				</div>
				<div style="clear:both;"></div>
				<div class="qty">
					<div style="float:left; padding:3px 0 0 0;">
						<span style="line-height:27px; text-align:center; display:block;float:left;">
							<?=$this->lang->line('products_qty')?>: 
						</span>
						<div style="float:left;margin:0 0 0 3px;">
							 <input type="text" name="qty" value="1" class="qty">
						</div>
					</div>
					<a href="#" class="add_to_cart" id="to_cart">
						<div>
							<?=$this->lang->line('products_to_cart')?>
						</div>
					</a>
				</div>
				<div style="clear:both;"></div>
				<div class="description">
					<?=$product['full_description']?>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
		<div style="clear:both;height:20px;"></div>
				<?php if(isset($product['back_url'])) { ?>
					<a href="<?=$product['back_url']?>" class="button_back">
						<div>
							<?=$this->lang->line('base_back_link_text')?>
						</div>
					</a>
				<?php } ?>
		<div style="clear:both;"></div>
	</div>
	
	<script language="javascript">
		$('#product_full_block').gbc_products_full(
		{
			price_attributes : price_attributes,
			product_id : '<?=$product['ID']?>',
			add_item_url : '<?=$this->router->build_url('cart_methods_lang', array('method' => 'ajax_add_item', 'lang' => $this->mlangs->lang_code));?>',
			write_admin_url : '<?=$this->router->build_url('customers_methods_lang', array('method' => 'write_admin_form', 'lang' => $this->mlangs->lang_code));?>'
		});
	</script>
	<script  language="javascript">
		function contextmenu(){
			$('div[class=highslide-container]').attr('oncontextmenu', 'return false;' );
		};
	</script>
<?
}
?>