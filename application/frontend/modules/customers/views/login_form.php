<form enctype="multipart/form-data" action="<?=$this->router->build_url('customers_methods_lang', array('method' => 'login','lang' => $this->mlangs->lang_code));?>" method="post" id="<?=$login_form_id?>">
<div align="center" class="form_block customer_login_form">
	<div class="form_label customer_login_label"><span><?=$this->lang->line('login_label')?></span></div>
	<div class="form_item_block customer_login_block">
	<div class="form_item_inside_block">
			<div class="form_field customer_login_field login_field"><label for="email">		<?=$this->lang->line('login_email')?> 				:</label><input type="text" name="email"><div class="clear_both"></div></div>
			<div class="form_field customer_login_field login_field"><label for="password">		<?=$this->lang->line('login_password')?> 			:</label><input type="password" name="password"><div class="clear_both"></div></div>
		
		<div class="registration_forgot_password_buttons"><a href="#" class="registration_button" id="customer_registration"><span><?=$this->lang->line('login_registration')?></span></a><a href="#" class="forgot_password_button" id="customer_forgot_password"><span><?=$this->lang->line('login_forgot_password')?></span></a><div class="clear_both"></div></div>
		
		<div class="form_message_block" id="customers_message_block">
			<div class="error_message">
				<div></div>
			</div>
			<div class="success_message">
				<div></div>
			</div>
		</div>
		<div class="form_button customer_login_button"><a href="#" id="submit"><span><?=$this->lang->line('login_enter')?></span></a></div>
	</div>
	</div>
</div>	
</form>