<?php
$wh_t_r_g = '#wh_logs_transfers_grid';
if(isset($wh_transfers_id))
{
	$wh_t_r_g = $wh_transfers_id;
}
?>
<script>
$('<?=$wh_t_r_g?>').view_wh_transfer();
</script>