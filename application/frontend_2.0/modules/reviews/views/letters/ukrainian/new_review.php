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
	<div>Уважаемый администратор сайта <b><?=$data['site']?></b>.</div>
	<div style="margin:15px 0 0 0;">Добавлен новый комментарий или отзыв в модуле <b><?=$data['module_alias']?></b>.</div>
	<div style="margin:15px 0 0 0;">Данные комментария :</div>
	<div>Имя : <b><?=$data['name']?></b></div>
	<div>E-Mail : <b><?=$data['email']?></b></div>
	<div>Комментарий : <b><?=$data['review']?></b></div>
	</body>
	</html>
<?php
}
?>