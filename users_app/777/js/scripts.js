jQuery(document).ready(function(){
	var p1=false;
	var p2=false;
	jQuery(".btn-contact").click(function(){
		if(!p1){
			jQuery(this).parent(".contact-main").addClass("show");
			p1=true;
		}else{
			jQuery(this).parent(".contact-main").removeClass("show");
			p1=false;
			p2=false;
		}
		if(p2){
			jQuery(this).parent(".contact-main").next().removeClass("show");
	}});
	jQuery(".btn-log").click(function(){
		if(!p2){
			jQuery(this).parent(".account-login-home").addClass("show");
			p2=true;
		}else{
			jQuery(this).parent(".account-login-home").removeClass("show");
			p1=false;p2=false;
	}});
	jQuery(document).click(function(event){
		if(p1||p2){
			if(jQuery(event.target).closest(".contact-main").length||jQuery(event.target).closest(".account-login-home").length)
		return;
	jQuery(".contact-main").removeClass("show");
	jQuery(".account-login-home").removeClass("show");
	p1=false;
	p2=false;
	event.stopPropagation();
	}});
	
	$(document).ready(function() {
	  $('a.nudge').hover(function() { //mouse in
		$(this).animate({ paddingLeft: '10px' }, 400);
	  }, function() { //mouse out
		$(this).animate({ paddingLeft: 0 }, 400);
	  });
	});
	
	$(document).ready(function() {
	  $('a.social').hover(function() { //mouse in
		$(this).animate({ marginTop: '-5px' }, 400);
	  }, function() { //mouse out
		$(this).animate({ marginTop: 0 }, 400);
	  });
	});
	
	jQuery(window).load(function() {
	  jQuery('.slider')._TMS({
		duration:800,
		easing:'easeOutQuad',
		preset:'random',
		pagination:'.pagination',
		nextBu:'.slider-next',
		prevBu:'.slider-prev',
		slideshow:4000,
		banners:true,
		waitBannerAnimation:false,
		pauseOnHover:true
		});
	});	
})