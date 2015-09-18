<?php
session_name('GBCSESSION');
session_start();
include('kcaptcha.php');
$captcha = new KCAPTCHA();
if($_REQUEST[session_name()]){
	if($_REQUEST['session_key'])
	{
		$_SESSION[$_REQUEST['session_key']] = $captcha->getKeyString();
	}
	else
	{
		$_SESSION['captcha_keystring'] = $captcha->getKeyString();
	}	
}

?>