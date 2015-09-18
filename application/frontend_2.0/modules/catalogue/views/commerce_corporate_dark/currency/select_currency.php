<?php
if(isset($select_currency_array) && isset($select_currency_array['currency_array']))
{
?>
<div class="select_currency_block" id="select_currency">
<div class="base_block">
	<div class="base_top">
		<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
	</div>
	<div class="base_center">
		<div class="base_center_left"></div><div class="base_center_right"></div>
		<div class="base_center_repeat">
			<div class="block">
				<span class="select_currency_label"><?=$this->lang->line('select_currency_label')?></span> <?=form_dropdown('select_currency', $select_currency_array['currency_array'], $select_currency_array['selected_currency']['ID'], 'id = "select_currency_select"');?>
			</div>
		</div>
	</div>	
	<div class="base_bot">
		<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
	</div>	
	</div>
</div>
</div>
<?
}
?>