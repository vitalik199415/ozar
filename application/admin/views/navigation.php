<?php
if(isset($navigation) && is_array($navigation))
{
?>
<div align="center">
<div class="navigation">
<?
	$st = false;
	foreach($navigation as $ms)
	{
		if($st)
		{
			echo '<span class="arrow_right"></span>';
		}
		if($ms['href'])
		{	
			echo anchor($ms['href'],$ms['name'],$ms['options']);
		}
		else
		{
		?>
			<span><?=$ms['name']?></span>
		<?
		}
		$st = true;
	}
?>
<div class="CB"></div>
</div>
</div>
<?	
}
?>