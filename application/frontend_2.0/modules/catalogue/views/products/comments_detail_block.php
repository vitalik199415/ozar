<?php
if(isset($PRD_array['product_settings']['reviews_on']) && $PRD_array['product_settings']['reviews_on'] == 1)
{
?>
<div class="product_detail_comments_block" id="product_comments_block_<?=$PRD_ID?>">
<div class="block">
	<div class="product_comments" id="product_comments">
	<?=$this->template->get_temlate_view('PRD_comments_'.$PRD_ID);?>
	</div>

	<div class="product_comments_form" id="product_comments_form">
	<?=$this->template->get_temlate_view('PRD_comments_form_'.$PRD_ID);?>
	</div>
</div>	
</div>
<?
}
?>