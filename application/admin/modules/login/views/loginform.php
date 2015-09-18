<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/gbccss/loginform.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="/favicon.ico" />
<title>GBC catalogue - USER LOGIN</title>
</head>
<body>
<div align="center" style="margin:70px 0 0 0;">
	<div align="center"><a href="/" class="submit"><p>gbc.net.ua</p></a></div><br /><br />
	<div class="logo"></div>
	<?php
	if(isset($ErrorArray))
		{
			?>
				<div class="errors">
					<div>
					<?php
						foreach($ErrorArray as $ms)
							{
								?>
									<p align="left"><?=$ms?></p>
								<?php
							}
					?>
					</div>
				</div>
			<?php
		}
	?>
	<?=form_open('login/auth', array('name' => 'form'));?>
	<table class="formtable" cellpadding="4" cellspacing="0" border="0">
	<tr>
		<td align="left" valign="middle">Login:</td>
		<td align="right"><input type="text" name="login" /></td>
	</tr>
	<tr>
		<td align="left" valign="middle">Password:</td>
		<td align="right"><input type="password" name="password" /></td>
	</tr>
	<tr>
		<td align="left"><a href="/register/" class="fp">Registration</a></td>
		<td align="right"><a href="<?=site_url("login/forgotpassword")?>" class="fp">Forgot Password?</a></td>
	</tr>
	<tr>
		<td colspan="2" height="70" valign="bottom" align="center"><a href="javascript:document.form.submit()" class="submit"><p>LOGIN</p></a></td>
	</tr>
	</table>
	<?=form_close();?>
</div>
</body>
</head>