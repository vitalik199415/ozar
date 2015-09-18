<?php
if(isset($textpage) && count($textpage)>0)
{
?>
<div class="textpages_block">
<?
	foreach($textpage as $ms)
	{
		?>
		<div class="block">
			<div><h2><?=$ms['name']?></h2></div>
			<div><?=$ms['text']?></div>
		</div>
		<?
	}
?>
</div>
<?
}
?>