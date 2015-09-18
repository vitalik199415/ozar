<?php
if(isset($wh_print_data))
{
?>
<div style="margin:10px 0; font-size:18px;" align="center"><a href="#" id="wh_pr_print_button">Печать данных</a></div>
<div style="background:#FFFFFF;color:#000000; font-size:14px; page_break_after:always;" align="left" id="wh_pr_print_block">
<table cellspacing="0" cellpadding="2" border="1" width="100%">
<tr>
	<td width="35%"><b>Артикул</b></td><td><b>Название</b></td><td width="15%"><b>Кол-во</b></td>
</tr>
	<?php
	foreach($wh_print_data as $ms)
	{
		?>
		<tr>
			<td width="35%"><?=$ms['sku']?></td><td><?=$ms['name']?></td><td width="15%"><?=$ms['qty']?></td>
		</tr>
		<?
	}
	?>
</table>
</div>
<script>
	$('#wh_pr_print_button').click(function()
	{
		$('#wh_pr_print_block').printElement();
		return false;
	});
</script>
<?
}
?>