<div class="novelty_top">
            Новинки
        </div>
<?php
if(isset($products))
{
	foreach($products as $ms)
	{
	?>
    	

			<div class="block">
            	<div class="img_block">
					<?php
                    if(isset($ms['timage']))
                    {
                        ?>
                            <a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide" onclick="return hs.expand(this)"><img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" height="100"  /></a>
                        <?php
                    }
                    ?>
                </div>
                <a href="<?=$ms['detail_url']?>"><div class="novelty_name"><?=$ms['name']?></div></a>
                <div class="novelty_price">
                    <?=$ms['price']?>
                </div>
            </div>
			

	<?
	}
}
?>

