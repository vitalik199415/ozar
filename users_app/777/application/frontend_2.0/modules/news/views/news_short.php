<?php
if(isset($news))
{
?>
<div class="news_block">
<?php	
	if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);
		foreach($news as $ms)
		{
			?>
			<div class="block">
				<?php
				if(isset($ms['timage']))
				{
				?>
					<div class="img_block">
						<a <?php if(isset($ms['detail_url'])) echo 'href="'.$ms['detail_url'].'"';?> class="img_href"><img src="<?=$ms['timage']?>" title="<?=quotes_to_entities($ms['image_title'])?>" alt="<?=quotes_to_entities($ms['image_alt'])?>"></a>
					</div>	
				<?php
				}
				?>
				<div class="name"><span><?=$ms['name']?></span><span class="date"><?=$ms['date']?></span></div>
				<span class="description"><?=$ms['short_description']?></span>
				<?php if(isset($ms['detail_url'])) echo '<div class="detail_link"><a href="'.$ms['detail_url'].'" ><span>'.$this->lang->line('base_detail_link_text').'</span> <i class="icon-double-angle-right"></i></a></div>';?>
				<div class="clear_both"></div>
			</div>
			<?
		}
	if(isset($pages)) echo $this->load->view('pagination_pages', $pages, TRUE);		
?>
</div>
<?
}
?>