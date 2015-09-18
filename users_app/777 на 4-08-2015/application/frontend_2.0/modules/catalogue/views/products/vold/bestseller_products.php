<?php
if(isset($PRS_bestseller['products']) && count($PRS_bestseller['products'])>
0)
{
?>
<script>
 var pr_config_highslide_bestseller_products = {
  slideshowGroup: 'pr_bestseller_group',
  transitions: ['expand', 'crossfade']
 };
</script>
<div class="bestsellers_block">
  <div class="title_block">
    <div class="block">
    	Распродажа  
    </div>
  </div>
  <div class="horizontal_carousel_block" align="center">
    <ul id="bestsellers_products">
      <?php
        foreach($PRS_bestseller['products'] as $ms)
        {
        ?>
      <li>
        <div class="bestsellers_products_block">
          
            <div class="block">
              <div class="image_block">
                <?php
                if(isset($ms['timage']))
                {
                 ?>
                <a href="<?=$ms['bimage']?>
                  " title="
                  <?=quotes_to_entities($ms['image_name'])?>
                  " class="highslide" onclick="return hs.expand(this)">
                  <img src="<?=$ms['timage']?>
                  " class="prod_img_src" title="
                  <?=quotes_to_entities($ms['image_title'])?>
                  " alt="
                  <?=quotes_to_entities($ms['image_alt'])?>" /></a>
                <?
                }
                ?>
                <?
                 if($ms['bestseller']) echo '<div class="bestseller"></div>
              ';
                 if($ms['new']) echo '
              <div class="new"></div>
              ';
                 if($ms['sale']) echo '
              <div class="sale_b">
                <div class="sale"></div>
              </div>
              ';
                 if(!$ms['in_stock']) echo '
              <div class="out_in_stock"></div>
              ';
                ?>
            </div>
            <div class="name">
              <a href="<?=$ms['detail_url']?>
                ">
                <span>
                  <?=$ms['name']?></span>
              </a>
            </div>
            <?=$ms['price']?>
            <div class="detail_button">
              <a href="<?=$ms['detail_url']?>" class="detail_but">
                <span>
                  <i class="icon-double-angle-right"></i>
              </a>
            </div>
            <div style="clear:both;"></div>
          
        </div>
      </div>
    </li>
    <?
        }
        ?>
    </ul>
    <div class="clear_both"></div>
</div>

</div>
<?
}
?>