<?php
if(isset($news))
{
?>
<div class="news_detail_block">
	<div class="block">
	<?php
	if(count($news['img'])>0)
	{
	?>
		<div class="img_block">
		<?
			foreach($news['img'] as $ms)
			{
				?>
					<a href="<?=$ms['bimage']?>" class="img_href highslide" onclick="return hs.expand(this)" title="<?=quotes_to_entities($ms['image_name'])?>"><img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>" ></a>
				<?
			}
		?>
		</div>
	<?php
	}
	?>
		<div class="name"><span><?=$news['name']?></span><span class="date"><?=$news['date']?></span></div>
		<span class="description">
			<?=$news['full_description']?>
		</span>
		<?php if(isset($news['back_url'])) echo '<div class="back_link_full"><a href="'.$news['back_url'].'" ><i class="icon-double-angle-left"></i> <span>'.$this->lang->line('base_back_link_text').'</span></a></div>';?>
	<div class="clear_both"></div>
	</div>
</div>
<?
}
?>