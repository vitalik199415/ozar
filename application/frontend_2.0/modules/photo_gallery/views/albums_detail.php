<?php
if(isset($album))
{
?>
<div class="album_detail_block">
	<div class="block">
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
		<div class="clear_both"></div>
		<div class="description">
		<?=$album['full_description']?>
		</div>
		<?php if(isset($album['back_url'])) echo '<div class="back_link"><a href="'.$album['back_url'].'" ><span>'.$this->lang->line('base_back_link_text').'</span></a></div>';?>
		<div class="clear_both"></div>
	</div>
</div>
<?
}
?>