<?php
function get_pages_array($count, $active, $limit=20, $href='')
	{
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
					$hr = $href.$i;
					if($i==1)
					{
						$hr = '';
					}
					$pages[] = array('num' => $i, 'act' => 0, 'href' => $hr);
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
						$hr = $href.$i;
						if($i==1)
						{
							$hr = '';
						}
						$pages[] = array('num' => $i, 'act' => 0, 'href' => $hr);
					}
					if($i==1 && $active > 4)
					{
						$pages[] = array('num' => '.....', 'act' => 2);
					}		
				}		
			}
		}
		
		$dropdown = array(
			'values' => array('10'=>'10', '20'=>'20', '50'=>'50', '100'=>'100', '500' => '500', '1000' => '1000'),
			'active' => $limit
		);
		
		return array('pages' => $pages,'row_count' => $count,'dropdown' => $dropdown);
	}
?>