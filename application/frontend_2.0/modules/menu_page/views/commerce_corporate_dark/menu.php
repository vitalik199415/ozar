<?php
if(isset($menu_base))
{
	?>
	<div class="menu_block">
		<div class="block">
		<ul id="multi_ddm_top">	
			<?php
			foreach($menu_base[0] as $ms)
			{
				?>
					<li><a <?php if($ms['menu_url']) echo 'href="'.$ms['menu_url'].'"';?>><span><?=$ms['name']?></span></a><?=build_submenu_html($menu_base, $ms['ID'])?></li>
				<?
			}
			?>
		</ul>	
		</div>
	</div>
	<?
	echo $this->template->get_temlate_view('menu_init');
}
function build_submenu_html($menu, $parent_id)
{
	$html = '';
	if(isset($menu[$parent_id]))
	{
		$html .= '<ul>';
		foreach($menu[$parent_id] as $ms)
		{
			$html .= '<li';
			if(isset($menu[$ms['ID']])) $html .= ' class="arrow"';
			$html .= '><a ';
			if($ms['menu_url']) $html .= 'href="'.$ms['menu_url'].'"';
			$html .= '><span>'.$ms['name'].'</span></a>';
			$html .= build_submenu_html($menu, $ms['ID']);
			$html .= '</li>';
		}
		$html .= '</ul>';
	}
	return $html;
}
?>