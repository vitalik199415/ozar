<div class="content" align="center">
    <div class="work">
        <div class="navigation">
            <?=$this->load->view('navigation',array(), TRUE);?>
        </div>
        <div class="left_block">
            <?=$this->template->get_temlate_view('last_news_block');?>
            <?=$this->template->get_temlate_view('last_reviews_block');?>
        </div>
        <div class="right_block">
        	<div class="intermediate_page_padding">
	            <div class="jeans_page_slideshow">
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
		                        jssor_slider1.$ScaleWidth(Math.min(bodyWidth, 945));
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
		                    <script>
		                        jssor_slider1_starter('slider1_container');
		                    </script>
		                    <!-- Jssor Slider End -->
		                </div>
		            </div>
	            </div>
	        </div>
	        <div class="intermediate_page_block">
	        	<div class="title">Мужская джинсовая одежда</div>
	        	<div class="block">
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/man/1.jpg">
	        			<div class="links">
	        				<div class="title">Мужская одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/man/2.jpg">
	        			<div class="links">
	        				<div class="title">Большие размеры</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/man/3.jpg">
	        			<div class="links">
	        				<div class="title">Подростковая одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/man/4.jpg">
	        			<div class="links">
	        				<div class="title">Детская джинсовая одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        	</div>
	        </div>
	        <div class="intermediate_page_block">
	        	<div class="title">Женская джинсовая одежда</div>
	        	<div class="block">
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/woman/1.jpg">
	        			<div class="links">
	        				<div class="title">Мужская одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/woman/2.jpg">
	        			<div class="links">
	        				<div class="title">Большие размеры</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/woman/3.jpg">
	        			<div class="links">
	        				<div class="title">Подростковая одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/woman/4.jpg">
	        			<div class="links">
	        				<div class="title">Детская джинсовая одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        	</div>
	        </div>
	        <div class="intermediate_page_block">
	        	<div class="title">Джинсовая одежда со скидкой</div>
	        	<div class="block">
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/sale/1.jpg">
	        			<div class="links">
	        				<div class="title">Мужская одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/sale/2.jpg">
	        			<div class="links">
	        				<div class="title">Большие размеры</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/sale/3.jpg">
	        			<div class="links">
	        				<div class="title">Подростковая одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        		<div class="links_block">
	        			<img src="/users_app/777/img/jeans_page/sale/4.jpg">
	        			<div class="links">
	        				<div class="title">Детская джинсовая одежда</div>
	        				<a href="/">Джинсы</a>
	        				<a href="/">Бриджи/Шорты</a>
	        				<a href="/">Рубашки</a>
	        				<a href="/">Пиджаки</a>
	        			</div>
	        		</div>
	        	</div>
	        </div>
        </div>

    </div>
    <div class="our_partners">
        <div class="our_partners_title"><span>Наши</span> партнеры</div>
        <div class="partner">
            <a href="/" class="dm_logo"></a>
            <div class="partner_text">Джисовая одежда турецкого производства</div>
        </div>
        <div class="partner">
            <a href="/" class="logo_ozar"></a>
        </div>
        <div class="partner">
            <a href="/" class="logo_portal"></a>
            <div class="partner_text">Создание и продвижение Интернет-магазинов</div>
        </div>
        <div class="partner">
            <a href="/" class="logo_jensa"></a>
            <div class="partner_text">Джисовая одежда китайского производства</div>
        </div>
    </div>
</div>