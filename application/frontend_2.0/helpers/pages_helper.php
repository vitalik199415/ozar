<?php
function get_pages_array($count, $active, $limit=8, array $href = array())
	{
		$CI = & get_instance();
		$countedpages = ceil($count/$limit);
		$pages = array();
		if($countedpages<10)
		{
			for($i=1;$i<=$countedpages;$i++)
			{
				if($i == $active)
				{
					$pages[] = array('num' => $i, 'act' => 1);
				}
				else
				{
					$href[1]['page'] = $i;
					if($i == 1) $href[1]['page'] = FALSE; 
					$pages[] = array('num' => $i, 'act' => 0, 'href' => $CI->router->build_url($href[0], $href[1]));
				}	
			}
		}
		else
		{
			for($i=1;$i<=$countedpages;$i++)
			{
				if(($i >= ($active-2) && $i <= ($active+2)) || $i == $countedpages || $i==1)
				{
					if($i == $countedpages && $active < ($countedpages-3))
					{
						$pages[] = array('num' => '.....', 'act' => 2);
					}		
					if($i == $active)
					{
						$pages[] = array('num' => $i, 'act' => 1);
					}
					else
					{
						$href[1]['page'] = $i;
						if($i == 1) $href[1]['page'] = FALSE; 
						$pages[] = array('num' => $i, 'act' => 0, 'href' => $CI->router->build_url($href[0], $href[1]));
					}
					if($i==1 && $active > 4)
					{
						$pages[] = array('num' => '.....', 'act' => 2);
					}		
				}		
			}
		}
		
		$prev_url = FALSE;
		$next_url = FALSE;
		
		if($active > 1)
		{
			if($active == 2)
			{
				$href[1]['page'] = FALSE;
			}
			else
			{
				$href[1]['page'] = $active - 1;
			}
			$prev_url = $CI->router->build_url($href[0], $href[1]);
		}
		if($active < $countedpages)
		{
			$href[1]['page'] = $active + 1;
			$next_url = $CI->router->build_url($href[0], $href[1]);
		}
		
		$dropdown = array(
			'values' => array('10'=>'10','20'=>'20','50'=>'50','100'=>'100'),
			'active' => $limit
		);
		
		return array('pages' => $pages, 'pages_active' => $active, 'rows_count' => $count, 'pages_count' => $countedpages, 'dropdown' => $dropdown, 'prev_url' => $prev_url, 'next_url' => $next_url);
	}
function getPagesArray($count, $active, $limit=8, $href='')
{
	return get_pages_array($count, $active, $limit, $href);
}	
?>