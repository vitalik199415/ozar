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
<div>Dear website administrator <b><?=$data['site']?></b>.</div>
<div style="margin:15px 0 0 0;">A new comment to the product  <a href="<?=$data['products_url']?>"><?=$data['products_name']?></a>, number <b><?=$data['products_sku']?></b> has been added.</div>
<div style="margin:15px 0 0 0;"> Comment:</div>
<div>Name : <b><?=$data['name']?></b></div>
<div>E-mail : <b><?=$data['email']?></b></div>
<div>Comment text:<b><?=$data['message']?></b></div>
</body>
</html>
<?php
}
?>