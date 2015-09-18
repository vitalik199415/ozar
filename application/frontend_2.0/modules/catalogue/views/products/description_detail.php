<?php
if(trim(strip_tags($PRD_array['product']['full_description'])) != '')
{
	?>
	<div class="product_description"><?=$PRD_array['product']['full_description']?></div>
	<div class="clear_both"></div>
	<div class="bottom_line"></div>
	<?
}
?>