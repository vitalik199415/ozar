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
p{
margin:0;
padding:0;
}
</style>
</head>
<body>
<div>Здравствуйте <b><?=$data['name']?></b>.</div>
<div style="margin:15px 0 0 0;">Вы оставляли комментарий к продукту <a href="<?=$data['product_url']?>"><b><?=$data['product_name']?></b></a> с артикулом <?=$data['product_sku']?> на сайте <?=$data['site']?>.</div>
<div style="margin:15px 0 0 0;">Администратор ответил на Ваш комментарий.</div>
<div style="margin:15px 0 0 0;">Ваш комментарий :</div>
<div><?=$data['message']?></div>
<div style="margin:15px 0 0 0;">Ответ администратора :</div>
<div><?=$data['answer']?></div>

<div style="margin:15px 0 0 0;">Для просмотра комментария и ответа на сайте перейдите по <a href="<?=$data['product_url']?>">ссылке >></a>.</div>

<div style="margin:15px 0 0 0;">
С уважением администрация <b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>