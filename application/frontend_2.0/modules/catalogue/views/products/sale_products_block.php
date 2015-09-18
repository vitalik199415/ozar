<?php
if(isset($PRS_sale))
{
	?>
	<div class="products_sale_block" id="<?=$PRS_sale_block_id?>">
	<?=$this->template->get_temlate_view('sale_products');?>
	</div>
	<?php
}
?>