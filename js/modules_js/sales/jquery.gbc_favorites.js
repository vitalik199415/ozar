(function( $ )
{
$.fn.gbc_favorites = function(method, option)
{
	var option = $.extend(
	{
		favorites_overlay_id : '#favorites_overlay',
		favorites_overlay_content_id : '#favorites_overlay_content',
		favorites_overlay_top : '9%',
		overlay_close : '.close',
		
		favorites_delete_item : '#favorites_delete_item',
		
		favorites_block_id : '#favorites_block',
		favorites_button_id : '#show_favorites_products',
		
		message_overlay_id : '#site_messages_overlay',
		message_overlay_top : '35%',
		message_overlay_content_id : '#site_messages_overlay_content',
		message_block_id : '#site_message_message_block',
		
		message_block_id : '#favorites_edit_message_block',
		success_block : '.success_message',
		error_block : '.error_message',
		
		add_to_favorites_id : '#add_to_favorites'
	}, option);
	
	var favorites_block_overlay = $(option.favorites_overlay_id).overlay({
		api: true,
		oneInstance : false,
		close : option.overlay_close,
		top : option.favorites_overlay_top,
		left: 'left',
		mask: {
			zIndex : 9000,
			color: '#000000',
			loadSpeed: 200,
			opacity: 0.8
		}
	});
	
	var methods = 
	{
		init : function()
		{
			return this.each(function(i,el)
			{
				var $this = $(el);
				data = $this.data('gbc_favorites');
				if(!data)
				{
					$(this).data('gbc_favorites',{
						target : $this
					});
					methods.init_favorites_block.apply($this, Array());
					methods.init_delete_item_button();
				}
			})
		},
		
		init_favorites_block : function()
		{
			var $this = $(this);
			$this.on('click', option.favorites_button_id, function()
			{
				jQuery.ajaxAG({
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					dataType : 'json',
					success: function(d)
					{
						if(d.success == 1)
						{
							$(option.favorites_overlay_content_id).html(d.favorites_products_html);
							favorites_block_overlay.load();
						}
						else
						{
							return false;
						}
					}
				});
				return false;
			});
		},
		
		init_delete_item_button : function()
		{
			$(option.favorites_overlay_content_id).on('click', option.favorites_delete_item, function()
			{
				methods.hide_message_block();
				jQuery.ajaxAG({
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					dataType : 'json',
					success: function(d)
					{
						if(d.success == 1)
						{
							$(option.favorites_overlay_content_id).html(d.favorites_products_html);
							$(option.favorites_block_id).html(d.favorites_html);
							
							methods.show_success(d.site_messages);
							var $timeoutId = setTimeout(methods.hide_message_block, 5000);
						}
						else
						{
							methods.show_errors(d.site_messages);
							var $timeoutId = setTimeout(methods.hide_message_block, 5000);
						}
					}
				});
				return false;
			});
		},
		
		show_errors : function(errors)
		{
			$(option.message_block_id).find(option.error_block).find('div').html(errors);
			$(option.message_block_id).find(option.error_block).show();
			$(option.message_block_id).show();
		},
		
		show_success : function(success)
		{
			$(option.message_block_id).find(option.success_block).find('div').html(success);
			$(option.message_block_id).find(option.success_block).show();
			$(option.message_block_id).show();
		},
		
		hide_message_block : function()
		{
			if(typeof($timeoutId) != "undefined")
			{
				clearTimeout($timeoutId);
			}
			$(option.message_block_id).find(option.error_block).find('div').html('');
			$(option.message_block_id).find(option.success_block).find('div').html('');
			$(option.message_block_id).find(option.error_block).hide();
			$(option.message_block_id).find(option.success_block).hide();
			$(option.message_block_id).hide();
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