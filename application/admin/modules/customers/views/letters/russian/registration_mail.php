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
<div style="margin:15px 0 0 0;">Администратор сайта <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b> зарегистрировал Вас в системе.</div>

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
	<div><b>Данные плательщика</b></div>
	<span>Имя, фамилия 	 : <b><?=@$data['addresses']['B']['name']?></b>, </span>
	<span>Страна : <b><?=@$data['addresses']['B']['country']?></b>, </span>
	<span>Город : <b><?=@$data['addresses']['B']['city']?></b>, </span>
	<span>Индекс : <b><?=@$data['addresses']['B']['zip']?></b>, </span>
	<span>Адрес : <b><?=@$data['addresses']['B']['address']?></b>, </span>
	<span>Телефон : <b><?=@$data['addresses']['B']['telephone']?></b>, </span>
	<span>E-Mail : <b><?=@$data['addresses']['B']['address_email']?></b></span>
</div>
<div style="margin:15px 0 0 0;">
Вы можете в любой момент отредактировать данные учетной записи и сменить пароль, для этого нужно осуществить вход на сайт, и кликнуть по кнопке с Вашим именем пользователя или с надписью <b>Личный кабинет</b>.
</div>
<div style="margin:15px 0 0 0;">
С уважением администрация <b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>