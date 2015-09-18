<?php
if(isset($categories))
{
?>
<div class="home-catalogue-block">

	<ul id="catalogue-dropdown">

			<?php

			$lvl1_open = array();
			$lvl1_close = '</ul></li>';
			$lvl2_open = array();
			$lvl2_close = '</ul></li>';
			$lvl3 = array();
            $lvl3_close = '</ul></li>';
			foreach($categories as $ms)
			{
				if($ms['level'] == 1)
				{
					if( $ms['have_chield'] != 0) {
						$lvl1_open[$ms['ID']] = '<li id="id_'.$ms['ID'].'"><a class="pointer_lvl1">'.$ms['name'].'</a><ul id="ullv2">';
					}
					else
					{
						$lvl1_open[$ms['ID']] = '<li><a href="'.$ms['category_url'].'">'.$ms['name'].'</a><ul>';
					}

				}

				if($ms['level'] == 2)
				{
					if($ms['have_chield'] !=0) {
						$lvl2_open[$ms['id_parent']][$ms['ID']] = '<li id="id_'.$ms['ID'].'"><a class="pointer_lvl2">'.$ms['name'].'</a><ul id="ullv3">';

					}
					else
					{
						$lvl2_open[$ms['id_parent']][$ms['ID']] = '<li><a href="'.$ms['category_url'].'">'.$ms['name'].'</a><ul>';
					}
				}
				if($ms['level'] == '3')
				{
                    $lvl3[$ms['id_parent']][] = '<li><a href="'.$ms['category_url'].'">'.$ms['name'].'</a></li>';
				}
			}
			foreach($lvl1_open as $key => $ms)
			{
				echo $ms;
				if(isset($lvl2_open[$key]))
				{
					foreach($lvl2_open[$key] as $key2 => $ms2)
					{
						echo $ms2;
						if(isset($lvl3[$key2]))
						{
							foreach($lvl3[$key2] as $ms3)
							{
								echo $ms3;
							}
						}
						echo $lvl2_close;
					}
				}
				echo $lvl1_close;
			}
			?>
	</ul>
	<div style="clear:both;"></div>
</div>
<?php
}
?>

<script type="text/javascript">
	$('#catalogue-dropdown').gbc_dropdown_menu({open_type:'hover'});
</script>

<script>
	$(function(){
		$('#catalogue-dropdown').slicknav();
	});
</script>

