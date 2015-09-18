<?php
if(isset($PRD_array['albums_array']) && count($PRD_array['albums_array'])>0)
{
	foreach($PRD_array['albums_array'] as $alb)
	{
		if(isset($PRD_array['images_in_album_array'][$alb['ALBUM_ID']]) && count($PRD_array['images_in_album_array'][$alb['ALBUM_ID']]) > 0)
		{
			?>
            <div class="images_block" id="album_img_<?=$alb['ALBUM_ID']?>" align="center">
				<?
                $i = TRUE;
                foreach($PRD_array['images_in_album_array'][$alb['ALBUM_ID']] as $ms)
                {
                    if($i)
                        {
                            $bimage = $ms;
                            $i = FALSE;
                            ?>
                            <div class="big_image" align="left">
                                
								<img id="zoom_<?=$alb['ALBUM_ID']?>" src="<?=$bimage['bimage']?>" title="<?=quotes_to_entities($bimage['image_title'])?>" alt="<?=quotes_to_entities($bimage['image_alt'])?>" />
							</div>
                            
                            <?
                        }
                    ?>
                    <?
                }
                ?>
                <div class="images_switcher">
                    <div class="carusel_block" id="carusel_block">
                        
                        <div class="carusel">
                            <div class="items_block">
                                <?
                                      foreach($PRD_array['images_array'] as $ms)
                                    {
                                ?>
                                <div class="gallery_zoom item" id="gallery_<?=$alb['ALBUM_ID']?>">
                                    <a href="#" title="<?=quotes_to_entities($ms['image_name'])?>" data-image="<?=$ms['bimage']?>" data-zoom-image="<?=$ms['bimage']?>">
                                    <img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
                                    </a>
                                </div>
                                    <?
                                    }
                                ?>
                            </div>
                        </div>
                        <a href="javascript:function f(){return false;}" class="next"><i class=""></i>sdf</a>
                        <a href="javascript:function f(){return false;}" class="prev"><i class="icon-caret-left"></i>123</a>
                    </div>
                    <script>
                        $(".carusel").scrollable(
                        {
                            speed:1500
                            
                        });
                    </script>
                </div>
                <script>
                    $('#zoom_<?=$alb['ALBUM_ID']?>').elevateZoom({
                        gallery:'gallery_<?=$alb['ALBUM_ID']?>', 
                        cursor: 'pointer', 
                        galleryActiveClass: 'active', 
                        imageCrossfade: true, 
                        loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif',
                        easing : true,
                        scrollZoom: false,
                        zoomWindowWidth: 400,
                        zoomWindowHeight: 674,
                        borderSize: 1
                    });
                </script>
			</div>
			<?
		}
	}
}
else
{
	if(isset($PRD_array['images_array']) && count($PRD_array['images_array'])>0)
	{
		?>
		<div class="images_block" id="album_img_1" align="center">
			<?
            $i = TRUE;
            foreach($PRD_array['images_array'] as $ms)
            {
                if($i)
                    {
                        $bimage = $ms;
                        $i = FALSE;
                        ?>
                        <div class="big_image" align="left">
                            <img id="zoom_<?=$ms['ID']?>" src="<?=$bimage['bimage']?>" title="<?=quotes_to_entities($bimage['image_title'])?>" alt="<?=quotes_to_entities($bimage['image_alt'])?>" />
                        </div>
                     
                        <?
                    }
                ?>
                <?
            }
            ?>
            <div class="images_switcher">
                <div class="carusel_block" id="carusel_block">
                    <a href="javascript:function f(){return false;}" class="prev"></a>
                    <div class="carusel">
                        <div class="items_block">
                            <?
                            foreach($PRD_array['images_array'] as $ms)
                            {
                            ?>
                            <div class="gallery_zoom item" id="gallery_<?=$ms['ID']?>">
                                <a href="#" title="<?=quotes_to_entities($ms['image_name'])?>" data-image="<?=$ms['bimage']?>" data-zoom-image="<?=$ms['bimage']?>">
                                <img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
                                </a>
                            </div>
                             <?
                            }
                            ?>
                        </div>
                    </div>
                    <a href="javascript:function f(){return false;}" class="next"></a>
                </div>
                <script>
                    $(".carusel").scrollable(
                    {
                        speed:1500
                        
                    });
                </script>
            </div>
            <script>
                $('#zoom_<?=$ms['ID']?>').elevateZoom({
                    gallery:'gallery_<?=$ms['ID']?>', 
                    cursor: 'pointer',
                    lensFadeIn: true,
                    lensFadeOut: true,
                    zoomWindowFadeIn: true,
                    zoomWindowFadeOut: true,
                    galleryActiveClass: 'active', 
                    imageCrossfade: false, 
                    loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif',
                    scrollZoom: true,
                    zoomWindowWidth: 520,
                    zoomWindowHeight: 520,
                    easing: false,
                    borderSize: 1
                });
                $(".carusel").scrollable({
                    speed:1500
				});
            </script>
        </div>
		<?
	}
}
?>