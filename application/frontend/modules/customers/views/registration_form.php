<div class="form_block customer_registration_form">
<form enctype="multipart/form-data" action="<?=$this->router->build_url('customers_methods_lang', array('method' => 'registration','lang' => $this->mlangs->lang_code));?>" method="post" id="<?=$registration_form_id?>">
	<div class="form_label registration_label"><span><?=$this->lang->line('c_o_form_customers_register')?></span></div>
	<div class="form_help registration_help">
	<span><?=$this->lang->line('c_o_help_required_fields')?></span>
	</div>
	
	<div class="login_block">
		<fieldset id="customer_email_pass" class="form_fieldset">
		<legend><?=$this->lang->line('c_o_fieldset_base_data')?></legend>
			<div class="form_field registration_field"><label for="customer[name]">				<?=$this->lang->line('c_o_form_b_name')?> 				(*) :</label><input type="text" name="customer[name]"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="customer[email]">			<?=$this->lang->line('c_o_form_b_email')?> 				(*) :</label><input type="text" name="customer[email]"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="customer[password]">			<?=$this->lang->line('c_o_form_b_password')?> 			(*) :</label><input type="password" name="customer[password]" id="customers_registration_password"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="confirm_password">			<?=$this->lang->line('c_o_form_b_confirm_password')?> 	(*) :</label><input type="password" name="confirm_password"><div class="clear_both"></div></div>
		</fieldset>
		<div class="form_help registration_help">
		<span><?=$this->lang->line('c_o_help_fill_address')?></span>
		</div>
	</div>
	
	<div class="billing_block" id="registration_billing_block">
	<div class="billing_address_block">
		<fieldset id="customer_address_b_fieldset" class="form_fieldset">
		<legend><?=$this->lang->line('c_o_fieldset_billing_address')?></legend>
			<div class="form_field registration_field"><label for="customer_address[B][name]">				<?=$this->lang->line('c_o_form_name')?> 	<?php if($registration_settings['address_B_name'] == 1) echo '(*) ';?>:</label><input type="text" name="customer_address[B][name]"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="customer_address[B][country]">			<?=$this->lang->line('c_o_form_country')?> 	<?php if($registration_settings['address_B_country'] == 1) echo '(*) ';?>:</label><input type="text" name="customer_address[B][country]"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="customer_address[B][city]">				<?=$this->lang->line('c_o_form_city')?> 	<?php if($registration_settings['address_B_city'] == 1) echo '(*) ';?>:</label><input type="text" name="customer_address[B][city]"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="customer_address[B][zip]">				<?=$this->lang->line('c_o_form_zip')?> 		<?php if($registration_settings['address_B_zip'] == 1) echo '(*) ';?>:</label><input type="text" name="customer_address[B][zip]"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="customer_address[B][address]">			<?=$this->lang->line('c_o_form_address')?> 	<?php if($registration_settings['address_B_address'] == 1) echo '(*) ';?>:</label><input type="text" name="customer_address[B][address]"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="customer_address[B][telephone]">			<?=$this->lang->line('c_o_form_telephone')?> 	<?php if($registration_settings['address_B_telephone'] == 1) echo '(*) ';?>:</label><input type="text" name="customer_address[B][telephone]"><div class="clear_both"></div></div>
			<div class="form_field registration_field"><label for="customer_address[B][address_email]">		<?=$this->lang->line('c_o_form_email')?> 	<?php if($registration_settings['address_B_address_email'] == 1) echo '(*) ';?>:</label><input type="text" name="customer_address[B][address_email]"><div class="clear_both"></div></div>
		</fieldset>
	</div>
	</div>
	
	<div class="form_item_block">
	<div class="form_item_inside_block">
	<div class="captcha">
		<div class="form_field registration_field">
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
	
	<div class="form_button registration_button"><a href="#" id="submit"><span><?=$this->lang->line('c_o_form_registration_submit')?></span></a></div>
</form>
</div>