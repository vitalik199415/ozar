<?php
if(isset($PRD_array['albums_array']) && count($PRD_array['albums_array'])>0)
{
	?>
	<div class="albums_block" id="albums_block">
	<?
	foreach($PRD_array['albums_array'] as $ms)
	{
		if($ms['type'] == 'COLOR')
		{
			?><div class="album_color_block"><a href="#" rel="<?=$ms['ALBUM_ID']?>" class="album_color"><span style="background-color:#<?=$ms['color']?>"></span></a></div><?
		}
		else
		{
			?><div class="album_text_block"><a href="#" rel="<?=$ms['ALBUM_ID']?>" class="album_text"><span><?=$ms['name']?></span></a></div><?
		}
	}
	?>
	</div>
	<?
}
?>