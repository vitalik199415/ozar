<!-- Start SiteHeart code -->
<script>
(function(){
var widget_id = 794725;
_shcp =[{widget_id : widget_id}];
var lang =(navigator.language || navigator.systemLanguage 
|| navigator.userLanguage ||"en")
.substr(0,2).toLowerCase();
var url ="widget.siteheart.com/widget/sh/"+ widget_id +"/"+ lang +"/widget.js";
var hcc = document.createElement("script");
hcc.type ="text/javascript";
hcc.async =true;
hcc.src =("https:"== document.location.protocol ?"https":"http")
+"://"+ url;
var s = document.getElementsByTagName("script")[0];
s.parentNode.insertBefore(hcc, s.nextSibling);
})();
</script>
<!-- End SiteHeart code -->
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
                <div><span class="skype_ico">ozar.company</span></div>
                <div><span class="viber_ico">+38 (050) 101 23 81</span></div>
                <div><span class="whatsapp_ico">+90538 726 13 12</span></div>
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
