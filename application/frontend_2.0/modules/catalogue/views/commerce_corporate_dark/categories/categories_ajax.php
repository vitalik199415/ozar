<?php
if(isset($categories))
{
echo build_categories_ajax_html($categories);
}
function build_categories_ajax_html($categories = array())
{
	$html = '';
	if(count($categories)>0)
	{
		foreach($categories as $ms)
		{
			$html .= '<div class="categorie cat cat_block_level'.$ms['level'].'">';
			
			if($ms['have_chield']>0)
			{
				$html .= '<a href="#" class="plus cat_plus" rel="'.$ms['ID'].'"></a><a href="'.$ms['categorie_url'].'" class="cat"><span>'.$ms['name'].'</span></a></div>
				';
			}
			else
			{
				$html .= '<a href="'.$ms['categorie_url'].'" class="cat no_child"><span>'.$ms['name'].'</span></a></div>
				';
			}
		}
	}
	return $html;
}
?>