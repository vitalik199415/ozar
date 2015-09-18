<?php
if(isset($textpage) && count($textpage)>0)
{
?>
<div class="modules_block textpages_block">
<div class="base_block">
	<div class="base_top">
		<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
	</div>
	<div class="base_center">
		<div class="base_center_left"></div><div class="base_center_right"></div>
		<div class="base_center_repeat">
			<div class="block">
<?
	foreach($textpage as $ms)
	{
		?>
			<div><h2><?=$ms['name']?></h2></div>
			<div><?=$ms['text']?></div>
		<?
	}
?>
			<div class="clear_both"></div>
			</div>
		</div>
	</div>	
	<div class="base_bot">
		<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
	</div>
</div>
</div>
<?
}
?>