<?php
if($customer != FALSE)
{
	?><div align="right" style="color:#FFFFFF; font-size:15px;">Покупатель зарегистрированый пользователь - <a href="#" target="_blank"><?=$customer['name'].' '.$customer['email']?></a></div><?
}
else
{
	?><div align="right" style="color:#FFFFFF; font-size:15px;">Покупатель обычный пользователь - плательщик <?=$addresses['B']['name'].' '.$addresses['B']['address_email']?></div><?
}
?>