<?php
if(isset($langs) && count($langs) > 1)
{
	?>
	<div class="language_block">
		<div class="base_block">
		<div class="base_top">
			<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
		</div>
		<div class="base_center">
			<div class="base_center_left"></div><div class="base_center_right"></div>
			<div class="base_center_repeat">
				<div class="block">
				<?
				foreach($langs as $ms)
				{
					?><a <?php if($ms['href']) echo 'href="'.$ms['href'].'"'; else echo 'class="active"';?>><span><?=$ms['short_name']?></span></a><?
				}
				?>
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