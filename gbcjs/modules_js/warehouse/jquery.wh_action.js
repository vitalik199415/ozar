(function( $ )
{
$.fn.wh_action = function(method, options)
{
	var option = $.extend(
	{
		order_id : false,
		
		overlay_top : '2%',
		overlay_id : '#overlay_block',
		overlay_content : '#overlay_content',
		
		add_exist_pr : '#add_exist_pr',
		print_wh_pr : '#print_wh_pr'
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
				$(this).find(".tabs_block ul").tabs(".block .block_padding div.field_block", {history: true});
				
				methods.init_add_exist_pr.apply($th, Array());
				methods.init_print_wh_pr.apply($th, Array());
			});
		},
		
		init_add_exist_pr : function()
		{
			$(this).on('click', option.add_exist_pr, function()
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
		
		init_print_wh_pr : function()
		{
			$(this).on('click', option.print_wh_pr, function()
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
}	
})(jQuery);	