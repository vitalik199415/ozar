<div class="content" align="center">
    <div class="work">
        <div class="navigation">
            <?=$this->load->view('navigation',array(), TRUE);?>
        </div>
        <div class="left_block">
            <?=$this->template->get_temlate_view('categories_block');?>
            <?=$this->template->get_temlate_view('types_block');?>
        </div>
		<script>
             //$(document).ready(function() {
             // $('.filters_block').scrollToFixed({
			//	  bottom: 270,
			//	  limit: $('.footer').offset().top
			//  });
          //  });
        </script>
        <div class="right_block">
            <?=$this->template->get_temlate_view('center_block');?>
            <div class="popular_categories_block">
                <a href="/category-linens" class="block post_belyo">
                    <div class="popular_categories_name">
                        <span>постельное белье</span>
                    </div>
                </a>
                <a href="/category-mens-shoes" class="block mans_shoose">
                    <div class="popular_categories_name">
                        <span>мужская обувь</span>
                    </div>
                </a>
                <a href="/category-womens-shoes" class="block wooman_shoose">
                    <div class="popular_categories_name">
                        <span>женская обувь</span>
                    </div>
                </a>
            </div>
        </div>        
    </div>
</div>