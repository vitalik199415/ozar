<?php
if($PRD_array['product_settings']['related_on'] == 1)
{
    if(isset($PRD_array['related_products']['related_products'] ) && count($PRD_array['related_products']['related_products'] )>0)
    {
    
?>
<div class="similar_block">
    <div class="clear_both"></div>
    <div class="horizontal_carousel_block" align="left">
  
            <?php
            foreach($PRD_array['related_products']['related_products'] as $ms)
            {
            ?>
                <div class="product_carousel_short">
                    <div class="block">
                        <div class="image_block">
                        <?php
                            if(isset($ms['timage']))
                            {
                                ?><a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide" onclick="return hs.expand(this)">
                                        <img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
                                    </a>
                                <?
                            }
                                if($ms['bestseller']) echo '<div class="bestseller"></div>';
                                if($ms['new']) echo '<div class="new"></div>';
                                if($ms['sale']) echo '<div class="sale_b"><div class="sale"></div></div>';
                                if(!$ms['in_stock']) echo '<div class="out_in_stock"></div>';
                            ?>
                        </div>
                        <div class="name">
                            <?=$ms['name']?>
                        </div>
                        <a href="#" class="detail_button"><span>Подробнее</span><i class="fa fa-caret-right"></i></a>
                        <div class="clear_both"></div>
                    </div>
                </div>
            <?
            }
            ?>
    </div>
</div>
<?php
    }
}
?>

