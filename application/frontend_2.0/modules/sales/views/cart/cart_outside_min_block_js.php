<script>
$('#<?=$cart_min_block_id?>').gbc_cart(
{
	edit_cart_url : '<?=$this->router->build_url('cart_methods_lang', array('method' => 'edit_cart_form', 'lang' => $this->mlangs->lang_code));?>',
	create_order_url : '<?=$this->router->build_url('order_methods_lang', array('method' => 'create_order_form', 'lang' => $this->mlangs->lang_code));?>'
});
</script>