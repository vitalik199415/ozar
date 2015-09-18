<?php
if($GridPages)
{
	?>
		<div class="pages_block">
		<div class="border_grey">
		<div class="page_title_rb">Количество записей : <b><?=$GridPages['row_count']?></b></div>
		<div class="page_title_rb">Показать на страницу : <?=form_dropdown('limit', $GridPages['dropdown']['values'], $GridPages['dropdown']['active'], 'autocomplete="off"');?></div>
		<?php
			if(isset($GridPages['pages']) && count($GridPages['pages'])>1)
			{
				?>
					<div class="page_title">Страница : </div>
				<?
				foreach($GridPages['pages'] as $ms)
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