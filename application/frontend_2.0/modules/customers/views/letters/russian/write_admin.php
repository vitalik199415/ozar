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
<div>Уважаемый администратор.</div>
<div style="margin:15px 0 0 0;">У вас новое сообщение с сайта <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b>.</div>
<div style="margin:15px 0 0 0;">
Текст сообщения :
</div>
<div style="margin:15px 0 0 0;">
<?=$data['message']?>
</div>
<div style="margin:15px 0 0 0;">
Сообщение от пользователя <b><?=$data['name']?></b> <?=$data['email']?>.
</div>
</body>
</html>
<?	
}
?>