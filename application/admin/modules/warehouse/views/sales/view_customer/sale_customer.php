<?php
if($sale_customer != FALSE && is_array($sale_customer))
{
	?><div align="right" style="color:#FFFFFF; font-size:15px;">Покупатель зарегистрированый пользователь - <a href="<?=set_url('warehouse/customers/view/id/'.$sale_customer['ID'])?>" target="_blank"><?=$sale_customer['name'].' '.$sale_customer['email']?></a>&nbsp&nbsp&nbsp&nbsp<a href="<?=set_url('*/warehouses_sales/ajax_unset_sale_customer/wh_id/'.$wh_id.'/sale_id/'.$sale_id)?>" class="icon_delete" id="unset_sale_customer"></a></div><?
}
else
{
	?><div align="right" style="color:#FFFFFF; font-size:15px;">Покупатель обычный пользователь</div><?
}
?>