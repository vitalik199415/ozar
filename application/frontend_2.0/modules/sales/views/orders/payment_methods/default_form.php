<div class="billing_block" id="order_billing_block">
<div class="billing_address_block">
	<fieldset id="customer_address_b_fieldset" class="form_fieldset order_billing_fieldset">
	<legend><?=$this->lang->line('c_o_fieldset_billing_address')?></legend>
		<div class="form_field order_field billing_field"><label for="order_address[B][name]">				<?=$this->lang->line('c_o_form_name')?> (*) :</label> 	<input type="text" name="order_address[B][name]" value="<?=@$order_data['customer_B_address_data']['name']?>"><div class="clear_both"></div></div>
		
		<? if(@$order_data['sales_settings']['address_B_country'] == 0 || @$order_data['sales_settings']['address_B_country'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_B_country'] == 1) $must_be = ' (*)' ?>
			<div class="form_field order_field billing_field"><label for="order_address[B][country]">		<?=$this->lang->line('c_o_form_country').$must_be?> :</label><input type="text" name="order_address[B][country]" value="<?=@$order_data['customer_B_address_data']['country']?>"><div class="clear_both"></div></div>
		<? } ?>

		<? if(@$order_data['sales_settings']['address_B_city'] == 0 || @$order_data['sales_settings']['address_B_city'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_B_city'] == 1) $must_be = ' (*)' ?>
			<div class="form_field order_field billing_field"><label for="order_address[B][city]">			<?=$this->lang->line('c_o_form_city').$must_be?> :</label> 	<input type="text" name="order_address[B][city]" value="<?=@$order_data['customer_B_address_data']['city']?>"><div class="clear_both"></div></div>
		<? } ?>

		<? if(@$order_data['sales_settings']['address_B_zip'] == 0 || @$order_data['sales_settings']['address_B_zip'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_B_zip'] == 1) $must_be = ' (*)' ?>
			<div class="form_field order_field billing_field"><label for="order_address[B][zip]">			<?=$this->lang->line('c_o_form_zip').$must_be?></label> 	<input type="text" name="order_address[B][zip]" value="<?=@$order_data['customer_B_address_data']['zip']?>"><div class="clear_both"></div></div>
		<? } ?>

		<? if(@$order_data['sales_settings']['address_B_address'] == 0 || @$order_data['sales_settings']['address_B_address'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_B_address'] == 1) $must_be = ' (*)' ?>
			<div class="form_field order_field billing_field"><label for="order_address[B][address]">		<?=$this->lang->line('c_o_form_address').$must_be?> :</label><input type="text" name="order_address[B][address]" value="<?=@$order_data['customer_B_address_data']['address']?>"><div class="clear_both"></div></div>
		<? } ?>

		<? if(@$order_data['sales_settings']['address_B_telephone'] == 0 || @$order_data['sales_settings']['address_B_telephone'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_B_telephone'] == 1) $must_be = ' (*)' ?>
			<div class="form_field order_field billing_field"><label for="order_address[B][telephone]">		<?=$this->lang->line('c_o_form_telephone').$must_be?> :</label><input type="text" name="order_address[B][telephone]" value="<?=@$order_data['customer_B_address_data']['telephone']?>"><div class="clear_both"></div></div>
		<? } ?>

		<? if(@$order_data['sales_settings']['address_B_skype'] == 0 || @$order_data['sales_settings']['address_B_skype'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_B_skype'] == 1) $must_be = ' (*)' ?>
			<div class="form_field order_field billing_field"><label for="order_address[B][skype]">		<?=$this->lang->line('c_o_form_skype').$must_be?> :</label><input type="text" name="order_address[B][skype]" value="<?=@$order_data['customer_B_address_data']['skype']?>"><div class="clear_both"></div></div>
		<? } ?>

		<? if(@$order_data['sales_settings']['address_B_viber'] == 0 || @$order_data['sales_settings']['address_B_viber'] == 1){ $must_be = '' ?>
			<? if(@$order_data['sales_settings']['address_B_viber'] == 1) $must_be = ' (*)' ?>
			<div class="form_field order_field billing_field"><label for="order_address[B][viber]">		<?=$this->lang->line('c_o_form_viber').$must_be?> :</label><input type="text" name="order_address[B][viber]" value="<?=@$order_data['customer_B_address_data']['viber']?>"><div class="clear_both"></div></div>
		<? } ?>

		<div class="form_field order_field billing_field"><label for="order_address[B][address_email]">		<?=$this->lang->line('c_o_form_email')?> (*) :</label> 	<input type="text" name="order_address[B][address_email]" value="<?=@$order_data['customer_B_address_data']['address_email']?>"><div class="clear_both"></div></div>
	</fieldset>
</div>
<?
if(isset($order_data['payment_methods_select']))
{
?>
<div class="form_item_block cart_product_block">
<div class="form_item_inside_block">
	<div class="form_field order_field payment_methods_select"><label for="payment_method_select"><?=$this->lang->line('c_o_form_select_payment_method')?> (*) :</label><?=form_dropdown('order_payment_method_select', $order_data['payment_methods_select'], $order_data['payment_methods_select_active'], 'id = "order_select_payment_method"')?><div class="clear_both"></div></div>
	<div class="form_help order_help payment_methods_select_desc" id="payment_methods_description"><span><?=$order_data['payment_methods_data']['description']?></span></div>
</div>
</div>
<?
}
?>
</div>