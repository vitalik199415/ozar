<div class="default_categories_block">
    <div class="default_categories_block_title">
        <?=$this->lang->line('base_catalogue_text')?>
    </div>
    <div class="block">
		<?php
        if(isset($categories))
        {
            foreach($categories as $ms)
            {
                ?>
                    <div class="lvl_<?=$ms['level']?>">
                    	<a href="<?=$ms['category_url']?>" class="lvl<?=$ms['level']?>">
                        	<span><?=$ms['name']?> (<?=$ms['products_count']?>)</span>
                        </a>
                    </div>
                <?
            }
        }
        ?>
    </div>
</div>
