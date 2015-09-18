<?php
if(isset($sale_pr_id))
{
?><div align="center" style="position:absolute; background:#333333; border:1px solid #888888;"><input type="text" name="edit_pr_qty" value="<?=$qty?>" style="width:25px;"><a href="<?=set_url(array('warehouse/warehouses_sales','ajax_sale_edit_product_qty', 'wh_id', $wh_id, 'sale_id', $sale_id, 'sale_pr_id', $sale_pr_id))?>" style="vertical-align:bottom;" class="icon_ok edit_product_qty_submit"></a><a href="#" style="vertical-align:bottom;" class="icon_delete cancel_edit_product_qty"></a></div><?
}
?>