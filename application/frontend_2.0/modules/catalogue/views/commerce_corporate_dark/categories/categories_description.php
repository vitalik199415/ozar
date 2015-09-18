<?php
if(isset($description) && trim($description) != '')
{
	?>
	<div class="categorie_description_block">
		<div class="base_block">
			<div class="base_top">
				<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
			</div>
			<div class="base_center">
				<div class="base_center_left"></div><div class="base_center_right"></div>
				<div class="base_center_repeat">
					<div class="block">
					<?=$description?>
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