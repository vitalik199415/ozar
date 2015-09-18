<?php
if(isset($langs) && count($langs) > 1)
{
	?>
	<div class="langs_block">
	<?
	foreach($langs as $ms)
	{
		?><a <?php if($ms['href']) echo 'href="'.$ms['href'].'"'; else echo 'class="active"';?>><span><?=$ms['short_name']?></span></a><?
	}
	?>
	</div>
	<?
}
?>