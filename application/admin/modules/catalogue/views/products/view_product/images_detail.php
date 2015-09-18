<?php
if(isset($PRD_array['albums_array']) && count($PRD_array['albums_array'])>0)
{
	foreach($PRD_array['albums_array'] as $alb)
	{
		if(isset($PRD_array['images_in_album_array'][$alb['ALBUM_ID']]) && count($PRD_array['images_in_album_array'][$alb['ALBUM_ID']]) > 0)
		{
			?><div class="images_block" id="album_img_<?=$alb['ALBUM_ID']?>"><?
			foreach($PRD_array['images_in_album_array'][$alb['ALBUM_ID']] as $ms)
			{
			?>
				<a href="#" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide">
				<img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
				<div class="clear_both"></div>
				</a>
			<?
			}
			?>
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
		<div class="images_block">
		<?
		foreach($PRD_array['images_array'] as $ms)
		{
			?>
				<a href="#" class="highslide" title="<?=quotes_to_entities($ms['image_name'])?>">
					<img src="<?=$ms['timage']?>"  title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>"/>
				</a>
			<?
		}
		?>
		</div>
		<?
	}
}
?>