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
<div>Dear administrator, You have a new registered customer.</div>

<div style="margin:15px 0 0 0;">
<div><b>Customer data:</b></div>
<div>Name : <b><?=$data['name']?></b></div>
<div>E-mail : <b><?=$data['email']?></b></div>
</div>
<div style="margin:15px 0 0 0;">
	<div><b>Billing address</b></div>
	<div><span>Name 	 : <b><?=@$data['addresses']['B']['name']?></b></span></div>
	<div><span>Country : <b><?=@$data['addresses']['B']['country']?></b></span></div>
	<div><span>City 	 : <b><?=@$data['addresses']['B']['city']?></b></span></div>
	<div><span>Zip	 : <b><?=@$data['addresses']['B']['zip']?></b></span></div>
	<div><span>Address : <b><?=@$data['addresses']['B']['address']?></b></span></div>
	<div><span>Telephone : <b><?=@$data['addresses']['B']['telephone']?></b></span></div>
	<div><span>Email 	 : <b><?=@$data['addresses']['B']['address_email']?></b></span></div>
</div>
</body>
</html>
<?	
}
?>