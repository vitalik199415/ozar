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
<div>Dear Customer!</div>
<div style="margin:15px 0 0 0;">Your order, is issued on the site <b><a href="<?=site_url()?>" target="_blank"><?=$site?></a></b>, with number <?=$orders_number?> successfully confirmed.</div>
<div style="margin:15px 0 0 0;">Manager will contact you any time soon!<div>
<div style="margin:15px 0 0 0;">
<div><b>Thank you for shopping in our store!</b></div>
Sincerely administration <b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>