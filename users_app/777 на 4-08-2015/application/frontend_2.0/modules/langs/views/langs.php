<?php
if(isset($langs) && count($langs) > 1)
{
	?>
	<div class="langs_block">
	<?
	//echo var_dump($langs);
	foreach($langs as $ms)
	{
		?><a <?php if($ms['href']) echo 'href="'.$ms['href'].'"'; else echo 'class="active"';?>><div id="<?=$ms['code']?>"></div></a><?
	}
	?>
	</div>
	<?
}
?>