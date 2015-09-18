<?php
if(isset($PRS_sale['products']) && count($PRS_sale['products'])>0)
{
foreach($PRS_new['products'] as $ms)
	{
		?>
        <div class="sale_top">Акционные товары</div>
		<div class="block_prod">
                <?php
                if(isset($ms['timage']))
                {
                    ?>
                    <div class="img_block">
                        <a href="<?=$ms['detail_url']?>" title="<?=quotes_to_entities($ms['image_name'])?>">
                            <img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" height="100" />
                        </a>
                    </div>
                    <?
                }
                ?>
                <a href="<?=$ms['detail_url']?>"><div class="sale_name"><?=$ms['name']?></div></a>
                <?=$ms['price']?>
		</div>
		<?
	}
}
?>