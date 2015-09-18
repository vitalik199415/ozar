<?php
if(isset($PRS_array))
{
	?>
	<div class="search_result_block">
		<?php if($PRS_array['pages']['rows_count'] == 0):?>
			<div class="search_result_data"><h2 class="search_empty"><span><?=$this->lang->line('search_result_empty')?></span></h2></div>
		<?php else:?>
			<div class="search_result_data"><span class="search_keywords"><?=$this->lang->line('search_result_search_keywords')?>: <span><?=$PRS_array['search_keywords']?></span></span> <span class="search_count"><?=$this->lang->line('search_result_rows_count')?>: <span><?=$PRS_array['pages']['rows_count']?></span></span></div>
		<?php endif;?>
	<div id="<?=$PRS_block_id?>">
		<?=$this->template->get_temlate_view('PRS');?>
	</div>
	</div>
<?php
}
?>