sdf<script type="text/javascript">
   $( function(){
    $( "#gallery" ).jGallery( {
        "transitionCols":"1",
        "transitionRows":"1",
        "thumbnailsPosition":"bottom",
        "thumbType":"image",
        "backgroundColor":"FFFFFF",
        "textColor":"000000",
        "mode":"standard"
    } );
} );
} );
    </script>
<?php
if(isset($album))
{
?>
<div class="album_detail_block">
	<div class="block">
    	<div id="gallery">
            <div class="name"><?=$album['name']?></div>
            <div class="img_block">
            <?
                foreach($album['img'] as $ms)
                {
                    ?>
                        <a href="<?=$ms['bimage']?>" class="img_href highslide" onclick="return hs.expand(this)" title="<?=quotes_to_entities($ms['image_name'])?>"><img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" ></a>
                    <?
                }
            ?>
            </div>
         </div>
            <div class="clear_both"></div>
            <div class="description">
            <?=$album['full_description']?>
            </div>
            <?php if(isset($album['back_url'])) echo '<div class="back_link"><a href="'.$album['back_url'].'" ><i class="fa fa-chevron-circle-left"></i><span>'.$this->lang->line('base_back_link_text').'</span></a></div>';?>
            <div class="clear_both"></div>
       
	</div>
</div>
<?
}
?>