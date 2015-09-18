<div class="block">
	<a href="<?=$this->router->build_url('ajax_lang', array('ajax' => 'sales/favorites/ajax_show_favorites_products', 'lang' => $this->mlangs->lang_code));?>" id="show_favorites_products"><?=$this->lang->line('favorites_favorites')?> : <span class="data"><?=$total_items?></span></a>
</div>