<div class="search_block">
	<div class="block">
        <div class="searchForm">
            <form enctype="multipart/form-data" action="<?=$this->router->build_url('start_search_lang', array('lang' => $this->mlangs->lang_code));?>" method="post" id="search_block_form">
                <input type="text" class="queryField" size="20" maxlength="40" name="search_string" value="<?=$search_text?>" >
                <input type="submit" class="searchSbmFl" id="search_button" value="">
            </form>
        </div>
    </div>
</div>
<?php
echo $this->template->get_temlate_view('search_init');
?>