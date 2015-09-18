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
</style>
</head>
<body>
<div>Уважаемый(ая) <b><?=$name?></b>.</div>
<div style="margin:10px 0 0 0;">Благодарим за регистрацию и интерес к нашему интернет-ресурсу.</div>
<div style="margin:10px 0 0 0;">
Администратор добавил Вас к ниже перечисленным группам пользователей.
</div>
<div style="margin:10px 0 0 0;">
<?php foreach($groups as $val):?>
<div style="margin:10px 0 0 0;">Группа : <b><?=$val['name']?></b><br><?=$val['description']?></div>
<?php endforeach; ?>
</div>

<div style="margin:20px 0 0 0;">
Напоминаем Вам, чтоб осуществить вход на сайт необходимо ввести E-Mail (<?=$email?>) и пароль, указанные при регистрации.
</div>

<div style="margin:25px 0 0 0;">
С уважением администрация <a href="http://<?=$domain?>" target="_blank"><b><?=$domain?></b></a>.
</div>
</body>
</html>