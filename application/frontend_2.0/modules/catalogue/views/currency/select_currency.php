<?php
if(isset($select_currency_array) && isset($select_currency_array['currency_array']) && count($select_currency_array['currency_array']) > 1)
{
?>
<div class="select_currency_block" id="select_currency">
<span class="select_currency_label"><?=$this->lang->line('select_currency_label')?></span> <?=form_dropdown('select_currency', $select_currency_array['currency_array'], $select_currency_array['selected_currency']['ID'], 'id = "select_currency_select"');?>
</div>
<?
}
?>