<div class="filters_block ui-modified" id="filters_ui_block" align="center">
	<div class="block">
		<div class="filters_title">
			Подбор по параметрам
		</div>
		<form enctype="multipart/form-data" action="" method="post" id="filters_clear_form"><input type="hidden" name="products_filters_clear" value="1"></form>
		<form enctype="multipart/form-data" action="<?=$this->router->build_url('category_filters_form_submit_sort_lang', array('category_url' => $category_url, 'sort_params' => $this->variables->get_url_vars('sort_params'), 'additional_params' => $this->variables->build_additional_url_params(), 'lang' => $this->mlangs->lang_code));?>" method="post" id="filters_form">
			<div id="insert_active_filters_block">
			</div>
			<div id="output_filters_block" align="center">
				<?=$this->template->get_template_view('types_block_filters');?>
				<div class="clear_both"></div>
			</div>
			<div class="filters_buttons">
				<a href="#" class="activate_filter" id="filter_block_activate_button"><span><?=$this->lang->line('products_filter_activate')?></span></a>
				<a href = "#" class="clear_filter" id="filter_block_clear_button"><span><?=$this->lang->line('products_filter_clear')?></span></a>
			</div>
		</form>
	</div>
</div>