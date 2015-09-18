<?php
	if(count($options_array) > 1)
	{
?>
<script type="text/javascript">
$(function(){

	$("select").multiselect({
		selectedList: 4

	});
	
});
</script>
	<div id="<?=$filter_item_id?>" class="dropdown_checkbox_filter_block filter_block" >
		<div><?=$group_name?></div>
		<div class="property_block">
			<?
			$options_checked = array();
			$options = array();
			foreach($options_array as $chkey => $chms)
			{
				$options[$chkey] = $chms['pname'];
			}
			?>
			<div>
				<?=form_dropdown('ducks', $options, 9)?>
			</div>
		</div>
	</div>	
<?
	}
?>


	
