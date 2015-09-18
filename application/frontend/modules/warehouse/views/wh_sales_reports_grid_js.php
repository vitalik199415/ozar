<?php
$wh_s_r_g = '#wh_logs_sales_reports_grid';
if(isset($wh_sales_id))
{
	$wh_s_r_g = $wh_sales_id;
}
?>
<script>
$('<?=$wh_s_r_g?>').view_wh_sale();
</script>