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
<div style="margin:15px 0 0 0;">Запрос на изменение пароля с сайта <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b> успешно обработан.</div>
<div style="margin:15px 0 0 0;">
Новый пароль : <b><?=$data['password']?></b>
</div>
<div style="margin:15px 0 0 0;">
Напоминаем Вам что пароль вы можете изменить в любой момент в личном кабинете. Для перехода в личный кабинет необходимо осуществить вход на сайт введя E-mail и пароль вашей учетной записи, после чего нажать на кнопку личного кабинета или кликнуть по ссылке с вашим именем.
</div>
<div style="margin:15px 0 0 0;">
С уважением администрация <b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>