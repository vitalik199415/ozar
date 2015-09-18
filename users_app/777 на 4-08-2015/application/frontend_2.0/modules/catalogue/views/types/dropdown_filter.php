<?php
	if(count($options_array) > 1)
	{
?>
	<div id="<?=$filter_item_id?>" class="dropdown_filter_block filter_block">
		<div class="block">
			<div class="group_name">
				<?=$group_name?>
			</div>
			<div class="properties_block">
				<?
				foreach($options_array as $chkey => $chms)
				{
					$checked = false;
					if(isset($options_active[$chkey])) $checked = true;
					$checkbox_data = array(
												'type' => 'checkbox',
												'id' => 'check'.$chkey.'',
												'name' => 'products_filters['.$type_id.']['.$chkey.']',
												'value' => ''.$chkey.'',
												'checked' => ''.$checked.''
					);
					?>
					<div class="property">
						<div class="checkbox"><label for="check<?=$chkey?>"></label><?=form_checkbox($checkbox_data)?></div><span><?=$chms['pname']?></span><i>(<?=$chms['pr_qty']?>)</i>
					</div>
					<?
				}
				?>
			</div>
		</div>
		<script>
		</script>
	</div>	
<?
	}
?>



	

	
