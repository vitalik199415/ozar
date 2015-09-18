<script>
var $required_registration_fields = new Object(
{
	"customer[name]" : 				{required: true},
	"customer[email]" : 			{required: true, email: true, remote: {url : "<?=$this->router->build_url('customers_methods_lang', array('method' => 'check_isset_email', 'lang' => $this->mlangs->lang_code));?>", type:"post"}},
	"customer[password]" : 			{required: true, minlength: 6},
	"confirm_password" : 			{equalTo: "#customers_registration_password"}
});
<?php
foreach($registration_settings as $key => $ms)
{
	$kkey = $key;
	if(strpos($kkey, 'address_B_') !== FALSE)
	{
		$kkey = substr($kkey, 10);
		if($ms == 1)
		{
			if($kkey != 'address_email')
			{
				?>$required_registration_fields["customer_address[B][<?=$kkey?>]"] = new Object({required: true});<?
			}
			else
			{
				?>$required_registration_fields["customer_address[B][<?=$kkey?>]"] = new Object({required: true, email: true});<?
			}
		}
		
	}
}
?>
$required_registration_fields["captcha"] = new Object({required: true, minlength: 6});
$('#<?=$registration_form_id?>').gbc_customers('init_customers_registration', $required_registration_fields);
</script>