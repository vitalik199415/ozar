<?php
$pg = 'products_grid';
if(isset($product_grid_id))
{
	$pg = $product_grid_id;
}
?>
<script>
var $products_grid_object = $('#<?=$pg?>').gbc_products_grid();
</script>