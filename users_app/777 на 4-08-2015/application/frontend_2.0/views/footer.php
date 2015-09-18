<div class="footer" align="center">
    <div class="work">
        <a href="/" class="footer_logo"></a>
        <div class="footer_menu">
            <?=$this->template->get_temlate_view('footer_menu_block');?>
        </div>
        <div class="footer_catalogue">
            <div class="footer_catalogue_block">
                <a href="/">Джинсовая одежда</a>
                <a href="/">Для нее</a>
                <a href="/">Для него</a>
                <a href="/">Детская одежда</a>
                <a href="/">Товары для дома</a>
                <a href="/">Ткани/Фурнитура</a>
                <a href="/">Текстиль</a>
                <a href="/">Аксессуары</a>
                <a href="/">Галантерея</a>
            </div>
        </div>
        <div class="social">
            <div class="social_title">Официальные страницы в соцсетях</div>
            <div class="social_block">
                <a href="/" class="vk_ico"></a>
                <a href="/" class="od_ico"></a>
                <a href="/" class="fb_ico"></a>
                <a href="/" class="tw_ico"></a>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $.scrollUp({
            scrollImg: { active: true, type: 'background', src: '/users_app/777/img/top.png' }
        });
    });

</script>

<div style="display: none;" align="left">  
    <div class="box-modal" id="boxUserFirstInfo" align="left">  
        <div class="user_first_text" >
            Уважаемые клиенты!
        </div>
    </div>  
</div>
<script>
(function($) {
$(function() {

  if (!$.cookie('visit')) {


    $('#boxUserFirstInfo').arcticmodal({
      closeOnOverlayClick: false,
      closeOnEsc: true
    });

  }

  $.cookie('visit', true, {
    expires: 365,
    path: '/'
  });

})
})(jQuery)
</script>


