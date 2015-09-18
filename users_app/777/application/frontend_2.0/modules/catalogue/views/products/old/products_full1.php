<?php
if(isset($product))
{
?>
	<div class="prod_full_block" id="product_full_block">
			<div class="prod_full_back_link">
				<?php if(isset($product['back_url'])) { ?><a href="<?=$product['back_url']?>">Вернуться назад</a><?php } ?>
			</div>
			
					
					<div class="img">
						<?php
							if(isset($images))
							{
								?><img src="<?=$images['0']['bimage']?>" width="550"  /><?
							}
						?>
						
							
							<?php 
							if($product['bestseller'])
							{
								?><div class="hitsales"></div><?
							}
							?>
                            <?php
								if($product['new'])
								{
									?><div class="prod_novelty"></div><?
								}
							?>
					</div>
					<div class="prod_full_text">
                        <div class="prod_full_descr">
                            <div class="prod_name"><?=$product['name']?></div>	
                            <?php
                                if(!$product['in_stock'])
                                {
                                    ?><div class="not_in_sale"><?=$this->lang->line('products_not_in_store')?></div><div class="clear_both"></div><?
                                }
                            ?>
                            <?php if($product['sale'])
								{
									?><div class="akciya"><div></div></div><?
								}
							?>				
                            <div class="prod_name">
                                <?=$this->lang->line('products_sku')?> : <?=$product['sku']?>
                            </div>
                            
                            <div class="price"><?=$prices?></div>
                            <div class="attributes">
                                <?=$attributes?>
                            </div>
                            <div class="qty_in_cart">
                                <div class="qty">
                                    Кол-во: <input type="text" name="qty" value="1">
                                </div>
                                <div class="to_cart">
                                    <a href="#" id="to_cart">
                                        <div class="text_to_cart">В корзину</div>
                                        <div class="korz_pic"></div>
                                    </a>
                                </div><div class="clear_both"></div>
                            </div>
                            <div class="description_block">
                                <?=$product['full_description']?>
                            </div>
						</div>
					</div>
					<div class="products_photo" align="left">
							<?php
							if(isset($images))
							{ 
							$i = 0;
							foreach($images as $key => $ms)
								{ 
								$i++;
								if($i > 1) {
									?>
									<a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" onclick="return hs.expand(this)">
									
										<img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
									
									</a>
									<?
									}
								}
							}
							?>	
					</div>
					<div class="prod_full_back_link">
						<?php if(isset($product['back_url'])) { ?><a href="<?=$product['back_url']?>">Вернуться назад</a><?php } ?>
					</div><div class="clear_both"></div>
	</div>
	
	<div class="clear_both"></div>
	<script language="javascript">
		$('#product_full_block').gbc_products_full(
		{
			price_attributes : price_attributes,
			product_id : '<?=$product['ID']?>',
			add_item_url : '<?=$this->router->build_url('cart_methods_lang', array('method' => 'ajax_add_item', 'lang' => $this->mlangs->lang_code));?>',
			write_admin_url : '<?=$this->router->build_url('customers_methods_lang', array('method' => 'write_admin_form', 'lang' => $this->mlangs->lang_code));?>'
		});
	</script>
<?
}
?>
