<?php
if($navigation = $this->variables->get_vars('navigation_array'))
{
?>
<div class="navigation_block">
	<div class="block">
	<?php
	foreach($navigation['navigation'] as $nav)
	{
	?>
		
		<a href="<?=base_url()?>" class="no_arrow"><span><?=$this->lang->line('base_home_text')?></span><i class="icon-double-angle-right"></i></a>
		<?php
			foreach($nav as $ms)
			{
			?>
				<a <?php if($ms[0]) echo 'href="'.$ms[0].'"'; else echo 'class="no_url"';?>><span><?=$ms[1]?></span><i class="icon-double-angle-right"></i></a>
			<?
			}
		?>
		
	<?php
	}
	?>
	</div>
</div>
<?
}
?>