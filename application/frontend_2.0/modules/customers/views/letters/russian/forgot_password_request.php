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
<div style="margin:15px 0 0 0;">На сайте <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b> был осуществлен запрос на смену пароля учетной записи, которая зарегистрирована на данный E-mail. Если вы не осуществляли запрос на смену пароля, то просим Вас проигнорировать данное письмо.</div>
<div style="margin:15px 0 0 0;">
Для изменения пароля вам необходимо перейти по указанной ниже ссылке, после чего вы получите еще одно письмо, в котором будет указан новый пароль для входа на сайт.
<div><a href="<?=$data['link']?>" target="_blank"><?=$data['link']?></a></div>
</div>
<div style="margin:15px 0 0 0;">
Напоминаем Вам, что пароль вы можете изменить в любой момент в личном кабинете. Для перехода в личный кабинет необходимо осуществить вход на сайт введя E-mail и пароль вашей учетной записи, после чего нажать на кнопку личного кабинета или кликнуть по ссылке с вашим именем.
</div>
<div style="margin:15px 0 0 0;">
С уважением администрация <b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>