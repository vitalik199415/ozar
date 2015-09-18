<?php
if(isset($select_array) && count($select_array)>0)
{
	$select_html = '';
	$show = FALSE;
	foreach($select_array as $key => $ms)
	{
		if(count($options_array[$key]) > 1)
		{
			$show = TRUE;
			$class_active = '';
			if($options_active[$key] != '') $class_active = 'class = "active_select"';
			$select_html .= form_dropdown('products_filters['.$key.']', array('' => $ms) + $options_array[$key], $options_active[$key], $class_active);
		}	
	}
	if($show)
	{
	?>
	<div class="filters_block" id="filters_block">
		<form enctype="multipart/form-data" action="<?=$this->router->build_url('categorie_lang', array('categorie_url' => $categorie_url, 'lang' => $this->mlangs->lang_code));?>" method="post" id="filters_clear_form"><input type="hidden" name="products_filters_clear" value="1"></form>
		<form enctype="multipart/form-data" action="<?=$this->router->build_url('categorie_lang', array('categorie_url' => $categorie_url, 'lang' => $this->mlangs->lang_code));?>" method="post" id="filters_form">
		<div class="block">
			<?php
			echo $select_html;
			?>
		<div class="filters_buttons"><a href = "#" id="filter_block_activate_button"><span><?=$this->lang->line('products_filter_activate')?></span></a><a href = "#" id="filter_block_clear_button"><span><?=$this->lang->line('products_filter_clear')?></span></a></div>	
		</div>
		</form>
	</div>
	<script>
	$('#filters_block').gbc_types();
	</script>
	<?php
	}
}
?>