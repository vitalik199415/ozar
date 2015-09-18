(function( $ )
{
$.fn.view_wh_logs = function(method, options)
{
	var option = $.extend(
	{
		order_id : false,
		
		overlay_top : '2%',
		overlay_id : '#overlay_block',
		overlay_content : '#overlay_content',
		
		view_wh_log : '.view_wh_log',
		view_wh_log_pr : '.overlay_view_wh_log_pr',
		back_to_wh_sale : '.back_to_wh_sale'
		
	},options);
	
	var overlay = $(option.overlay_id).overlay(
	{
		api: true, 
		oneInstance : false,
		top : option.overlay_top,
		left: 'left',
		mask: {
			color: '#000000',
			loadSpeed: 200,
			opacity: 0.8
			}
	});
	
	var methods = new Object(
	{
		init : function(option)
		{
			return this.each(function(i,el)
			{
				$th = $(el);
				methods.init_view_sale.apply($th, Array());
				methods.init_view_sale_prod();
				methods.init_back_to_wh_sale();
			});
		},
		
		init_view_sale : function()
		{
			$(this).on('click', option.view_wh_sale, function()
			{
				jQuery.ajaxAG(
				{
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
							$(option.overlay_id).find(option.overlay_content).html(data.html);
							overlay.load();
						}
					}
				});
				return false;
			});
		},
		
		init_view_sale_prod : function()
		{
			$(option.overlay_content).on('click', option.view_wh_sale_pr, function()
			{
				jQuery.ajaxAG(
				{
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
							$(option.overlay_id).find(option.overlay_content).html(data.html);
							overlay.load();
						}
					}
				});
				return false;
			});
		},
		
		init_back_to_wh_sale : function()
		{
			$(option.overlay_content).on('click', option.back_to_wh_sale, function()
			{
				jQuery.ajaxAG(
				{
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
							$(option.overlay_id).find(option.overlay_content).html(data.html);
						}
					}
				});
				return false;
			});
		}
	});
	
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
	return $this;
}	
})(jQuery);	