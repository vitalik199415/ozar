<?php
$EMAIL = 'pivasska@gmail.com'; //   Ваш e-mail на который будут приходить заявки
$FROM = 'noreplay@mail.ru';  //  e-mail отправителя (иногда требуется указать разрешенный настройками e-mail)
$REPLY = 'noreplay@mail.ru';   //  e-mail для ответа 
$PRIORITY = false;       //  пометить как важное


switch (@$_POST['action']) { // рассматриваем варианты разных значений поля action
    case 'ask_order': 
        sendmail("Вопрос с сайта", render(
                        '<div><b>Имя:</b> '.stripinput($_POST['name']).'</div>'.
                        '<div><b>Почта:</b> '.stripinput($_POST['email']).'</div>'.
                        '<div><b>Содежание вопроса:</b> '.stripinput($_POST['intro']).'</div>'
        ),$EMAIL,$FROM,$REPLY);
        exit('200');
    break;
    case 'callme_order':
        sendmail("Заказ обратного звонка", render(
                        '<div><b>Имя:</b> '.stripinput($_POST['name']).'</div>'.
                        '<div><b>Телефон:</b> '.stripinput($_POST['tel']).'</div>'.
                        '<div><b>Время звонка:</b> '.stripinput($_POST['time']).'</div>'.
                        '<div><b>Примечание:</b> '.stripinput($_POST['descr']).'</div>'
        ),$EMAIL,$FROM,$REPLY);
        exit('200');
    break;
    case 'service_order':
        sendmail("Исполнитель на корпоратив", render(
                        '<div><b>Ваша Компания:</b> '.stripinput($_POST['company']).'</div>'.
                        '<div><b>Телефон:</b> '.stripinput($_POST['tel']).'</div>'.
                        '<div><b>Количество человек:</b> '.stripinput($_POST['list']).'</div>'.
                        '<div><b>Исполнитель:</b> '.stripinput($_POST['artist']).'</div>'.
                        '<div><b>Дополнительно:</b> '.stripinput($_POST['intro']).'</div>'
        ),$EMAIL,$FROM,$REPLY);
        exit('200');
    break;
    default:
        sendmail("Заказ обратного звонка c основного сайта", render(
                        '<div><b>Имя:</b> '.stripinput($_POST['name']).'</div>'.
                        '<div><b>Телефон:</b> '.stripinput($_POST['tel']).'</div>'.
						'<div><b>E-mail:</b> '.stripinput($_POST['email']).'</div>'.
						'<div><b>Примечание:</b> '.stripinput($_POST['descr']).'</div>'
        ),$EMAIL,$FROM,$REPLY);
        exit('200');
    break;
}



header("HTTP/1.0 404 Not Found");
function stripinput($_sText) {
	if (ini_get('magic_quotes_gpc')) $_sText = stripslashes($_sText);
	$search = array("\"", "'", "\\", '\"', "\'", "<", ">", "&nbsp;");
	$replace = array("&quot;", "&#39;", "&#92;", "&quot;", "&#39;", "&lt;", "&gt;", " ");
	$_sText = str_replace($search, $replace, $_sText);
	return $_sText;
}
function sendmail($_sSubject, $_sMessage, $_sEmail, $_sFrom, $_sReply, $_bPriority=false){
	$subject = "=?utf-8?b?" . base64_encode($_sSubject) . "?=";
	$headers  = "From: $_sFrom\r\n";
	$headers .= "Reply-To: $_sReply\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
    if($_bPriority){
        $headers .= "X-Priority: 1 (Highest)\n";
        $headers .= "X-MSMail-Priority: High\n";
        $headers .= "Importance: High\n";
    }
	$headers .= "Content-type: text/html; charset=utf-8\r\n";
	return mail($_sEmail, $subject, $_sMessage, $headers);
}
function render($_sBody){
    return '<html>
<head>
<style type="text/css">
body {margin:10px;background:#ffffff;color:#000000;font-size:10pt;font-family:Tahoma;}
div {font-size:10pt;font-family:Tahoma}
.header {padding-bottom:20px}
a {color: #003399!important;text-decoration:underline;}
</style>
</head>
<body>
<div class="body">
'.$_sBody.'
</div>
</body>
</html>';
}
?>