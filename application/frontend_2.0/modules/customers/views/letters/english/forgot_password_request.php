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
<div style="margin:15px 0 0 0;">On the site <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b> was made a request to change the account password, which is registered to this E-mail. If you not made ​​a request to change the password, please ignore this email.</div>
<div style="margin:15px 0 0 0;">
To change the password, you must go to the link below and you will receive another letter, which will contain the new password to your account.
<div><a href="<?=$data['link']?>" target="_blank"><?=$data['link']?></a></div>
</div>
<div style="margin:15px 0 0 0;">
We remind you that you can change your password at any time in your account. To go to your account you must log on to the site by entering the E-mail and password for your account, and then click on the account button or the click on the link with your name.
</div>
<div style="margin:15px 0 0 0;">
Administration of<b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>