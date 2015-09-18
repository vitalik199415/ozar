<?php
if(isset($PRD_array['attributes_array']) && count($PRD_array['attributes_array'])>0)
{
?>
<div class="attributes_block" id="attributes_block">
<div class="block" align="right">
<table cellspacing="0" cellpadding="0" border="0">
	<?php
	foreach($PRD_array['attributes_array']['attributes'] as $ms)
	{
	?><tr class="attribute_select_tr"><td width="1" valign="middle"><div class="attribute_select_label" rel="<?=$ms['ID']?>"><pre><span><?=$ms['a_name']?>:</span></pre></div></td><td valign="middle"><div class="attribute_select_block" rel="<?=$ms['ID']?>"><?=form_dropdown("attributes[".$ms['ID']."]", $PRD_array['attributes_array']['options'][$ms['ID']], '', 'rel="'.$ms['ID'].'" class="select_attributes"')?></div></td></tr><?
	}
	?>
</table>
</div>
</div>
<?
}
?>