<?php
if(isset($PRS_new))
{
	?>
	<div class="products_new_block" id="<?=$PRS_new_block_id?>">
	<?=$this->template->get_temlate_view('new_products');?>
	</div>
	<?php
}
?>