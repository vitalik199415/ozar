<form enctype="multipart/form-data" action="<?=$this->router->build_url('customers_methods_lang', array('method' => 'forgot_password', 'lang' => $this->mlangs->lang_code));?>" method="post" id="<?=$customer_fp_form_id?>">
<div align="center" class="form_block customer_fp_form">
	<div class="form_label customer_fp_label"><span><?=$this->lang->line('forgot_password_label')?><span></div>
	<div class="form_item_block customer_fp_block">
	<div class="form_item_inside_block">	
		<div class="form_help fp_help"><span><?=$this->lang->line('forgot_password_help')?></span></div>
			
		<div class="form_field fp_field"><label for="email"><?=$this->lang->line('login_email')?> :</label><input type="text" name="email"><div class="clear_both"></div></div>
		<div class="captcha">
			<div class="form_field fp_field">
				<label for="captcha">
				<?=$this->lang->line('forgot_password_captcha')?> :
				<img src="/additional_libraries/kcaptcha/check.php?<?php echo session_name()?>=<?php echo session_id()?>&rand=<?=rand(1000, 9999)?>" id="customer_forgot_password_captcha_img">
				</label><input type="text" name="captcha"><div class="clear_both"></div>
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
		
		<div class="form_button customer_fp_button"><a href="#" id="submit"><span><?=$this->lang->line('forgot_password_submit')?></span></a></div>
	</div>
	</div>
</div>	
</form>