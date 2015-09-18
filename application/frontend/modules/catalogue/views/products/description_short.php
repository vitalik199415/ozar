<?php
if(trim(strip_tags($PRD_array['product']['short_description'])) != '')
{
	?>
	<div class="product_description_short"><?=$PRD_array['product']['short_description']?></div>
	<div class="clear_both"></div>
	<div class="bottom_line"></div>
	<?
}
?>