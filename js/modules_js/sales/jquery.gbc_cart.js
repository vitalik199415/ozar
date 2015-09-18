(function( $ )
{
$.fn.gbc_cart = function(method, option)
{
	var option = new Object(
	{
		cart_overlay_id : '#cart_overlay',
		cart_overlay_cn : '#cart_overlay_content',
		
		order_overlay_id : '#order_overlay',
		order_overlay_cn : '#order_overlay_content',
		
		overlay_top : '9%',
		overlay_close : '.close',
		
		cart_id : '#cart_block',
		cart_min_id : '#cart_min_block',
		cart_edit : '#cart_edit_button, #cart_edit_button1',
		cart_create_order : '#cart_create_order_button, #cart_create_order_button1',
		
		url_login_form : false,
		url_edit_item : false,
		url_delete_item : false,
		
		success_create_order : false,
		error_submit : false,
		error_create_order : false
	});
	
	var cart_overlay = $(option.cart_overlay_id).overlay({
	api: true, 
	oneInstance : true,
	close : option.overlay_close,
	top : option.overlay_top,
	left: 'left',
	mask: {
		zIndex : 9000,
		color: '#000000',
		loadSpeed: 200,
		opacity: 0.8
		}
	});
	
	var order_overlay = $(option.order_overlay_id).overlay({
	api: true, 
	oneInstance : true,
	close : option.overlay_close,
	top : option.overlay_top,
	left: 'left',
	mask: {
		color: '#000000',
		loadSpeed: 200,
		opacity: 0.8
		}
	});
	
	var methods = 
	{
		init : function(settings)
		{
			option = $.extend(option, settings);
			return this.each(function(i,el)
			{
				var $this = $(el);
				var data = $this.data('gbc_cart');
				if(!data)
				{	
					$this.data('gbc_cart', true);
					methods.init_cart_block.apply($this, Array());
				}
			});
		},
		
		init_cart_block : function()
		{
			$this = $(this);
			$this.on('click', option.cart_edit ,function()
			{
				methods.edit_cart();
				return false;
			});
			$this.on('click', option.cart_create_order, function()
			{
				methods.create_order();
				return false;
			});
		},
		
		edit_cart : function()
		{
			jQuery.ajaxAG(
			{
				url: option.edit_cart_url,
				type: "GET",
				data: {},
				success: function(d)
				{
					$(option.cart_overlay_cn).html(d);
					cart_overlay.load();
				}
			});
		},
		
		create_order : function()
		{
			jQuery.ajaxAG(
			{
				url: option.create_order_url,
				type: "GET",
				data: {},
				success: function(d)
				{
					$(option.order_overlay_cn).html(d);
					order_overlay.load();
				}
			});
		}
	}	
	
	if ( methods[method] )
	{
		return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
	}
	else if ( typeof method === 'object' || ! method )
	{
		return methods.init.apply( this, arguments );
	}
	else
	{
		$.error( 'Метод ' +  method + ' не существует' );
	}
}	
})(jQuery);	