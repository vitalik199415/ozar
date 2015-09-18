(function( $ )
{
$.fn.view_wh_transfer = function(method, options)
{
	var option = $.extend(
	{
		order_id : false,
		
		overlay_top : '2%',
		overlay_id : '#overlay_block',
		overlay_content : '#overlay_content',
		
		view_wh_transfer : '.view_wh_transfer',
		view_wh_transfer_pr : '.overlay_view_wh_transfer_pr',
		back_to_wh_transfer : '.back_to_wh_transfer'
		
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
				methods.init_view_transfer.apply($th, Array());
				methods.init_view_transfer_prod();
				methods.init_back_to_wh_transfer();
			});
		},
		
		init_view_transfer : function()
		{
			$(this).on('click', option.view_wh_transfer, function()
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
		
		init_view_transfer_prod : function()
		{
			$(option.overlay_content).on('click', option.view_wh_transfer_pr, function()
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
		
		init_back_to_wh_transfer : function()
		{
			$(option.overlay_content).on('click', option.back_to_wh_transfer, function()
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
		$.error( '����� ' +  method + ' �� ����������' );
	}
	return $this;
}	
})(jQuery);	