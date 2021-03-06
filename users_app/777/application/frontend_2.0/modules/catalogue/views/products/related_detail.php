<?php
if($PRD_array['product_settings']['related_on'] == 1)
{
	if(isset($PRD_array['related_products']['related_products'] ) && count($PRD_array['related_products']['related_products'] )>0)
	{
	
	?>
<div id="related_products" class="related_block">
    <div class="title_block">
        <span>Сопутствующие</span> товары
        <a id="prev3" class="prev_new" href="#"><i class="icon-angle-left"></i></a>
        <a id="next3" class="next_new" href="#"><i class="icon-angle-right"></i></a>
    </div>
    <div class="list_carousel">
        
        <ul id="foo3">
		<?
		foreach($PRD_array['related_products']['related_products'] as $ms)
		{
		?>
			<li>
                <div class="products_block_short" align="center">
                    <div class="products_block_short_name">
                        <span><?=$ms['name']?></span>
                    </div>
                                <!--            	<a href="#" rel="<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/favorites/ajax_add_item/id/'.$ms['ID'], 'lang' => $this->mlangs->lang_code));?>" id="to_favorites" class="add_to_favorites"><span></span></a>
                                -->    
                    <?php
                        if(isset($ms['timage']))
                        {
                            ?>
                            <div class="prod_img" align="center">
                                <a href="<?=$ms['detail_url']?>" title="<?=quotes_to_entities($ms['image_name'])?>">
                                    <img style="max-width:200px" src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
                                </a>
                                <?
                                    if($ms['bestseller']) echo '<div class="bestseller"></div>';
                                    if($ms['new']) echo '<div class="new"></div>';
                                    if($ms['sale']) echo '<div class="sale"></div>';
                                    if(!$ms['in_stock']) echo '<div class="out_in_stock">'.$this->lang->line('products_not_in_store').'</div>';
                                ?>
                                <div class="clear_both"></div>
                            </div>
                            <?
                        }
                        ?>
                        <div class="products_block_short_bottom">
                            <div class="short_sku_detail">
                                <div class="short_sku">
                                    <?=$this->lang->line('products_sku')?> : <span><?=$ms['sku']?></span>
                                </div>
                                
                                <a href="<?=$ms['detail_url']?>" class="btn-detail" title="<?=$this->lang->line('base_detail_link_text')?>"><i class="icon-double-angle-right"></i></a>
                                <div class="clear_both"></div>
                            </div>
                            <div class="products_short_price_block"><?=$ms['price']?></div>
                            <div class="clear_both"></div>
                        </div>
                </div>
			</li>
		<?
		}
		?>
        </ul>
    	<div class="clear_both"></div>
    </div>
    <div class="clear_both"></div>
</div>
<?
	}
}
?>
