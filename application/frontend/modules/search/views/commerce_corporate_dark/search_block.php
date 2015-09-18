<?php

?>
<form enctype="multipart/form-data" action="<?=$this->router->build_url('start_search_lang', array('lang' => $this->mlangs->lang_code));?>" method="post" id="search_block_form">
<div class="search_block">
	<div class="block">
		<input type="text" name="search_string" value="<?=$search_text?>" class="focus_out"><a href="#" class="big_button search" id="search_button"><span><?=$this->lang->line('search_button_text')?></span></a>
	</div>
</div>
</form>
<?php
echo $this->template->get_temlate_view('search_init');
?>