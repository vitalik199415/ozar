<?php
if(isset($PRD_array['albums_array']) && count($PRD_array['albums_array'])>0)
{
	foreach($PRD_array['albums_array'] as $alb)
	{
	?>
	<script>
		var pr_config_highslide_album_<?=$alb['ALBUM_ID']?> = {
			slideshowGroup: 'pr_album_group_<?=$alb['ALBUM_ID']?>',
			transitions: ['expand', 'crossfade']
		};
	</script>
			<?
		if(isset($PRD_array['images_in_album_array'][$alb['ALBUM_ID']]) && count($PRD_array['images_in_album_array'][$alb['ALBUM_ID']]) > 0)
		{
			?><div class="images_block" id="album_img_<?=$alb['ALBUM_ID']?>"><?
			foreach($PRD_array['images_in_album_array'][$alb['ALBUM_ID']] as $ms)
			{
				?>
				
				<a href="<?=$ms['bimage']?>" title="<?=quotes_to_entities($ms['image_name'])?>" class="highslide" onclick="return hs.expand(this, pr_config_highslide_album_<?=$alb['ALBUM_ID']?>)">
				<img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" />
				<div class="clear_both"></div>
				</a>
				<?					
				if($PRD_array['product']['bestseller']) echo '<div class="bestseller"></div>';
				if($PRD_array['product']['new']) echo '<div class="new"></div>';
				if($PRD_array['product']['sale']) echo '<div class="sale"></div>';
				if(!$PRD_array['product']['in_stock']) echo '<div class="out_in_stock">'.$this->lang->line('products_not_in_store').'</div>';
			?>
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
		<script>
			var pr_config_highslide_album = {
				slideshowGroup: 'pr_album_group_<?=$PRD_array['product']['ID']?>',
				transitions: ['expand', 'crossfade']
			};
		</script>
		<div class="images_block">

		<?
		foreach($PRD_array['images_array'] as $ms)
		{
			?>
				<a href="<?=$ms['bimage']?>" class="highslide" title="<?=quotes_to_entities($ms['image_name'])?>" onclick="return hs.expand(this, pr_config_highslide_album)">
					<img src="<?=$ms['timage']?>"  title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>"/>
				</a>
			<?					
				if($PRD_array['product']['bestseller']) echo '<div class="bestseller"></div>';
				if($PRD_array['product']['new']) echo '<div class="new"></div>';
				if($PRD_array['product']['sale']) echo '<div class="sale"></div>';
				if(!$PRD_array['product']['in_stock']) echo '<div class="out_in_stock">'.$this->lang->line('products_not_in_store').'</div>';
			?>
			<?
			
		}
		?>
		</div>
		<?
	}
}
?>