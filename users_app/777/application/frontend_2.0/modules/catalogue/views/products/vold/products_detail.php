<?php
if(isset($PRD_array) && isset($PRD_block_id))
{
?>
<div class="product_full_block clearfix" id="<?=$PRD_block_id?>">
    <div class="block">
        
        <?=$this->template->get_temlate_view('PRD_images_'.$PRD_ID);?>
        <div class="data_block">
        	<div class="name">
                <?=$PRD_array['product']['name']?>
            </div>
            <div class="clear_both"></div>
            <div class="sku">
                <?=$this->lang->line('products_sku')?> : <?=$PRD_array['product']['sku']?>
            </div>
            <div class="small_images_block">

            </div>
            <!--<div class="marks_block">
				<?php if(!$PRD_array['product']['in_stock']) { ?><div class="in_stock"><?=$this->lang->line('products_not_in_store')?></div><? } ?>
                <?php if($PRD_array['product']['bestseller']) { ?><div class="bestseller"><?=$this->lang->line('products_bestseller')?></div> <? } ?>
                <?php if($PRD_array['product']['new']) { ?><div class="new"><?=$this->lang->line('products_new')?></div><? } ?>
                <?php if($PRD_array['product']['sale']) { ?><div class="sale"><?=$this->lang->line('products_sale')?></div><? } ?>
            </div>-->
			<?
			if(isset($PRD_array['albums_array']) && count($PRD_array['albums_array'])>0)
			{
			?>
            <div class="color_title">
                Цвет:    
            </div>
			<?
			}
			?>
            <?=$this->template->get_temlate_view('PRD_albums_'.$PRD_ID);?>
            <?=$this->template->get_temlate_view('PRD_attributes_'.$PRD_ID);?><div class="clear_both"></div>
            <?=$this->template->get_temlate_view('PRD_prices_'.$PRD_ID);?>
            <div class="buttons_block clearfix">
                <div class="qty">
                    <span>
                       К-во:
                    </span>
                	<input type="text" class="inputboxquantity" size="4" id="quantity" name="qty" value="1"/>
                    <input type="button" class="minus transition_3s" />
                	<input type="button" class="plus transition_3s" />
					<script type="text/javascript">
                        var qty_el = document.getElementById('quantity');
                        var qty = qty_el.value;
                        $('.minus').click(function(){
                            if (!isNaN(qty) && qty > 0 )
                            qty_el.value--;
                            return false;
                        })
                        
                        $('.plus').click(function(){
                            if (!isNaN(qty))
                            qty_el.value++;
                            return false;
                        })
                    </script>
                </div>
                <a href="#" class="add_to_cart" id="to_cart">
                    <span>
                        <?=$this->lang->line('products_to_cart')?>
                    </span>
                </a>
                <a href="#" class="add_to_favorite" id="to_favorites"></a>
            </div>
            <div class="clear_both"></div>
            <?
            if($PRD_array['product']['short_description']){
            ?>
            <div class="full_description clearfix">
                <?=$this->template->get_temlate_view('PRD_description_short_'.$PRD_ID);?>
            </div>
            <?
            }
            ?>
            <?php if(isset($PRD_array['product']['back_url']))
            {
            ?>
                <a href="<?=$PRD_array['product']['back_url']?>" class="back_button"><?=$this->lang->line('base_back_link_text')?></span></a>
            <?php 
            }
            ?>
            <div class="clear_both"></div>
            
        </div>
        <div class="clear_both"></div>
        <?=$this->template->get_temlate_view('PRD_tabs_'.$PRD_ID);?>
    </div>
    <div class="clear_both"></div>
    <?=$this->template->get_temlate_view('PRD_related_'.$PRD_ID);?>
</div>
<?
}
?>
