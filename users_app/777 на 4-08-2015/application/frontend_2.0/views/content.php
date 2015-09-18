<div class="content" align="center">
    <div class="work">
        <div class="slideshow_block">
            <script>
                jssor_slider1_starter = function (containerId) {
                var options = {
                    $AutoPlay: true,                                    //[Optional] Whether to auto play, to enable slideshow, this option must be set to true, default value is false
                    $AutoPlayInterval: 4000,                            //[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing, default value is 3000
                    $PauseOnHover: 1,                                   //[Optional] Whether to pause when mouse over if a slider is auto playing, 0 no pause, 1 pause for desktop, 2 pause for touch device, 3 pause for desktop and touch device, 4 freeze for desktop, 8 freeze for touch device, 12 freeze for desktop and touch device, default value is 1

                    $ArrowKeyNavigation: true,                          //[Optional] Allows keyboard (arrow key) navigation or not, default value is false
                    $SlideDuration: 800,                                //[Optional] Specifies default duration (swipe) for slide in milliseconds, default value is 500
                    $MinDragOffsetToSlide: 20,                          //[Optional] Minimum drag offset to trigger slide , default value is 20
                    //$SlideWidth: 600,                                 //[Optional] Width of every slide in pixels, default value is width of 'slides' container
                    //$SlideHeight: 300,                                //[Optional] Height of every slide in pixels, default value is height of 'slides' container
                    $SlideSpacing: 0,                                   //[Optional] Space between each slide in pixels, default value is 0
                    $DisplayPieces: 1,                                  //[Optional] Number of pieces to display (the slideshow would be disabled if the value is set to greater than 1), the default value is 1
                    $ParkingPosition: 0,                                //[Optional] The offset position to park slide (this options applys only when slideshow disabled), default value is 0.
                    $UISearchMode: 1,                                   //[Optional] The way (0 parellel, 1 recursive, default value is 1) to search UI components (slides container, loading screen, navigator container, arrow navigator container, thumbnail navigator container etc).
                    $PlayOrientation: 1,                                //[Optional] Orientation to play slide (for auto play, navigation), 1 horizental, 2 vertical, 5 horizental reverse, 6 vertical reverse, default value is 1
                    $DragOrientation: 1,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)

                    $ArrowNavigatorOptions: {                       //[Optional] Options to specify and enable arrow navigator or not
                        $Class: $JssorArrowNavigator$,              //[Requried] Class to create arrow navigator instance
                        $ChanceToShow: 1,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                        $AutoCenter: 2,                                 //[Optional] Auto center arrows in parent container, 0 No, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                        $Steps: 1                                       //[Optional] Steps to go for each navigation request, default value is 1
                    },

                    
                };

                var jssor_slider1 = new $JssorSlider$(containerId, options);

                //responsive code begin
                //you can remove responsive code if you don't want the slider scales while window resizes
                function ScaleSlider() {
                    var bodyWidth = document.body.clientWidth;
                    if (bodyWidth)
                        jssor_slider1.$ScaleWidth(Math.min(bodyWidth, 1280));
                    else
                        $Jssor$.$Delay(ScaleSlider, 30);
                }

                ScaleSlider();
                $Jssor$.$AddEvent(window, "load", ScaleSlider);

                $Jssor$.$AddEvent(window, "resize", $Jssor$.$WindowResizeFilter(window, ScaleSlider));
                $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
                //responsive code end
            };
            </script>

            <div style="position: relative; width: 100%; background-color: #fff; overflow: hidden;">
                <div style="position: relative; left: 50%; width: 5000px; text-align: center; margin-left: -2500px;">
                    <!-- Jssor Slider Begin -->
                    <div id="slider1_container" style="position: relative; margin: 0 auto;
                        top: 0px; left: 0px; width: 1280px; height: 434px;">
                        <!-- Loading Screen -->
                        <div u="loading" style="position: absolute; top: 0px; left: 0px;">
                            <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block;
                                top: 0px; left: 0px; width: 100%; height: 100%;">
                            </div>
                            <div style="position: absolute; display: block; background: url(../img/loading.gif) no-repeat center center;
                                top: 0px; left: 0px; width: 100%; height: 100%;">
                            </div>
                        </div>
                        <!-- Slides Container -->
                        <div u="slides" style="cursor: move; position: absolute; left: 0px; top: 0px; width: 1280px;
                            height: 434px; overflow: hidden;">
                            <div>
                                <img u="image" src="/users_app/777/img/slide_main.jpg">
                            </div>
                            <div>
                                <img u="image" src="/users_app/777/img/slide4.jpg">
                                <div class="slide_title tslide-1">Джинсовая одежда</div>
                                <div class="slide_href" align="right">
                                    <a href="/">Женская</a>
                                    <div class="clear_both"></div>
                                    <a href="/">Мужская</a>
                                    <div class="clear_both"></div>
                                    <a href="/">Подростковая</a>
                                </div>
                            </div>
                            <div>
                                <img u="image" src="/users_app/777/img/slide1.jpg">
                                <div class="slide_title tslide-1">Детская одежда</div>
                                <div class="slide_href" align="right">
                                    <a href="/">Для новородженных</a>
                                    <div class="clear_both"></div>
                                    <a href="/">Для мальчиков</a>
                                    <div class="clear_both"></div>
                                    <a href="/">Для девочек</a>
                                </div>
                            </div>
                            <div>
                                <img u="image" src="/users_app/777/img/slide2.jpg">
                                <div class="slide_title tslide-1">Домашний текстиль</div>
                                <div class="slide_href" align="right">
                                    <a href="/">Постельное белье</a>
                                    <a href="/">Покрывала</a>
                                    <a href="/">Одеяла</a>
                                    <a href="/">Подушки</a>
                                    <a href="/">Одежда для дома</a>
                                    <a href="/">Прочие товары</a>
                                </div>
                            </div>
                            <div>
                                <img u="image" src="/users_app/777/img/slide3.jpg">
                                <div class="slide_title tslide-1">Мужская одежда</div>
                                <div class="slide_href" align="right">
                                    <a href="/">Рубашки</a>
                                    <a href="/">Свитера</a>
                                    <div class="clear_both"></div>
                                    <a href="/">Джинсы</a>
                                    <a href="/">Куртки</a>
                                    <div class="clear_both"></div>
                                    <a href="/">Пиджаки</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        jssor_slider1_starter('slider1_container');
                    </script>
                    <!-- Jssor Slider End -->
                </div>
            </div>






        </div>
        <div class="why_we">
            <a href="/users_app/777/why_we/1.html" class="why_modalbox">
                <div class="img"><img src="/users_app/777/img/posredniki.png"/></div>
                <span>Работаем <br>без <br>посредников</span>
            </a>
            <a href="/users_app/777/why_we/2.html"  class="why_modalbox">
                <div class="img"><img src="/users_app/777/img/ceny_proizvoditelya.png"/></div>
                <span>Покупайте товары <br>по ценам <br>производителя</span>
            </a>
            <a href="/users_app/777/why_we/3.html" class="why_modalbox">
                <div class="img"><img src="/users_app/777/img/dostavka.png"/></div>
                <span>Доставка <br>в любую страну СНГ <br>от 4 до 15 дней</span>
            </a>
            <a href="/users_app/777/why_we/4.html" class="why_modalbox">
                <div class="img"><img src="/users_app/777/img/tovary.png"/></div>
                <span>Еженедельно <br>более 1000 <br>новых товаров</span>
            </a>
            <a href="/users_app/777/why_we/5.html" class="why_modalbox">
                <div class="img"><img src="/users_app/777/img/opt.png"/></div>
                <span>Гибкая <br>система скидок <br>для оптовых <br>покупателей</span>
            </a>
            <a href="/users_app/777/why_we/6.html" class="why_modalbox">
                <div class="img"> <img src="/users_app/777/img/sp.png"/></div>
                <span>Накопительная <br>система скидок <br>для организаторов <br>совместных покупок</span>
            </a>
        </div>
        <script>
            $("a.why_modalbox").fancybox(
                {                                 
                    "frameWidth" : 1000,     
                    "frameHeight" : 500,
                    "hideOnContentClick" : false,
                    "hideOnOverlayClick" : false
                                                  
                });
        </script>
        <?=$this->template->get_temlate_view('last_news_block');?>
    </div>
    <div class="call_me_block">
        <div class="work">
            <div class="test_form">
                <div id="jbCallme_form" class="jbCallme2" style="display: block;">
                    <form class="jb_form2">
                        <div class="jb_input2">
                            <input required="required" placeholder="Страна" type="text" name="name">
                        </div>
                        <div class="jb_input2">
                            <input required="required" placeholder="Ф.И.О" type="text" name="tel">
                        </div>
                        <div class="jb_input2">
                            <input placeholder="Номер телефона" type="text" name="email">
                        </div>
                        <div class="jb_input2">
                            <input placeholder="E-mail" type="text" name="email">
                        </div>
                        <div class="jb_input2">
                            <input placeholder="Skype" type="text" name="email">
                        </div>
                        <div class="jb_input2">
                            <input value="Заказать звонок" type="submit" name="submit" >
                        </div>
                        <input value="callme" type="hidden" name="action">
                    </form>
                    <div class="jb_success" style="display: none;">
                     <?=$this->lang->line('z_form_succsess')?>
                    </div>
                    <div class="jb_progress" style="display: none;">
                    </div>
                    <div class="jb_fail" style="display: none;">
                        <?=$this->lang->line('z_form_succsess')?>
                    </div>
                    <script type="text/javascript">
                        $(function(){
                            $('.test_form').jbcallme_form({
                                
                            }
                                
                        )
                            jQuery('form')[0].reset();
                        })
                    </script>
                </div>       
            </div>
            <div class="call_me_text_block">
                <div class="title">Уважаемые клиенты!</div>
                <div class="text_block">
                    Если Вам необходимо задать или обсудить важные
                    для Вас вопросы, заполните форму обратного звонка
                    и наш менеджер свяжется с Вами в удобное для Вас 
                    время.
                </div>
                <div class="text_block">
                Рабочие дни: <span>Понедельник-Пятница</span><br>
                    Выходные суббота и воскресенье
                </div>
            </div>
            <div class="clear_both"></div>
        </div>
        <div class="clear_both"></div>
    </div>
    <div class="work">
        <div class="popular_categories_block">
            <a href="/" class="block post_belyo">
                <div class="popular_categories_name">
                    <span>постельное белье</span>
                </div>
            </a>
            <a href="/" class="block mans_shoose">
                <div class="popular_categories_name">
                    <span>мужская обувь</span>
                </div>
            </a>
            <a href="/" class="block wooman_shoose">
                <div class="popular_categories_name">
                    <span>женская обувь</span>
                </div>
            </a>
            <a href="/" class="block woman_bags">
                <div class="popular_categories_name">
                    <span>женские сумки</span>
                </div>
            </a>
            <a href="/" class="block man_rub">
                <div class="popular_categories_name">
                    <span>мужские рубашки</span>
                </div>
            </a>
            <a href="/" class="block woman_jeans">
                <div class="popular_categories_name">
                    <span>женские джинсы</span>
                </div>
            </a>
            <a href="/" class="block man_jeans">
                <div class="popular_categories_name">
                    <span>мужские джинсы</span>
                </div>
            </a>
            <a href="/" class="block man_svitera">
                 <div class="popular_categories_name">
                    <span>мужские свитера</span>
                </div>
            </a>
        </div>
        <?=$this->template->get_temlate_view('last_reviews_block');?>
        <a href="/" class="registration_baner">
            <div class="top_text">
                2%
            </div>
            <div class="center_text">
            </div>
            <div class="bottom_text">
                Разовая скидка<br> на первую покупку
            </div>
            <div class="mask">
                <div class="top_text">
                    2%
                </div>
                <div class="center_text">
                </div>
                <div class="bottom_text">
                    Разовая скидка<br> на первую покупку
                </div>
            </div>
        </a>

        <div class="our_partners">
            <div class="our_partners_title"><span>Наши</span> партнеры</div>
            <script>
                jssor_slider2_starter = function (containerId) {
                    var options = {
                        $AutoPlay: true,                                    //[Optional] Whether to auto play, to enable slideshow, this option must be set to true, default value is false
                        $AutoPlaySteps: 1,                                  //[Optional] Steps to go for each navigation request (this options applys only when slideshow disabled), the default value is 1
                        $AutoPlayInterval: 3000,                            //[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing, default value is 3000
                        $PauseOnHover: 1,                               //[Optional] Whether to pause when mouse over if a slider is auto playing, 0 no pause, 1 pause for desktop, 2 pause for touch device, 3 pause for desktop and touch device, 4 freeze for desktop, 8 freeze for touch device, 12 freeze for desktop and touch device, default value is 1

                        $ArrowKeyNavigation: true,                          //[Optional] Allows keyboard (arrow key) navigation or not, default value is false
                        $SlideDuration: 500,                                //[Optional] Specifies default duration (swipe) for slide in milliseconds, default value is 500
                        $MinDragOffsetToSlide: 20,                          //[Optional] Minimum drag offset to trigger slide , default value is 20
                        $SlideWidth: 315,                                   //[Optional] Width of every slide in pixels, default value is width of 'slides' container
                        //$SlideHeight: 150,                                //[Optional] Height of every slide in pixels, default value is height of 'slides' container
                        $SlideSpacing: 3,                                   //[Optional] Space between each slide in pixels, default value is 0
                        $DisplayPieces: 4,                                  //[Optional] Number of pieces to display (the slideshow would be disabled if the value is set to greater than 1), the default value is 1
                        $ParkingPosition: 0,                              //[Optional] The offset position to park slide (this options applys only when slideshow disabled), default value is 0.
                        $UISearchMode: 1,                                   //[Optional] The way (0 parellel, 1 recursive, default value is 1) to search UI components (slides container, loading screen, navigator container, arrow navigator container, thumbnail navigator container etc).
                        $PlayOrientation: 1,                                //[Optional] Orientation to play slide (for auto play, navigation), 1 horizental, 2 vertical, 5 horizental reverse, 6 vertical reverse, default value is 1
                        $DragOrientation: 1,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)

                        $BulletNavigatorOptions: {                                //[Optional] Options to specify and enable navigator or not
                            $Class: $JssorBulletNavigator$,                       //[Required] Class to create navigator instance
                            $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                            $AutoCenter: 0,                                 //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                            $Steps: 1,                                      //[Optional] Steps to go for each navigation request, default value is 1
                            $Lanes: 1,                                      //[Optional] Specify lanes to arrange items, default value is 1
                            $SpacingX: 0,                                   //[Optional] Horizontal space between each item in pixel, default value is 0
                            $SpacingY: 0,                                   //[Optional] Vertical space between each item in pixel, default value is 0
                            $Orientation: 1                                 //[Optional] The orientation of the navigator, 1 horizontal, 2 vertical, default value is 1
                        },

                        $ArrowNavigatorOptions: {
                            $Class: $JssorArrowNavigator$,              //[Requried] Class to create arrow navigator instance
                            $ChanceToShow: 1,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                            $AutoCenter: 2,                                 //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                            $Steps: 4                                       //[Optional] Steps to go for each navigation request, default value is 1
                        }
                    };

                    var jssor_slider2 = new $JssorSlider$(containerId, options);

                    //responsive code begin
                    //you can remove responsive code if you don't want the slider scales while window resizes
                    function ScaleSlider() {
                        var bodyWidth = document.body.clientWidth;
                        if (bodyWidth)
                            jssor_slider2.$ScaleWidth(Math.min(bodyWidth, 1280));
                        else
                            window.setTimeout(ScaleSlider, 30);
                    }

                    ScaleSlider();
                    $Jssor$.$AddEvent(window, "load", ScaleSlider);

                    $Jssor$.$AddEvent(window, "resize", $Jssor$.$WindowResizeFilter(window, ScaleSlider));
                    $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
                    ////responsive code end
                };
            </script>
                <div id="slider2_container" style="position: relative; top: 0px; left: 0px; width: 1280px; height: 150px; overflow: hidden;">

                <!-- Loading Screen -->
                <div u="loading" style="position: absolute; top: 0px; left: 0px;">
                    <div style="filter: alpha(opacity=70); opacity:0.7; position: absolute; display: block;
                                background-color: #000; top: 0px; left: 0px;width: 100%;height:100%;">
                    </div>
                    <div style="position: absolute; display: block; background: url(../img/loading.gif) no-repeat center center;
                                top: 0px; left: 0px;width: 100%;height:100%;">
                    </div>
                </div>

                <!-- Slides Container -->
                <div u="slides" style="cursor: move; position: absolute; left: 0px; top: 0px; width:1280px; height: 150px; overflow: hidden;">
                    <div>
                        <div class="partner" >
                        <a href="http://xn----ctbfikcfzfk1af6k.com.ua/" class="dm_logo" target="_blank"></a>
                        <div class="partner_text">Джисовая одежда турецкого производства</div>
                    </div>
                    </div>
                    <div>
                        <div class="partner">
                            <a href="http://ozar.com.ua/" class="logo_ozar" target="_blank"></a>
                        </div>
                    </div>
                    <div style="width:390px">
                        <div class="partner">
                            <a href="http://gbc.ua/" class="logo_portal" target="_blank"></a>
                            <div class="partner_text">Создание и продвижение Интернет-магазинов</div>
                        </div>
                    </div>
                    <div>
                        <div class="partner">
                            <a href="http://jeansa-optom.com.ua/" class="logo_jensa" target="_blank"></a>
                            <div class="partner_text">Джисовая одежда китайского производства</div>
                        </div>
                    </div>
                    <div>
                        <div class="partner">
                            <a href="/" class="logo_kargo"></a>
                        </div>
                    </div>
                
                    
                </div>
                <!-- Arrow Left -->
                <span u="arrowleft" class="partner_arrow_left" style="top: 123px; left: 8px;">
                </span>
                <!-- Arrow Right -->
                <span u="arrowright" class="partner_arrow_right" style="top: 123px; right: 8px;">
                </span>
                <!--#endregion Arrow Navigator Skin End -->
                
                <!-- Trigger -->
                <script>
                    jssor_slider2_starter('slider2_container');
                </script>
            </div>
        </div>
        <div id="show"><?=$this->template->get_temlate_view('center_block');?></div>
    </div>
</div>
<script>
    $('#show').readmore({
        speed: 500,
        maxHeight: 153,
        moreLink: '<a href="#" id="more">Развернуть</a>',
        lessLink: '<a href="#" id="less">Свернуть</a>'
    });
</script>
