<script type="text/javascript">
	jssor_slider1_starter = function (containerId) {
        var options = {
            $AutoPlay: false,                                    //[Optional] Whether to auto play, to enable slideshow, this option must be set to true, default value is false
            $AutoPlaySteps: 1,                                  //[Optional] Steps to go for each navigation request (this options applys only when slideshow disabled), the default value is 1
            $AutoPlayInterval: 4000,                            //[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing, default value is 3000
            $PauseOnHover: 1,                               //[Optional] Whether to pause when mouse over if a slider is auto playing, 0 no pause, 1 pause for desktop, 2 pause for touch device, 3 pause for desktop and touch device, 4 freeze for desktop, 8 freeze for touch device, 12 freeze for desktop and touch device, default value is 1

            $ArrowKeyNavigation: true,   			            //[Optional] Allows keyboard (arrow key) navigation or not, default value is false
            $SlideDuration: 160,                                //[Optional] Specifies default duration (swipe) for slide in milliseconds, default value is 500
            $MinDragOffsetToSlide: 20,                          //[Optional] Minimum drag offset to trigger slide , default value is 20
            $SlideWidth: 225,                                   //[Optional] Width of every slide in pixels, default value is width of 'slides' container
            //$SlideHeight: 150,                                //[Optional] Height of every slide in pixels, default value is height of 'slides' container
            $SlideSpacing: 3, 					                //[Optional] Space between each slide in pixels, default value is 0
            $DisplayPieces: 1,                                  //[Optional] Number of pieces to display (the slideshow would be disabled if the value is set to greater than 1), the default value is 1
            $ParkingPosition: 0,                              //[Optional] The offset position to park slide (this options applys only when slideshow disabled), default value is 0.
            $UISearchMode: 1,                                   //[Optional] The way (0 parellel, 1 recursive, default value is 1) to search UI components (slides container, loading screen, navigator container, arrow navigator container, thumbnail navigator container etc).
            $PlayOrientation: 1,                                //[Optional] Orientation to play slide (for auto play, navigation), 1 horizental, 2 vertical, 5 horizental reverse, 6 vertical reverse, default value is 1
            $DragOrientation: 1,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)

            $BulletNavigatorOptions: {                                //[Optional] Options to specify and enable navigator or not
                $Class: $JssorBulletNavigator$,                       //[Required] Class to create navigator instance
                $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                $AutoCenter: 0,                                 //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                $Steps: 1,                                      //[Optional] Steps to go for each navigation request, default value is 1
                $Lanes: 1,                                      //[Optional] Specify lanes to arrange items, default value is 1
                $SpacingX: 0,                                   //[Optional] Horizontal space between each item in pixel, default value is 0
                $SpacingY: 0,                                   //[Optional] Vertical space between each item in pixel, default value is 0
                $Orientation: 1                                 //[Optional] The orientation of the navigator, 1 horizontal, 2 vertical, default value is 1
            },

            $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$,              //[Requried] Class to create arrow navigator instance
                $ChanceToShow: 1,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                $AutoCenter: 2,                                 //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                $Steps: 1                                       //[Optional] Steps to go for each navigation request, default value is 1
            }
        };

        var jssor_slider1 = new $JssorSlider$(containerId, options);

        //responsive code begin
        //you can remove responsive code if you don't want the slider scales while window resizes
        function ScaleSlider() {
            var bodyWidth = document.body.clientWidth;
            if (bodyWidth)
                jssor_slider1.$ScaleWidth(Math.min(bodyWidth, 225));
            else
                window.setTimeout(ScaleSlider, 30);
        }

        ScaleSlider();
        $Jssor$.$AddEvent(window, "load", ScaleSlider);

        $Jssor$.$AddEvent(window, "resize", $Jssor$.$WindowResizeFilter(window, ScaleSlider));
        $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
        ////responsive code end
    };
</script>
<?php
if(isset($PRS_array['products']))
{
	
	?>
	<?=$this->template->get_temlate_view('types_active_additional_block');?>
	<? if(isset($PRS_array['pages'])) echo $this->load->view('pagination_pages', $PRS_array['pages'], TRUE);
	?><div class="clear_both"></div>
	
	<?=$this->template->get_temlate_view('sorting_block');?>
	<?
	foreach($PRS_array['products'] as $ms)
	{
	?>
	<div class="products_block_short products_short_block">
		<div class="products_block_short_name">
			<table cellpadding="0" cellspacing="0" border="0" height="100%">
				<tr valign="middle" align="center">
					<td>
						<a href="<?=$ms['detail_url']?>"><span><?=$ms['name']?></span></a>
					</td>
				</tr>
			</table>
	    	
	    </div>
	    <div class="products_block_short_img" align="center">	
	    	<?php if($ms['sale']) { ?><div class="rasprodaja_mark" style="top: 0; left: 11px;"></div><? } ?>
			<?php if($ms['new']) { ?><div class="novelty_mark" style="top: 0; left: 11px;"></div><? } ?>
			<?php if($ms['bestseller']) { ?><div class="hitsales_mark" style="top: 0; left: 11px;"></div><? } ?>
			<?php if($ms['different_colors']) { ?><div class="different_colors_mark" style="bottom: 3px; left: 11px;"></div><? } ?>
			<?php if($ms['action']) { ?><div class="akciya_mark" style="top: 5px; left: 16px;"></div><? } ?>
			<?php if($ms['super_price']) { ?><div class="super_price_mark" style="bottom: 10px; left: 16px;"></div><? } ?>
			<?php if($ms['restricted_party']) { ?><div class="restricted_party_mark" style="bottom: 3px; left: 11px;"></div><? } ?>
			<?php if($ms['customised_product']) { ?><div class="customised_product_mark" style="bottom: 5px; left: 11px;">Модель под заказ</div><? } ?>
			<?php if(!$ms['in_stock'])	
				{
					?>
						<div class="not_in_sale_mark" style="top: 150px; left:0px">Нет в наличии</div>
					<?
				}
			?>
	    	<?php
	        if(isset($ms['images']))
            {
	        ?>	
				<div id="slider<?=$ms['ID']?>_container" style="position: relative; top: 0px; left: 0px; width: 225px; height: 337px; overflow: hidden; ">
			        <div u="loading" style="position: absolute; top: 0px; left: 0px;">
			            <div style="filter: alpha(opacity=70); opacity:0.7; position: absolute; display: block;
			                        background-color: #000; top: 0px; left: 0px;width: 100%;height:100%;">
			            </div>
			            <div style="position: absolute; display: block; background: url(/users_app/777/img/loading.gif) no-repeat center center;
			                        top: 0px; left: 0px;width: 100%;height:100%;">
			            </div>
			        </div>
			        <div u="slides" style="cursor: move; position: absolute; left: 0px; top: 0px; width: 225px; height: 337px; overflow: hidden;">
		                <?
							foreach($ms['images'] as $img)
							{
		                    	?>
		                    	<div>
		                    	<a href="<?=$img['bimage']?>" class="gallery prod_full_zoomin" title="<?=quotes_to_entities($ms['image_name'])?>" rel="group"><i class="icon-zoom-in"></i></a>
									<table border="0" cellpadding="0" cellspacing="0" height="337">
										<tr valign="middle">
											<td>
												<img src="<?=$img['timage']?>" title="<?=quotes_to_entities($img['title'])?>" alt="<?=quotes_to_entities($img['title'])?>" />
											</td>
										</tr>
									</table>

										
									
								</div>
								<?
		                	}
		                ?>
				        <!-- Arrow Left -->
				        <span u="arrowleft" class="prod_short_arrow_left">
				        </span>
				        <!-- Arrow Right -->
				        <span u="arrowright" class="prod_short_arrow_right">
				        </span>
				        <script>
				            jssor_slider1_starter('slider<?=$ms['ID']?>_container');
				        </script>
				    </div>
				</div>
		    <?
			}
	    	?>
		 
	</div>
    <div class="products_block_short_bottom">
    	<div class="short_sku_detail">
    		<div class="short_sku">
				<?=$this->lang->line('products_sku')?> : <span><?=$ms['sku']?></span>
            </div>
            <a href="#" rel="<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/favorites/ajax_add_item/id/'.$ms['ID'], 'lang' => $this->mlangs->lang_code));?>" class="add_to_favorite" id="to_favorites">
            	<i class="icon-heart">
            		<i class="icon-plus-sign"></i>
            	</i>
            </a>
            <a href="<?=$ms['detail_url']?>" class="btn-detail" title="<?=$this->lang->line('base_detail_link_text')?>"><i class="icon-double-angle-right"></i></a>
        	<div class="clear_both"></div>
    	</div>
        <div class="products_short_price_block"><?=$ms['price']?></div>
        <div class="clear_both"></div>
    </div>
</div>	
<?
}
?>
<div class="clear_both"></div>
	<?
	if(isset($PRS_array['pages'])) echo $this->load->view('pagination_pages', $PRS_array['pages'], TRUE);
}
?>
<div class="cat_descr">
	<?=$this->template->get_temlate_view('categories_description_block');?>
</div> 

<script>
	$('.not_in_sale_mark').parents('.products_block_short_img').css('opacity','.6')
</script>
<script type="text/javascript">
	$("a.gallery").fancybox({
		"frameWidth" : 1200,     
        "frameHeight" : 500,
        "hideOnContentClick" : false,
        "hideOnOverlayClick" : false
	});
</script>