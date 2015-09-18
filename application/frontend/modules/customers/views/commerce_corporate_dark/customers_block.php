<?php
if($this->session->userdata('customer_id'))
{
?>
	<div class="block">
		<a href="#" class="big_button customer" id="customer_office"><span><?=$this->session->userdata(array('CUSTOMER','name'));?></span></a><a href="<?=$this->router->build_url('customers_methods_lang', array('method' => 'logout', 'lang' => $this->mlangs->lang_code));?>" class="big_button logout"><span><?=$this->lang->line('login_logout')?></span></a>
	</div>	
<?
}
else
{
?>
	<div class="block">
		<a href="#" class="big_button enter" id="customer_login"><span><?=$this->lang->line('login_enter')?></span></a><a href="#" class="big_button register" id="customer_registration"><span><?=$this->lang->line('login_registration')?></span></a>
	</div>
<?php
}
?>