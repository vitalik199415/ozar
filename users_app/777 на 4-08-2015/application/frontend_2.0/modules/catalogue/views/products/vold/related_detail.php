<?php
if($PRD_array['product_settings']['related_on'] == 1)
{
	if(isset($PRD_array['related_products']['related_products'] ) && count($PRD_array['related_products']['related_products'] )>0)
	{
	
	?>
<div id="related_products">
    <div class="list_carousel">
    	<div class="right_polos_z_index"></div>
        <a id="prev3" class="prev" href="#"></a>
        <a id="next3" class="next" href="#"></a>
        <ul id="foo3">
		<?
		foreach($PRD_array['related_products']['related_products'] as $ms)
		{
		?>
			<li>
<!--            	<a href="#" rel="<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/favorites/ajax_add_item/id/'.$ms['ID'], 'lang' => $this->mlangs->lang_code));?>" id="to_favorites" class="add_to_favorites"><span></span></a>
-->                <?php
                if(isset($ms['timage']))
                {
                    ?>
                    <div class="prod_img" align="center">
                        <a href="<?=$ms['detail_url']?>" title="<?=quotes_to_entities($ms['image_name'])?>"  style="background:url(<?=$ms['timage']?>) no-repeat center;">
                            <img style="max-width:195px" src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
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
                <table cellpadding="0" cellspacing="0" class="name">
                    <tr><td valign="middle"><a href="<?=$ms['detail_url']?>"><?=$ms['name']?></a></td></tr>
                </table>
                <?=$ms['price']?>
                <a href="#" rel="<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/favorites/ajax_add_item/id/'.$ms['ID'], 'lang' => $this->mlangs->lang_code));?>" id="to_favorites" class="add_to_favorites">В избранное</a>
                <!--<div class="product_sku"> <?=$this->lang->line('products_sku')?>:<span><b><?=$ms['sku']?></b></span></div>-->
                <a href="<?=$ms['detail_url']?>" class="detail"><?=$this->lang->line('base_detail_link_text')?></a>
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
