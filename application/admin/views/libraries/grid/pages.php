<?php
if($grid_pages)
{
	?>
		<div class="pages_block">
		<div class="border_grey">
		<div class="page_title_rb">Количество записей : <b><?=$grid_pages['row_count']?></b></div>
		<div class="page_title_rb">Показать на страницу : <?=form_dropdown('limit', $grid_pages['dropdown']['values'], $grid_pages['dropdown']['active'], 'autocomplete="off"');?></div>
		<?php
			if(isset($grid_pages['pages']) && count($grid_pages['pages'])>1)
			{
				?>
					<div class="page_title">Страница : </div>
				<?
				foreach($grid_pages['pages'] as $ms)
				{
					if($ms['act']==0)
					{
						?><a href="<?=$ms['href']?>" class="page" rel="<?=$ms['href']?>"><?=$ms['num']?></a><?
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
			}
		?>
		<div style="clear:both;"></div>
		</div>
		</div>
	<?
}
?>