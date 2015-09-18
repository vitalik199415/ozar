<script>
	$('#<?=$customers_block_id?>').gbc_customers(
	{
		url_registration_form : "<?=$this->router->build_url('customers_methods_lang', array('method' => 'registration_form', 'lang' => $this->mlangs->lang_code));?>",
		url_office_form : "<?=$this->router->build_url('customers_methods_lang', array('method' => 'office_form', 'lang' => $this->mlangs->lang_code));?>",
		url_login_form : "<?=$this->router->build_url('customers_methods_lang', array('method' => 'login_form', 'lang' => $this->mlangs->lang_code));?>", 
		url_forgot_password_form : "<?=$this->router->build_url('customers_methods_lang', array('method' => 'forgot_password_form', 'lang' => $this->mlangs->lang_code));?>",
		url_change_password_form : "<?=$this->router->build_url('customers_methods_lang', array('method' => 'change_password_form','lang' => $this->mlangs->lang_code));?>",
		url_registration_check_email : "<?=$this->router->build_url('customers_methods_lang', array('method' => 'check_isset_email', 'lang' => $this->mlangs->lang_code));?>",
		url_write_admin_form : '<?=$this->router->build_url('customers_methods_lang', array('method' => 'write_admin_form', 'lang' => $this->mlangs->lang_code));?>',
	
		error_customer_exists : "<?=$this->lang->line('c_o_error_customer_exists')?>",
		error_login_submit : "<?=$this->lang->line('login_error_js_valid_wrong_data')?>",
		error_fp_submit : "<?=$this->lang->line('forgot_password_error_js_valid_wrong_data')?>",
		error_cp_submit : "<?=$this->lang->line('change_password_error_js_valid_wrong_data')?>",
		error_submit : "<?=$this->lang->line('c_o_error_submit')?>"
	});
</script>