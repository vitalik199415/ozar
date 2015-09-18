<?php
if(isset($data))
{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body{
font-family:Arial, Helvetica, sans-serif;
padding:0;
margin:0;
font-size:14px;
color:#333333;
background:#FFFFFF;
text-align:left;
}
div{
text-align:left;
}
</style>
</head>
<body>
<div>Здравствуйте <b><?=$data['name']?></b>.</div>
<div style="margin:15px 0 0 0;">Благодарим Вас за регистрацию на сайте <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b>.</div>
<div style="margin:15px 0 0 0; font-size:16px;"><b>Большая просьба!</b> Если письмо попало в папку <b>«спам»</b> будьте добры отметить его как <b>«Не спам»</b> во избежание дальнейших проблем с получением различных технических писем.</div>

<div style="margin:15px 0 0 0;">
Для подтверждения существования <b>E-mail</b> и <b>активации учетной записи</b> Вам нужно перейти по ссылке : <a href="<?=$data['link']?>" target="_blank"><?=$data['link']?></a>.
<div>При успешной активации учетной записи вы будете автоматически залогинены в системе.</div>
</div>

<div style="margin:15px 0 0 0;">
<div>Ваши данные для входа на сайт:</div>
<div>E-mail(для входа в систему) : <b><?=$data['email']?></b></div>
<div>Пароль : <b><?=$data['password']?></b></div>
</div>
<div style="margin:15px 0 0 0;">
	<div><b><?=$this->lang->line('c_o_fieldset_billing_address')?></b></div>
	<span><?=$this->lang->line('c_o_form_name')?> 	 : <b><?=@$data['addresses']['B']['name']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_country')?> : <b><?=@$data['addresses']['B']['country']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_city')?> 	 : <b><?=@$data['addresses']['B']['city']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_zip')?>	 : <b><?=@$data['addresses']['B']['zip']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_address')?> : <b><?=@$data['addresses']['B']['address']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_telephone')?> : <b><?=@$data['addresses']['B']['telephone']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_email')?> 	 : <b><?=@$data['addresses']['B']['address_email']?></b></span>
</div>
<div style="margin:15px 0 0 0;">
<div>Все данные, которые были заполнены Вами во время регистрации, будут автоматически заполнены при оформлении заказа, что ускорит процесс покупки.</div>
</div>
<div style="margin:15px 0 0 0;">
Так же Вы можете в любой момент отредактировать данные учетной записи, для этого нужно осуществить вход на сайт, и кликнуть по кнопке с Вашим именем пользователя или с надписью <b>Личный кабинет</b>.
</div>
<div style="margin:15px 0 0 0;">
С уважением администрация <b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>