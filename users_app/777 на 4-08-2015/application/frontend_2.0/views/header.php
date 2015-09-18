<div align="center">
    <div class="header_media">
        <div class="work">


        </div>
    </div>
    <script>
         $(document).ready(function() {
          $('.header_media').scrollToFixed();
        });
    </script>
    <div class="header_top">
        <div class="work">
            <?=$this->template->get_temlate_view('customers_block');?>
            <?=$this->template->get_temlate_view('select_currency_block');?>
            <?=$this->template->get_temlate_view('menu_block');?>
            <div class="clear_both"></div>
        </div>
    </div>
    <div class="header_bottom">
        <div class="work">
            <a class="logo_ru" href="<?=$this->router->build_url('index', array('lang' => $this->mlangs->lang_code))?>"></a>
            <?=$this->template->get_temlate_view('search_block');?>
            <div class="header_phones_block">
                <div class="phones_ico"></div>
                <div><span>+38 (066) 923 00 00</span></div>
                <div><span>+38 (066) 923 00 00</span></div>
                <div><span>+38 (066) 923 00 00</span></div>
            </div>
            <?=$this->template->get_temlate_view('favorites_block');?>
            <div style="display:block"><?=$this->template->get_temlate_view('cart_block');?></div>
            <div class="clear_both"></div>
        </div>
    </div>
    <div class="home_categories_block">
        <div class="work">
            <a href="/" class="home_btn"></a><?=$this->template->get_temlate_view('home_categories_block');?>
        </div>
    </div>
</div>
