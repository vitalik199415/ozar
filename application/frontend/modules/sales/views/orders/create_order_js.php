<?php
if(isset($order_data['products']['cart_products']) && count($order_data['products']['cart_products'])>0)
{
?>
<script>
$("#create_order").gbc_orders(
{
	error_submit : 				'<?=$this->lang->line('c_o_error_submit')?>',
	url_login_form : 			'<?=$this->router->build_url('customers_methods_lang', array('method' => 'login_form', 'lang' => $this->mlangs->lang_code));?>',
	url_change_shipping_method :'<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/shipping_methods/ajax_get_shipping_methods_form', 'lang' => $this->mlangs->lang_code));?>',
	url_change_payment_method : '<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/payment_methods/ajax_get_payment_methods_form', 'lang' => $this->mlangs->lang_code));?>',
	url_edit_item : 			'<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/orders/ajax_edit_cart_item', 'lang' => $this->mlangs->lang_code));?>',
	url_delete_item : 			'<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/orders/ajax_delete_cart_item', 'lang' => $this->mlangs->lang_code));?>'
});
$("#create_order").gbc_orders('option', 'order_required_payment_fields', order_required_payment_fields);
$("#create_order").gbc_orders('option', 'order_required_shipping_fields', orders_shipping_required_fields);

/*$("#create_order").gbc_orders({beforeSubmitOrder : function(event, $this){ 
	delete($this.options.order_required_payment_fields['order_address[B][name]']);
	delete($this.options.order_required_payment_fields['order_address[B][address_email]']);
	delete($this.options.order_required_shipping_fields['order_address[S][address_email]']);
	var TA = $this.element.find("textarea[name='order[note]']");
	$(TA).val('123 : 3445, zzz : wwww '+$(TA).val());
	} 
});*/
</script>
<?php
}
?>