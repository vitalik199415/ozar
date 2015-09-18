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
<div>Шановний <b><?=$data['name']?></b>.</div>
<div style="margin:15px 0 0 0;">Дякуємо Вам за реєстрацію на сайті <b><a href="<?=site_url()?>" target="_blank"><?=$data['site']?></a></b>.</div>

<div style="margin:15px 0 0 0;">
Для підтвердження існування <b>E-mail</b> і <b>активації облікового запису</b> Вам потрібно перейти по посиланню: <a href="<?=$data['link']?>" target="_blank"><?=$data['link']?></a>.
<div>При успішній активації облікового запису ви будете автоматично залогінені в системі.</div>
</div>

<div style="margin:15px 0 0 0;">
<div>Ваші дані для входу на сайт:</div>
<div>E-mail(для входу в систему) : <b><?=$data['email']?></b></div>
<div>Пароль : <b><?=$data['password']?></b></div>
</div>
<div style="margin:15px 0 0 0;">
	<div><b><?=$this->lang->line('c_o_fieldset_billing_address')?></b></div>
	<span><?=$this->lang->line('c_o_form_name')?> 	 : <b><?=@$data['addresses']['B']['name']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_country')?> : <b><?=@$data['addresses']['B']['country']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_city')?> 	 : <b><?=@$data['addresses']['B']['city']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_zip')?>	 : <b><?=@$data['addresses']['B']['zip']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_address')?> : <b><?=@$data['addresses']['B']['address']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_telephone')?> : <b><?=@$data['addresses']['B']['telephone']?></b>, </span>
	<span><?=$this->lang->line('c_o_form_email')?> 	 : <b><?=@$data['addresses']['B']['address_email']?></b></span>
</div>
<div style="margin:15px 0 0 0;">
<div>Всі дані, які були заповнені Вами під час реєстрації, будуть автоматично заповнені при оформленні замовлення, що прискорить процес заповнення форми.</div>
</div>
<div style="margin:15px 0 0 0;">
Так само Ви можете в будь-який момент відредагувати дані облікового запису, для цього потрібно здійснити вхід на сайт, і клікнути по кнопці з Вашим ім'ям користувача або з написом <b>Особистий кабінет</b>.
</div>
<div style="margin:15px 0 0 0;">
З повагою адміністрація<b><?=$data['site']?></b>.
</div>
</body>
</html>
<?	
}
?>