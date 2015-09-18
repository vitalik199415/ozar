<?php
if(isset($order_data['shipping_method_fields']))
{
?>
<div class="form_item_block cart_product_block">
<div class="form_item_inside_block">
	<div class="form_field order_field shipping_methods_select"><label for="shipping_method_select"><?=$this->lang->line('c_o_form_select_shipping_method')?> (*) :</label><?=form_dropdown('order_shipping_method_select', $order_data['shipping_methods_select'], $order_data['shipping_methods_select_active'], 'id = "order_select_shipping_method"')?><div class="clear_both"></div></div>
	<div class="form_help order_help shipping_methods_select_desc"><span><?=$order_data['shipping_methods_data']['description']?></span></div>
</div>
</div>
<div class="shipping_address_block">
	<div class="form_button order_button same_as_billing"><a href="#" id="same_as_billing"><span><?=$this->lang->line('c_o_form_same_as_billing')?></span></a></div>
	<fieldset id="customer_address_s_fieldset" class="form_fieldset order_shipping_fieldset">
	<legend><?=$this->lang->line('c_o_fieldset_shipping_address')?></legend>
		<?php
		foreach(@$order_data['shipping_method_fields'] as $ms)
		{
			?><div class="form_field order_field shipping_field"><label for="order_address[S][<?=$ms['field']?>]"><?=$this->lang->line($order_data['shipping_method_alias'].'c_o_form_shipping_'.$ms['field'])?> <?php if(@$ms['required'] == 1) echo "(*)";?> :</label> 	<input type="text" name="order_address[S][<?=$ms['field']?>]" value="<?=@$order_data['customer_S_address_data'][$ms['field']]?>"><div class="clear_both"></div></div><?
		}
		?>
	</fieldset>
</div>
<?
}
else
{
?>
<div class="shipping_address_block">
	<div class="form_button order_button same_as_billing"><a href="#" id="same_as_billing"><span><?=$this->lang->line('c_o_form_same_as_billing')?></span></a></div>
	<fieldset id="customer_address_s_fieldset" class="form_fieldset order_shipping_fieldset">
	<legend><?=$this->lang->line('c_o_fieldset_shipping_address')?></legend>
		<div class="form_field order_field shipping_field"><label for="order_address[S][name]">				<?=$this->lang->line('c_o_form_name')?> (*) :</label> 	<input type="text" name="order_address[S][name]" value="<?=@$order_data['customer_S_address_data']['name']?>"><div class="clear_both"></div></div>
		<? if(@$order_data['sales_settings']['address_S_country'] == 0 || @$order_data['sales_settings']['address_S_country'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_S_country'] == 1) $must_be = ' (*)' ?>
		<div class="form_field order_field shipping_field"><label for="order_address[S][country]">			<?=$this->lang->line('c_o_form_country')?> (*) :</label><input type="text" name="order_address[S][country]" value="<?=@$order_data['customer_S_address_data']['country']?>"><div class="clear_both"></div></div>
		<? } ?>
		<? if(@$order_data['sales_settings']['address_S_city'] == 0 || @$order_data['sales_settings']['address_S_city'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_S_city'] == 1) $must_be = ' (*)' ?>
		<div class="form_field order_field shipping_field"><label for="order_address[S][city]">				<?=$this->lang->line('c_o_form_city')?> (*) :</label> 	<input type="text" name="order_address[S][city]" value="<?=@$order_data['customer_S_address_data']['city']?>"><div class="clear_both"></div></div>
		<? } ?>
		<? if(@$order_data['sales_settings']['address_S_zip'] == 0 || @$order_data['sales_settings']['address_S_zip'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_S_zip'] == 1) $must_be = ' (*)' ?>
		<div class="form_field order_field shipping_field"><label for="order_address[S][zip]">				<?=$this->lang->line('c_o_form_zip')?></label> 			<input type="text" name="order_address[S][zip]" value="<?=@$order_data['customer_S_address_data']['zip']?>"><div class="clear_both"></div></div>
		<? } ?>
		<? if(@$order_data['sales_settings']['address_S_address'] == 0 || @$order_data['sales_settings']['address_S_address'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_S_address'] == 1) $must_be = ' (*)' ?>
		<div class="form_field order_field shipping_field"><label for="order_address[S][address]">			<?=$this->lang->line('c_o_form_address')?> (*) :</label><input type="text" name="order_address[S][address]" value="<?=@$order_data['customer_S_address_data']['address']?>"><div class="clear_both"></div></div>
		<? } ?>
		<? if(@$order_data['sales_settings']['address_S_telephone'] == 0 || @$order_data['sales_settings']['address_S_telephone'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_S_telephone'] == 1) $must_be = ' (*)' ?>
		<div class="form_field order_field shipping_field"><label for="order_address[S][telephone]">		<?=$this->lang->line('c_o_form_telephone')?> (*)</label><input type="text" name="order_address[S][telephone]" value="<?=@$order_data['customer_S_address_data']['telephone']?>"><div class="clear_both"></div></div>
		<? } ?>
		<div class="form_field order_field shipping_field"><label for="order_address[S][address_email]">	<?=$this->lang->line('c_o_form_email')?> (*) :</label> 	<input type="text" name="order_address[S][address_email]" value="<?=@$order_data['customer_S_address_data']['address_email']?>"><div class="clear_both"></div></div>
	</fieldset>
</div>
<?php
}
?>