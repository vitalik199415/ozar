<?php
if(isset($PRS_bestseller))
{
	?>
	<div class="products_bestseller_block" id="<?=$PRS_bestseller_block_id?>">
	<?=$this->template->get_temlate_view('bestseller_products');?>
	</div>
	<?php
}
?>