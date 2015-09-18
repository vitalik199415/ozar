<?php
if(isset($product))
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
p{
margin:0;
padding:0;
}
</style>
</head>
<body>
<div>Здравствуйте <b> уважаемый покупатель.</div>
<div style="margin:15px 0 0 0;">Просматриваемый вами продукт <a href="<?=$product['site']?>/product-<?=$product['PR_ID']?>"><b><?=$product['name']?></b></a> с артикулом <?=$product['sku']?> на сайте <?=$product['site']?> появился в наличии.<BR> Вы можете перейти на страницу продукта для дальнейших действий.</div>
<div style="margin:15px 0 0 0;">Благодарим за интерес к сайту.</div>

<div style="margin:15px 0 0 0;">
С уважением администрация <a href="<?=$product['site']?>"><b><?=$product['site']?></b></a>.
</div>
</body>
</html>
<?
}
?>