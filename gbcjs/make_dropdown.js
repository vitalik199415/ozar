(function($){

  var timeout    = 500;
  var closetimer = 0;
  var ddmenuitem = null;

  // From https://github.com/Modernizr/Modernizr/blob/master/modernizr.js
  var isTouch = ('ontouchstart' in window) ||
                window.DocumentTouch && document instanceof DocumentTouch;

  // jsddm funcs from http://javascript-array.com/scripts/jquery_simple_drop_down_menu/
  function jsddm_open($this){
    jsddm_canceltimer();
    jsddm_close();
    ddmenuitem = $this.find('ul:first').css('visibility', 'visible');
  }

  function doc_jsddm_close() {
    if(ddmenuitem){
      ddmenuitem.css('visibility', 'hidden');
      ddmenuitem = null;
    }
  }
  
  function jsddm_close() {
	if(ddmenuitem){
	ddmenuitem.css('visibility', 'hidden');
	}
  }

  function jsddm_timer() {
    closetimer = window.setTimeout(function() { jsddm_close() }, timeout);
  }

  function jsddm_canceltimer() { 
    if(closetimer)
    {  window.clearTimeout(closetimer);
       closetimer = null;
    }
  }

  function jsddm_toggle($this) {
    if (ddmenuitem && $this.has(ddmenuitem[0]).length){
      jsddm_close();
    }
    else {
      jsddm_open($this);
    }
  }

  $.fn.make_dropdown = function(options){

    return this.each(function(){

      if (options && options['timeout']){
        timeout = options['timeout'];
      }

      $(this).find('li').click(function(event){
        jsddm_toggle($(this));
        event.stopPropagation();
      });
      if (!isTouch){
        $(this).find('li').mouseover(function(){ jsddm_open($(this)) }).mouseout(function(){ jsddm_timer() });
      }

    });
  }

  $(document).click(doc_jsddm_close);

})(jQuery);

