<?php
if($order_customer != FALSE && is_array($order_customer))
{
	?><div align="right" style="color:#FFFFFF; font-size:15px;">Покупатель зарегистрированый пользователь - <a href="<?=set_url('customers/view/id/'.$order_customer['ID'])?>" target="_blank"><?=$order_customer['name'].' '.$order_customer['email']?></a>&nbsp&nbsp&nbsp&nbsp<a href="<?=set_url('*/orders/ajax_unset_order_customer/ord_id/'.$ord_id)?>" class="icon_delete" id="unset_order_customer"></a></div><?
}
else
{
	?><div align="right" style="color:#FFFFFF; font-size:15px;">Покупатель обычный пользователь</div><?
}
?>