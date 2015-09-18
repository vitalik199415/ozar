<?php
if(isset($all_wh))
{
?>
<div align="center" style="font-size:16px;">
<?php
foreach($all_wh as $key => $ms)
{
	?>&nbsp;&nbsp;<a href="<?=set_url('*/*/wh_actions/wh_id/'.$key)?>"><?=$ms?></a><?
}
?>&nbsp;&nbsp;
</div>
<?
}
?>