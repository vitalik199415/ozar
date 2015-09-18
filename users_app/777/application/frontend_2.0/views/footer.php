<div class="footer" align="center">
    <div class="work">
        <a href="/" class="footer_logo"></a>
        <div class="footer_menu">
            <?=$this->template->get_temlate_view('footer_menu_block');?>
        </div>
        <div class="footer_catalogue">
            <div class="footer_catalogue_block">
                <a href="/category-jeans">Джинсовая одежда</a>
                <a href="/category-for-she">Для нее</a>
                <a href="/category-for-him">Для него</a>
                <a href="/category-shoes">Обувь</a>
                <a href="/category-handbags">Сумки</a>
                <a href="/category-baby-clothes">Детская одежда</a>
                <a href="/category-textile">Домашний текстиль</a>
                
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
			<div id="open_counters">
                <a href="#" class="counters_ico" title="Показать счетчики"></a>
                <div class="counters">
                    <div class="footer_slide_block">
                        <?=$this->template->get_temlate_view('block_additionally_footer');?>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $('.footer_slide_block').hide();    
                $('#open_counters > a').toggle(
                    function(){
                        $('.footer_slide_block').show(200);
                        $(this).toggleClass('active'); 
                    },
                    function(){
                        $('.footer_slide_block').hide(200);
                        $(this).toggleClass('active'); 
                    }
                );
        
                $(document).click(function(event){
                    if($(event.target).closest(".slide_block").length) return;
                    $('.footer_slide_block').hide(200);
                    $('#open_counters a').removeClass('active'); 
                    event.stopPropagation();
                });
            </script>
        </div>
		<div style="padding:5px 0 10px 0; float:right;  margin:0px 0px 0 0px; color:#b1afa2; position:absolute; right:0; top:160px;">
                <div style="width:470px;">
                    <a href="http://gbc.net.ua" target="_blank" title="Создание и раскрутка сайта - бизнес каталог GBC">
                        <div class="gbc"></div>
                        <div style=" color:#b1afa2; margin-top:3px; text-transform: lowercase; text-align:right; cursor:pointer; float:right;line-height:18px; width:320px; float:right;  padding:5px;">
                            создание и продвижение интернет-магазинов<br />БИЗНЕС КАТАЛОГ GBC.NET.UA
                        </div>
                    </a>
                </div><div style="clear:both"></div>
            </div><div style="clear:both">
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

 <script language=JavaScript>

var message="Правая кнопка мыши отключена!";
function click(e) {
   if (document.all) {    // IE
      if (event.button == 2) {    // Чтобы отключить левую кнопку поставьте цифру 1
          alert(message);    // чтобы отключить среднюю кнопку поставьте цифру 1
          return false;}
      }
   if (document.layers) { // NC
      if (e.which == 3) {
          alert(message);
          return false;}
      }
}
if (document.layers)
   {document.captureEvents(Event.MOUSEDOWN);}
document.onmousedown=click;
document.oncontextmenu=function(e){return false};
//
</script>
