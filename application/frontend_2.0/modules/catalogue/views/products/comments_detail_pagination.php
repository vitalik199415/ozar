<?php
if(isset($pages) && count($pages)>1)
{
?>
	<div class="pagination_pages_block">
		<div class="base_block">
			<div class="base_top">
				<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
			</div>
			<div class="base_center">
				<div class="base_center_left"></div><div class="base_center_right"></div>
				<div class="base_center_repeat">
					<div class="pages_block">
						<span class="rows_count"><?=$this->lang->line('products_comments_rows_count')?> :<b><?=$rows_count?></b></span>
						<!--<span class="pages_count"><?=$this->lang->line('products_comments_pages_count')?> :<b><?=$pages_count?></b></span>-->
						<span class="pages_active"><?=$this->lang->line('products_comments_page')?> :<b><?=$pages_active?></b></span>
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
								?><a href="<?=$ms['href']?>" class="page"><span><?=$ms['num']?></span></a><?
							}
							if($ms['act']==1)
							{
								?><a class="page page_act"><span><?=$ms['num']?></span></a><?
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
						<div class="clear_both"></div>
					</div>
				</div>
			</div>	
			<div class="base_bot">
				<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
			</div>
		</div>
	</div>
<?php		
}
?>