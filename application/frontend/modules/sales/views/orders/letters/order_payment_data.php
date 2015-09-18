<div style="margin:15px 0 0 0;">
	<div><b><?=$this->lang->line('c_o_fieldset_billing_address')?></b></div>
	<span><?=$this->lang->line('c_o_form_name')?> 	 : <b><?=@$payment_address_data['name']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_country')?> : <b><?=@$payment_address_data['country']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_city')?> 	 : <b><?=@$payment_address_data['city']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_zip')?>	 : <b><?=@$payment_address_data['zip']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_address')?> : <b><?=@$payment_address_data['address']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_telephone')?> : <b><?=@$payment_address_data['telephone']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_email')?> 	 : <b><?=@$payment_address_data['address_email']?></b></span>
</div>
<?
if(isset($order_payment_method))
{
?>
<div><?=$this->lang->line('c_o_form_payment_method')?> : <b><?=$order_payment_method?></b></div>
<?
}
?>