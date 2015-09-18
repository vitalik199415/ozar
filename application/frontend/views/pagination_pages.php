<?php
if(isset($pages) && count($pages)>1)
{
	?>
	<div class="pagination_pages_block">
	<div class="pages_block">
		<span class="rows_count"><?=$this->lang->line('base_rows_count_text')?><b><?=$rows_count?></b></span>
		<span class="pages_count"><?=$this->lang->line('base_pages_count_text')?><b><?=$pages_count?></b></span>
		<span class="pages_active"><?=$this->lang->line('base_pages_text')?><b><?=$pages_active?></b></span>
		<div class="pages">
		<?php
			if($prev_url)
			{
			?>
				<a href="<?=$prev_url?>" class="page_prev"></a>
			<?
			}
			foreach($pages as $ms)
			{
				if($ms['act']==0)
				{
					?><a href="<?=$ms['href']?>" class="page"><?=$ms['num']?></a><?
				}
				if($ms['act']==1)
				{
					?><a class="page_act"><?=$ms['num']?></a><?
				}
				if($ms['act']==2)
				{
					?><span class="point">.....</span><?
				}
			}
			if($next_url)
			{
			?>
				<a href="<?=$next_url?>" class="page_next"></a>
			<?
			}
		?>
		</div>
		<div style="clear:both;"></div>
	</div>
	</div>
	<?
}
?>