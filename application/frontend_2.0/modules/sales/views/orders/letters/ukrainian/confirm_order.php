<?php
if(isset($orders_number))
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
<div>Шановний покупець!</div>
<div style="margin:15px 0 0 0;">Ваше замовлення, оформлене на сайті <b><a href="<?=site_url()?>" target="_blank"><?=$site?></a></b>, з номером <?=$orders_number?> успішно підтверджено.</div>
<div style="margin:15px 0 0 0;">Менеджер зв'яжеться з вами найближчим часом!<div>
<div style="margin:15px 0 0 0;">
<div><b>Дякуємо за покупку в нашому магазині!</b></div>
З повагою адміністрація <b><?=$site?></b>.
</div>
</body>
</html>
<?	
}
?>