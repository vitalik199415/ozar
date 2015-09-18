<?php
if(isset($categories_original))
{
?>
<div class="categories_block left_block_margin" id="categories_block">
	<div class="base_left_block">
		<div class="base_left_top"><div class="label"><?=$this->lang->line('base_catalogue_text')?></div></div>
		<div class="base_left_center">
			<div class="block">
			<?=build_categories_html($categories_original)?>
			</div>
		</div>
		<div class="base_left_bot"><div class="base_left_bot_repeat"></div><div class="base_left_bot_right"></div></div>
	</div>
</div>	
<?php
echo $this->template->get_temlate_view('categories_init');
}
function build_categories_html($categories, $parent_id = 0)
{
	$html = '';
	if(isset($categories[$parent_id]))
	{
		if($parent_id > 0) $html .= '<div id="categorie_chield_'.$parent_id.'">';
		foreach($categories[$parent_id] as $ms)
		{
			$html .= '<div class="categorie cat cat_block_level'.$ms['level'].'">';
			
			if(isset($categories[$ms['ID']]))
			{
				$html .= '<a href="#" class="minus" rel="'.$ms['ID'].'"></a><a href="'.$ms['categorie_url'].'" class="cat"><span>'.$ms['name'].'</span></a></div>
				';
			}	
			else if(!isset($categories[$ms['ID']]) && $ms['have_chield']>0)
			{
				$html .= '<a href="#" class="plus" rel="'.$ms['ID'].'"></a><a href="'.$ms['categorie_url'].'" class="cat"><span>'.$ms['name'].'</span></a></div>
				';
			}
			else
			{
				$html .= '<a href="'.$ms['categorie_url'].'" class="cat no_child"><span>'.$ms['name'].'</span></a></div>
				';
			}
			$html .= build_categories_html($categories, $ms['ID']);
		}
		if($parent_id > 0) $html .= '</div>';
	}
	return $html;
}
?>