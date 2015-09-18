<script>
	var filters_settings ={
		changePrQtyUrl : '<?=$this->router->build_url('ajax_lang', array('ajax' => 'catalogue/types/ajax_update_products_qty/category_id/'.$this->variables->get_vars('category_id'), 'lang' => $this->mlangs->lang_code));?>',
		filtersTypes : filtersTypes,
		filtersActiveHtml : '<?=trim(str_replace("\n", '', str_replace("\r\n", '', $this->template->get_template_view('types_active_block'))));?>',
		filtersActiveAdditionalHtml : '<?=trim(str_replace("\n", '', str_replace("\r\n", '', $this->template->get_template_view('types_active_additional_block'))));?>'
	};
</script>