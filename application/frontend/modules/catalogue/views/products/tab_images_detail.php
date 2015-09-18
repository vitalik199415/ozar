<script>
	var pr_config_tab_highslide_album = {
		slideshowGroup: 'pr_tab_album_group_<?=$PRD_array['product']['ID']?>',
		transitions: ['expand', 'crossfade']
	};
</script>
<div class="tab_image_block">
<div class="block">
<?php
foreach($PRD_array['images_array'] as $ms)
{
?>
	<a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide" onclick="return hs.expand(this, pr_config_tab_highslide_album)">
	<img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
	</a>
<?
}
?>
</div>
</div>