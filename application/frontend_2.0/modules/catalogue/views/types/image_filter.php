<?php
	if(count($options_array) > 1)
	{
?>
	<div id="<?=$filter_item_id?>" class="image_filter_block filter_block">
		<div class="block">
			<div class="group_name">
				<span>
					<?=$group_name?>
				</span>
			</div>
			<div class="properties_block">
				<?
				foreach($options_array as $chkey => $chms)
				{
					$checked = false;
					if(isset($options_active[$chkey])) $checked = true;
					$checkbox_data = array(
						'type' => 'checkbox',
						'id' => 'filter_checkbox_'.$chkey,
						'name' => 'products_filters['.$type_id.']['.$chkey.']',
						'value' => $chkey,
						'checked' => $checked
					);
					if($chms['pr_qty'] == 0) $checkbox_data['disabled'] = NULL;
					?>
					<div class="property">
						<div class="checkbox">
							<label for="filter_checkbox_<?=$chkey?>" title="<?=$chms['pr_qty']?>">
								<div class="img_block">
									<img src=<?=$chms['image']?> />
								</div>
							</label>
							<?=form_checkbox($checkbox_data)?>
							<span class="checkbox_name"><?=$chms['pname']?></span><i>(<?=$chms['pr_qty']?>)</i>
						</div>
						<div class="clear_both"></div>
					</div>
					<?
				}
				?>
			</div>
		</div>
	</div>	
<?
	}
?>

