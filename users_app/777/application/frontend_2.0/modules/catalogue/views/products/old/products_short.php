<?php
if(isset($products))
{
	?>
	
	<div class="pagination"><?
	if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);
	?></div><div class="clear_both"></div><?
	foreach($products as $ms)
	{
		?>
		<div class="prod_block_short">
				<div class="prod_short_img">
					<?php
                    if(isset($ms['timage']))
                    {
                        ?><a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" onclick="return hs.expand(this)"><img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" /></a><?
                    }
                    ?>
                    <?php if($ms['bestseller'])
                        {
                            ?><div class="hitsales"><span></span></div><?
                        }
                    ?>
                    <?php if($ms['sale'])
                        {
                            ?><div class="akciya"></div><?
                        }
                    ?>
                    <?php
                        if(!$ms['in_stock'])
                        {
                            ?><div class="not_in_sale"><?=$this->lang->line('products_not_in_store')?></div><?
                        }
                    ?>
                    <?php
                        if($ms['new'])
                        {
                            ?><div class="prod_novelty"></div><?
                        }
                    ?>
                    <div class="prod_name"><?=$ms['name']?></div>
                    <div class="prod_sku">Артикул : <b><?=$ms['sku']?></b></div>
			</div>
            <div class="price_short"><?=$ms['price']?></div><div class="clear_both"></div>
            <div class="detail_btn" align="center">
                <a href="<?=$ms['detail_url']?>"><?=$this->lang->line('base_detail_link_text')?></a>
            </div>
		</div>
		<?
	}
	?><div class="clear_both"></div><div class="pagination"><?
	if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);
}?></div><?
?>