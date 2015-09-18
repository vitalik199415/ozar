<?php
if(isset($order_data['shipping_method_fields']))
{
	?>
	<script>
	var orders_shipping_required_fields = new Object();
	<?
	foreach($order_data['shipping_method_fields'] as $ms)
	{
		if($ms['required'] == 1)
		{
		?>
			var orders_shipping_required_rules = {required: true};
			<?php if($ms['field'] == 'address_email') echo "orders_shipping_required_rules['email'] = true;";?>
			orders_shipping_required_fields["order_address[S][<?=$ms['field']?>]"] = orders_shipping_required_rules;
		<?
		}
	}
	?>
	</script>
	<?
}
else
{
	?>
	<script>
	var orders_shipping_required_fields = new Object();
	orders_shipping_required_fields["order_address[S][name]"] = {required: true};
	<? if($order_data['sales_settings']['address_S_country'] == 1){ ?> orders_shipping_required_fields["order_address[S][country]"] = {required: true}; <? } ?>
	<? if($order_data['sales_settings']['address_S_city'] == 1){ ?> orders_shipping_required_fields["order_address[S][city]"] = {required: true}; <? } ?>
	<? if($order_data['sales_settings']['address_S_address'] == 1){ ?> orders_shipping_required_fields["order_address[S][address]"] = {required: true}; <? } ?>
	<? if($order_data['sales_settings']['address_S_telephone'] == 1){ ?> orders_shipping_required_fields["order_address[S][telephone]"] = {required: true}; <? } ?>
	<? if($order_data['sales_settings']['address_S_skype'] == 1){ ?> orders_shipping_required_fields["order_address[S][skype]"] = {required: true}; <? } ?>
	<? if($order_data['sales_settings']['address_S_viber'] == 1){ ?> orders_shipping_required_fields["order_address[S][viber]"] = {required: true}; <? } ?>
	orders_shipping_required_fields["order_address[S][address_email]"] = {required: true, email: true};
	</script>
	<?
}
?>
<script>
	if (typeof($("#create_order").data('gbcGbc_orders')) != 'undefined')
	{
		$("#create_order").gbc_orders('option', 'order_required_shipping_fields', orders_shipping_required_fields);
	}
</script>