<div id="insert_active_filters_additional_block"></div>
<div class="sort_block ui-modified" id="sorting_ui_block">
	<form enctype="multipart/form-data" action="" method="post" id="sorting_clear_form"><input type="hidden" name="products_sort_clear" value="1"></form>
	<form action="<?=$this->router->build_url('category_sort_form_submit_filters_lang', array('category_url' => $this->variables->get_url_vars('category_url'), 'filters_params' => $this->variables->get_url_vars('filters_params'), 'additional_params' => $this->variables->build_additional_url_params(), 'lang' => $this->mlangs->lang_code));?>" method="post" enctype="multipart/form-data" id="sorting_form">
    <div id="sort">
      	<a class="drop_link"><?=$this->lang->line('products_sort_base_link')?> 
        	<span id="active_sort"></span>
            <i class="fa fa-caret-down"></i>
        </a>
        <div id="drop_container">
            <div class="block">
                <div class="property">
                    <div class="checkbox"><label for="sort_1"></label><input name="products_sort_clear" value="1" id="sort_1" type="checkbox"></div>
                    <span class="checkbox_name" id="default_sort"><?=$this->lang->line('products_sort_default')?></span>
                </div>
                <div class="property">
                    <div class="checkbox"><label for="sort_2"></label><input name="products_sort[price]" value="ASC" id="sort_2" type="checkbox"></div>
                    <span class="checkbox_name"><?=$this->lang->line('products_sort_price_asc')?></span>
                </div>
                <div class="property">
                    <div class="checkbox"><label for="sort_3"></label><input name="products_sort[price]" value="DESC" id="sort_3" type="checkbox"></div>
                    <span class="checkbox_name"><?=$this->lang->line('products_sort_price_desc')?></span>
                </div>
                <div class="property">
                    <div class="checkbox"><label for="sort_4"></label><input name="products_sort[create_date]" value="ASC" id="sort_4" type="checkbox"></div>
                    <span class="checkbox_name"><?=$this->lang->line('products_sort_create_date_asc')?></span>
                </div>
                <div class="property">
                    <div class="checkbox"><label for="sort_5"></label><input name="products_sort[create_date]" value="DESC" id="sort_5" type="checkbox"></div>
                    <span class="checkbox_name"><?=$this->lang->line('products_sort_create_date_desc')?></span>
                </div>
                <div class="property">
                    <div class="checkbox"><label for="sort_6"></label><input name="products_sort[options]" value="new" id="sort_6" type="checkbox"></div>
                    <span class="checkbox_name"><?=$this->lang->line('products_new')?></span>
                </div>
                <div class="property">
                    <div class="checkbox"><label for="sort_7"></label><input name="products_sort[options]" value="bestseller" id="sort_7" type="checkbox"></div>
                    <span class="checkbox_name"><?=$this->lang->line('products_bestseller')?></span>
                </div>
                <div class="property">
                    <div class="checkbox"><label for="sort_8"></label><input name="products_sort[options]" value="sale" id="sort_8" type="checkbox"></div>
                    <span class="checkbox_name"><?=$this->lang->line('products_sale')?></span>
                </div>
            </div>
        </div>
    </div>
    </form>
    <div class="products_show_type">
        <span class="list-style-buttons">
            <a href="#" id="gridview" class="switcher active"><img src="/design/icons/grid-view-active.png" alt="Grid"></a>
            <a href="#" id="listview" class="switcher"><img src="/design/icons/list-view.png" alt="List"></a>
        </span>
    </div>
    <div class="products_per_page">
    <!--<?
		if(isset($prod_per_page)){
			echo form_dropdown('products_per_page', $prod_per_page['products_limit_array'], $prod_per_page['products_limit_active']);
		}
	?>-->
        <span class="property"><label for="products_per_page"><?=$this->lang->line('products_sort_products_per_page')?></label></span>
        <?
            if(isset($prod_per_page)){
                $id = 'id="products_per_page"';
                echo form_dropdown('products_per_page', $prod_per_page['products_limit_array'], $prod_per_page['products_limit_active'], $id);
            }
        ?>
    </div>
    <div class="clear_both"></div>
</div>