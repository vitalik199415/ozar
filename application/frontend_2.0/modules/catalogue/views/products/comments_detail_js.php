<script>
$('#product_comments_block_<?=$PRD_ID?>').gbc_products_detail_comments({
	error_submit : '<?=$this->lang->line('products_comments_error_form_submit')?>',
	product_id : '<?=$PRD_ID?>'
});
</script>