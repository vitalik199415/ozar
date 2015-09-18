<?php
if(isset($menu_base))
{
	?>
	<div class="menu_block">
        <ul id="menu">
			<?php
			foreach($menu_base[0] as $key=>$ms)
			{
				 
	            
	                
						if ($ms['url'] == 'about-company' || $ms['url'] == 'order')
						{
						?>
							
						<?
						}
						else
						{
						?>
							<li class="menu_href menu_<?php echo $key?>">
		                    	<a class="parent_menu" <?php if($ms['menu_url']) echo 'href="'.$ms['menu_url'].'"';?>><span><?=$ms['name']?></span></a>
								<?=build_submenu_html($menu_base, $ms['ID'])?>
		                  	</li>
						<?
					
				}
			}
			?>
		</ul>	
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
			$html .= ' class="arrow"';
			$html .= '>'.$ms['name'].'</a>';
			$html .= build_submenu_html($menu, $ms['ID']);
			$html .= '</li>';
			?> 
				
			<?
		}
		$html .= '</ul>';
	}
	return $html;
}
?>
<script type="text/javascript">
jQuery('ul#menu').find('li').each( function(i,item){
	if($(item).find('ul').length >0)
	{
		$(this).find('a:first').append('<span class="arrow_down"></span>');	
	}
});

	
$('ul#menu ul').hide();
$('ul#menu li').click(function() {
	var _this = jQuery(this);
	jQuery('ul#menu > li').each( function(i,item){
		
		if(!jQuery(item).hasClass(_this.attr('class')))
		{
			jQuery(item).children('ul').slideUp(300);
			$(item).removeClass('menu_href_active');
		}
		else 
		{
			if ($(item).children('ul').is(':hidden'))
			{
				$(item).children('ul').slideDown(300);
				_this.addClass('menu_href_active');
			}
			else
			{
				$(item).children('ul').slideUp(300);
				_this.removeClass('menu_href_active');
			}		
		}
		
	});
});

</script>