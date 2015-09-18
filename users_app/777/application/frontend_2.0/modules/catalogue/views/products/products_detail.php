<div class="clear_both"></div>
<?php
if(isset($PRD_array) && isset($PRD_block_id))
{
?>

<div class="clear_both"></div>
<div class="product_full_block clearfix" id="<?=$PRD_block_id?>">
    <div class="full_name_sku_block">
        <div class="product_full_name">
            <?=$PRD_array['product']['name']?>
        </div>
        <div class="product_full_sku">
            <?=$this->lang->line('products_sku')?> : <span><?=$PRD_array['product']['sku']?></span>
        </div>
        <div class="clear_both"></div>
    </div>
    
    <div class="block">
        <div style="position:relative">
            <div class="marks_block">
                <?php if($PRD_array['product']['sale']) { ?><div class="rasprodaja_mark"></div><? } ?>
                <?php if($PRD_array['product']['new']) { ?><div class="novelty_mark"></div><? } ?>
                <?php if($PRD_array['product']['bestseller']) { ?><div class="hitsales_mark"<? } ?>
                <?php if($PRD_array['product']['different_colors']) { ?><div class="different_colors_mark"></div><? } ?>
                <?php if($PRD_array['product']['action']) { ?><div class="akciya_mark"></div><? } ?>
                <?php if($PRD_array['product']['super_price']) { ?><div class="super_price_mark"></div><? } ?>
                <?php if($PRD_array['product']['restricted_party']) { ?><div class="restricted_party_mark"></div><? } ?>
                <?php if($PRD_array['product']['customised_product']) { ?><div class="customised_product_mark">Модель под заказ</div><? } ?>
                <?php if(!$PRD_array['product']['in_stock'])  
                    {
                        ?>
                            <div class="not_in_sale_mark">Нет в наличии</div>
                        <?
                    }
                ?>
            </div>
            <?=$this->template->get_temlate_view('PRD_images_'.$PRD_ID);?>
        </div>
        
        <div class="data_block">
            <div class="small_images_block"></div>
            <div class="delivery_block">
                <div class="delivery_box"></div>
                <div class="delivery_text">Доставка в:</div>
                <a href="/users_app/777/delivery/ukraine.html" class="urkraine_ico modalbox">Украину</a>
                <a href="/users_app/777/delivery/russia.html" class="russia_ico modalbox">Россию</a>
                <a href="/users_app/777/delivery/belorus.html" class="brus_ico modalbox">Беларусь</a>
                <a href="/users_app/777/delivery/sng.html" class="sng_ico modalbox">СНГ</a>
                <a href="/users_app/777/delivery/krim.html" class="krim_ico modalbox">&nbsp&nbspКрым</a>
            </div>
            <script>
                $("a.modalbox").fancybox(
                    {                                 
                        "frameWidth" : 1200,     
                        "frameHeight" : 500,
                        "hideOnContentClick" : false,
                        "hideOnOverlayClick" : false
                                                      
                    });
            </script>
            <div class="prev_next_prod_block">
                <div class="prev_product_href">
                    <? if(count($PRD_array['prev']) > 0): ?>
                        <a href="<?=$PRD_array['prev']['url']?>">
                           <i class="icon-chevron-sign-left"></i> Предыдущий товар 
                        </a>
                    <?endif;?>
                </div>
                <div class="next_product_href">
                    <? if(count($PRD_array['next']) > 0): ?>
                        <a href="<?=$PRD_array['next']['url']?>">
                            Следующий товар <i class="icon-chevron-sign-right"></i>
                        </a>
                    <? endif; ?>
                </div>
            </div>
            <div class="clear_both"></div>
            
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
            <div class="buttons_block">
                <div class="qty">
                    <span>
                       К-во:
                    </span>
                	<input type="text" class="inputboxquantity" size="4" id="quantity" name="qty" value="1"/>
                    <div class="plus_minus_block">
                        <input type="button" class="plus transition_3s" />
                        <input type="button" class="minus transition_3s" />
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
                </div>
                <a href="#" class="add_to_cart" id="to_cart">
                    <span>
                        <?=$this->lang->line('products_to_cart')?>
                    </span>
                </a>
                <a href="#" class="add_to_favorite" id="to_favorites">
                    <i class="icon-heart">
                        <i class="icon-plus-sign"></i>
                    </i>
                </a>
            </div>
            <div class="clear_both"></div>
            <?
            if($PRD_array['product']['short_description']){
            ?>
            <div class="prod_full_short_descr">
                <?=$this->template->get_temlate_view('PRD_description_short_'.$PRD_ID);?>
            </div>
            <?
            }
            ?>
            <div class="prod_full_buttons_block"> 
                <?php
                    if(!$PRD_array['product']['in_stock'])
                    {
                        ?>
                          <a href="#" id="to_waitlist" class="check_btn">
                                Сообщить о наличии
                            </a>
                        <?
                    }
                ?>
                <a href="/" class="call_me_btn" id="ob2">
                    Заказать звонок
                </a>
                <script type="text/javascript">
                    $(function(){
                        $('#ob2').jbcallme({
                            title: "Оставить заявку",
                            postfix: "form2",
                            no_submit: true,
                            fields:{
                                descr:{
                                    label: "Примечание к заявке",
                                    type: "textarea"
                                    },
                                send: {
                                    type: "submit",
                                    value: "Оставить заявку",
                                }   
                                },          
                            });
                    })
                </script>
            </div>
            <div class="clear_both"></div>
            <div class="prod_full_back">
                <?php if(isset($PRD_array['product']['back_url']))
                {
                ?>
                    <a href="<?=$PRD_array['product']['back_url']?>" class="back_button"><i class="icon-chevron-sign-left"></i> <?=$this->lang->line('base_back_link_text')?></a>
                <?php 
                }
                ?>
            </div>
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
