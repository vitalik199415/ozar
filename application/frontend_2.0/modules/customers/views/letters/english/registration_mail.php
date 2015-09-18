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
<div>Hello <b><?=$data['name']?></b>.</div>
<div style="margin:15px 0 0 0;">Thank you for registering on <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b>.</div>
<div style="margin:15px 0 0 0;">
<div>Your login details to the site:</div>
<div>E-mail : <b><?=$data['email']?></b></div>
<div>Password : <b><?=$data['password']?></b></div>
</div>
<div style="margin:15px 0 0 0;">
To confirm the existence <b>E-mail</b> and <b>activate your account</b> You need to link to:<a href="<?=$data['link']?>" target="_blank"><?=$data['link']?></a>.
<div>When successfully activated your account you will be automatically logged in to the system.</div>
</div>
<div style="margin:15px 0 0 0;">
<div>All data that has been filled by you during the registration process will be automatically filled in at checkout, which will speed up the ordering process.</div>
</div>
<div style="margin:15px 0 0 0;">
Also, you can always edit your account information, you just need to log on to the site and click on the button with your user name or button <b>Personal account</b>.
</div>
<div style="margin:15px 0 0 0;">
Administration of <b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>