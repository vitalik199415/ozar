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
    <div>Здравствуйте <b> уважаемый <?=$name?>.</div>
    <div style="margin:15px 0 0 0;"> <h3>Новость дня!</h3> Желаете скидку на покупку товаров в интернет-магазине <a href="http://<?=$domain?>"><?=$domain?></a>? Воспользуйтесь уникальным купоном из этого письма до <b><?=$date_to?></b> во время оформления заказа и радуйтесь сэкономленным средствам!</div>
    <div style="margin:15px 0 0 0;"><?=$message?></div>
    <div style="margin:15px 0 0 0;">
        Ваш промокод на скидку: <b><?=$number?></b>.
    </div>
    <div style="margin:15px 0 0 0;">
        Чтобы получить более подробную информацию о купоне, перейдите по следующему <a href="http://<?=$domain?>">адресу</a> и введите Ваш промокод в соответсвующее поле.
    </div>
    <div style="margin:15px 0 0 0;">Благодарим за интерес к сайту.</div>
    <div style="margin:15px 0 0 0;">
        С уважением администрация <b><?=$domain?></b>.
    </div>
</body>
</html>