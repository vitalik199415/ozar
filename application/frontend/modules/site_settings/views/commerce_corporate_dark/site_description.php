<?php
if(count($site_description)>0 && ($site_description['company_name'] != '' || $site_description['work_name'] != ''))
{
	?>
	<div class="site_description_block">
	<div class="site_description_block_pad">	
		<div class="base_block">
			<div class="base_top">
				<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
			</div>
			<div class="base_center">
				<div class="base_center_left"></div><div class="base_center_right"></div>
				<div class="base_center_repeat">
					<div class="block">
						<?php if($site_description['company_name'] != '') { ?><h2><?=$site_description['company_name']?></h2><?php } ?>
						<?php if($site_description['work_name'] != '') { ?><h1><?=$site_description['work_name']?></h1><?php } ?>
					</div>
				</div>
			</div>
			<div class="base_bot">
				<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
			</div>
		</div>
	</div>	
	</div>
	<?
}
?>