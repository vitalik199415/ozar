<script>
var order_required_payment_fields = new Object();
order_required_payment_fields["order_address[B][name]"] = {required: true};
<? if($order_data['sales_settings']['address_B_country'] == 1){ ?> order_required_payment_fields["order_address[B][country]"] = {required: true}; <? } ?>
<? if($order_data['sales_settings']['address_B_city'] == 1){ ?> order_required_payment_fields["order_address[B][city]"] = {required: true}; <? } ?>
<? if($order_data['sales_settings']['address_B_address'] == 1){ ?> order_required_payment_fields["order_address[B][address]"] = {required: true}; <? } ?>
<? if($order_data['sales_settings']['address_B_telephone'] == 1){ ?> order_required_payment_fields["order_address[B][telephone]"] = {required: true}; <? } ?>
order_required_payment_fields["order_address[B][address_email]"] = {required: true, email: true};
</script>
