<?php
if(isset($customer_edit_data))
{
?>
<form enctype="multipart/form-data" action="<?=$this->router->build_url('customers_methods_lang', array('method' => 'registration','lang' => $this->mlangs->lang_code));?>" method="post" id="<?=$customer_edit_form_id?>">
<div class="form_block customer_registration_form customer_office_form">
	<div class="form_label office_label"><?=$this->lang->line('c_o_form_customers_office')?></div>
	<div class="form_help registration_help office_help">
	<span><?=$this->lang->line('c_o_help_required_fields')?></span>
	</div>
	
	<div class="login_block">
		<fieldset id="customer_email_pass" class="form_fieldset">
		<legend><?=$this->lang->line('c_o_fieldset_base_data')?></legend>
		<div class="form_field registration_field office_field"><label for="customer[name]"><?=$this->lang->line('c_o_form_b_name')?> (*):</label><input type="text" name="customer[name]" value="<?=$customer_edit_data['customer']['name']?>"><div class="clear_both"></div></div>

		<div class="form_button registration_button office_button registration_change_password"><a href="#" id="change_password"><span><?=$this->lang->line('c_o_login_submit_change_password')?></span></a> <a href="#" id="write_admin"><span><?=$this->lang->line('wa_label')?></span></a></div>
		</fieldset>
		<div class="form_help registration_help office_help">
		<span><?=$this->lang->line('c_o_help_fill_address')?></span>
		</div>
	</div>
	
	<div class="billing_block" id="office_billing_block">
	<div class="billing_address_block">
		<fieldset id="customer_address_b_fieldset" class="form_fieldset">
		<legend><?=$this->lang->line('c_o_fieldset_billing_address')?></legend>
			<div class="form_field registration_field office_field"><label for="customer_address[B][name]">				<?=$this->lang->line('c_o_form_name')?> 	:</label><input type="text" name="customer_address[B][name]" value="<?=@$customer_edit_data['customer_address']['B']['name']?>"><div class="clear_both"></div></div>
			<div class="form_field registration_field office_field"><label for="customer_address[B][country]">			<?=$this->lang->line('c_o_form_country')?> 	:</label><input type="text" name="customer_address[B][country]" value="<?=@$customer_edit_data['customer_address']['B']['country']?>"><div class="clear_both"></div></div>
			<div class="form_field registration_field office_field"><label for="customer_address[B][city]">				<?=$this->lang->line('c_o_form_city')?> 	:</label><input type="text" name="customer_address[B][city]" value="<?=@$customer_edit_data['customer_address']['B']['city']?>"><div class="clear_both"></div></div>
			<div class="form_field registration_field office_field"><label for="customer_address[B][zip]">				<?=$this->lang->line('c_o_form_zip')?> 		:</label><input type="text" name="customer_address[B][zip]" value="<?=@$customer_edit_data['customer_address']['B']['zip']?>"><div class="clear_both"></div></div>
			<div class="form_field registration_field office_field"><label for="customer_address[B][address]">			<?=$this->lang->line('c_o_form_address')?> 	:</label><input type="text" name="customer_address[B][address]" value="<?=@$customer_edit_data['customer_address']['B']['address']?>"><div class="clear_both"></div></div>
			<div class="form_field registration_field office_field"><label for="customer_address[B][telephone]">		<?=$this->lang->line('c_o_form_telephone')?> 	:</label><input type="text" name="customer_address[B][telephone]" value="<?=@$customer_edit_data['customer_address']['B']['telephone']?>"><div class="clear_both"></div></div>
			<div class="form_field registration_field office_field"><label for="customer_address[B][address_email]">	<?=$this->lang->line('c_o_form_email')?> 	:</label><input type="text" name="customer_address[B][address_email]" value="<?=@$customer_edit_data['customer_address']['B']['address_email']?>"><div class="clear_both"></div></div>
		</fieldset>
	</div>
	</div>
	
	<div class="form_item_block">
	<div class="form_item_inside_block">
	<div class="captcha">
		<div class="form_field registration_field office_field">
			<label for="captcha">
			<?=$this->lang->line('c_o_form_enter_captcha')?> (*) :
			<img src="/additional_libraries/kcaptcha/check.php?<?php echo session_name()?>=<?php echo session_id()?>&rand=<?=rand(1000, 9999)?>" id="customer_registration_captcha_img">
			</label><input type="text" name="captcha"><div class="clear_both"></div>
		</div>	
	</div>
	</div>
	</div>
	
	<div class="form_message_block" id="customers_message_block">
		<div class="error_message">
			<div></div>
		</div>
		<div class="success_message">
			<div></div>
		</div>
	</div>
	
	<div class="form_button registration_button office_button"><a href="#" id="submit"><span><?=$this->lang->line('c_o_form_registration_submit_edit')?></span></a></div>
</div>
</form>
<?php
}
?>