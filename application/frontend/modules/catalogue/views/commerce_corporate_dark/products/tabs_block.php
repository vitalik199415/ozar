<div class="tabs_block">
	<ul class="tabs">
		<?php
		if(trim(strip_tags($PRD_array['product']['full_description'])) != '')
		{
		?>
		<li><a href="#" class="big_button"><span><?=$this->lang->line('products_description')?></span></a></li>
		<?
		}
		if(isset($PRD_array['images_array']) && count($PRD_array['images_array'])>0)
		{
		?>
		<li><a href="#" class="big_button"><span><?=$this->lang->line('products_photo')?></span></a></li>
		<?
		}
		if($PRD_array['product_settings']['reviews_on'] == 1)
		{
		?>
		<li><a href="#" class="big_button"><span><?=$this->lang->line('products_review')?></span></a></li>
		<?
		}
		if($PRD_array['product_settings']['related_on'] == 1 && count($PRD_array['related_products']['related_products']) > 0)
		{
		?>
		<li><a href="#" id="related" class="big_button"><span><?=$this->lang->line('products_related')?></span></a></li>
		<?
		}
		?>
	</ul>
	<div class="panes">
		<?php
		if(trim(strip_tags($PRD_array['product']['full_description'])) != '')
		{
		?>
		<div class="tab">
			<?=$this->template->get_temlate_view('PRD_description_'.$PRD_ID);?>
		</div>
		<?
		}
		if(isset($PRD_array['images_array']) && count($PRD_array['images_array'])>0)
		{
		?>
		<div class="tab">
			<?=$this->template->get_temlate_view('PRD_tab_images_'.$PRD_ID);?>
		</div>
		<?
		}
		if($PRD_array['product_settings']['reviews_on'] == 1)
		{
		?>
		<div class="tab">
			<?=$this->template->get_temlate_view('PRD_comments_block_'.$PRD_ID);?>
			<div style="clear:both;"></div>
		</div>
		<?
		}
		if($PRD_array['product_settings']['related_on'] == 1 && count($PRD_array['related_products']['related_products']) > 0)
		{
		?>
		<div class="tab" id="related_content">
			<?=$this->template->get_temlate_view('PRD_related_'.$PRD_ID);?>
		</div>
		<?
		}
		?>
	</div>
</div>
<div style="height:5px;"></div>