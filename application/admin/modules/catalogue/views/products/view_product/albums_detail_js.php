<?php
if(isset($PRD_array['albums_array']) && count($PRD_array['albums_array'])>0)
{
	?>
	<script>
		$('#<?=$PRD_block_id?>').gbc_products_albums({
			product_id : '<?=$PRD_ID?>'
		});
	</script>
	<?php
}
?>