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
<div style="margin:15px 0 0 0;">Запит на зміну пароля з сайту <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b> успішно оброблений.</div>
<div style="margin:15px 0 0 0;">
Новий пароль : <b><?=$data['password']?></b>
</div>
<div style="margin:15px 0 0 0;">
Нагадуємо Вам що пароль ви можете змінити в будь-який момент в особистому кабінеті. Для переходу в особистий кабінет необхідно здійснити вхід на сайт ввівши E-mail і пароль до облікового запису, після чого натиснути на кнопку особистого кабінету або клікнути по посиланню з вашим ім'ям.
</div>
<div style="margin:15px 0 0 0;">
З повагою адміністрація<b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>