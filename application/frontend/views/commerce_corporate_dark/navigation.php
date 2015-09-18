<?php
if($navigation = $this->variables->get_vars('navigation_array'))
{
?>
<div class="navigation_block" align="center">
	<div class="base_block block">
		<div class="base_top">
			<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
		</div>
		<div class="base_center">
			<div class="base_center_left"></div><div class="base_center_right"></div>
			<div class="base_center_repeat">
				<?php
				foreach($navigation['navigation'] as $nav)
				{
				?>
				<div class="nav_block">
					<a href="<?=$this->router->build_url('index', array('lang' => $this->mlangs->lang_code));?>" class="no_arrow"><span><?=$this->lang->line('base_home_text')?></span></a>
					<?php
					foreach($nav as $ms)
					{
					?>
						<a <?php if($ms[0]) echo 'href="'.$ms[0].'"'; else echo 'class="no_url"';?>><span><?=$ms[1]?></span></a>
					<?
					}
					?>
				</div>
				<?php
				}
				?>
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