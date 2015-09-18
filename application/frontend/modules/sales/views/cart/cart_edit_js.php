<script>
$('#<?=$cart_edit_block_id?>').gbc_edit_cart(
{
	url_edit_item : '<?=$this->router->build_url('cart_methods_lang', array('method' => 'ajax_edit_cart_item', 'lang' => $this->mlangs->lang_code));?>',
	url_delete_item : '<?=$this->router->build_url('cart_methods_lang', array('method' => 'ajax_delete_cart_item', 'lang' => $this->mlangs->lang_code));?>'
});
</script>