<?php
if(isset($shipping_method_fields))
{
?>
<div style="margin:15px 0 0 0;">
	<div><?=$this->lang->line('c_o_form_shipping_method')?> :<b><?=$order_shipping_method?></b></div>
	<div><b><?=$this->lang->line('c_o_fieldset_shipping_address')?></b></div>
	<?php
	foreach($shipping_method_fields as $ms)
	{
		?><span><?=$this->lang->line($ms['alias'].'c_o_form_shipping_'.$ms['field'])?> : <b><?=@$shipping_address_data[$ms['field']]?></b>, </span><?
	}
	?>
</div>
<?
}
else
{
?>
<div style="margin:15px 0 0 0;">
	<div><b><?=$this->lang->line('c_o_fieldset_shipping_address')?></b></div>
		<span><?=$this->lang->line('c_o_form_name')?> 	 : <b><?=@$shipping_address_data['name']?></b>, </span>
		<span><?=$this->lang->line('c_o_form_country')?> : <b><?=@$shipping_address_data['country']?></b>, </span>
		<span><?=$this->lang->line('c_o_form_city')?> 	 : <b><?=@$shipping_address_data['city']?></b>, </span>
		<span><?=$this->lang->line('c_o_form_zip')?>	 : <b><?=@$shipping_address_data['zip']?></b>, </span>
		<span><?=$this->lang->line('c_o_form_address')?> : <b><?=@$shipping_address_data['address']?></b>, </span>
		<span><?=$this->lang->line('c_o_form_telephone')?> 	 : <b><?=@$shipping_address_data['telephone']?></b>, </span>
		<span><?=$this->lang->line('c_o_form_email')?> 	 : <b><?=@$shipping_address_data['address_email']?></b></span>
</div>
<?php
}
?>