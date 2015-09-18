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
<div>Уважаемый покупатель!</div>
<div style="margin:15px 0 0 0;">Ваш заказ, оформленный на сайте <b><a href="<?=site_url()?>" target="_blank"><?=$site?></a></b>, с номером <?=$orders_number?> успешно подтвержден.</div>
<div style="margin:15px 0 0 0;">Менеджер свяжется с вами в ближайшее время!<div>
<div style="margin:15px 0 0 0;">
<div><b>Благодарим за покупку в нашем магазине!</b></div>
С уважением администрация <b><?=$site?></b>.
</div>
</body>
</html>
<?	
}
?>