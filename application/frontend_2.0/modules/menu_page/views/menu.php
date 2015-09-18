<?php
if(isset($menu))
{
	?>
	<div class="menu_block">
		<div class="block">
			<?php
			foreach($menu as $ms)
			{
				?>
					<a <?php if($ms['menu_url']) echo 'href="'.$ms['menu_url'].'"';?> class="menu_link"><?=$ms['name']?></a>
				<?
			}
			?>
		</div>
	</div>
	<?
}
?>