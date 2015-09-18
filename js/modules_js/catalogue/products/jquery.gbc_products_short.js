(function( $ )
{
	$.fn.gbc_products_short = function(method, settings)
	{
		var option = new Object(
		{
			favorites_block_id : '#favorites_block',
			to_favorites_button_id : '#to_favorites',
			
			message_overlay_id : '#site_messages_overlay',
			message_overlay_top : '35%',
			overlay_close : '.close',
			
			message_overlay_content_id : '#site_messages_overlay_content',
			message_block_id : '#site_message_message_block',
			
			overlay_id : '#product_overlay_content',
			customers_overlay_id : '#customers_overlay_content'
		});
		
		var message_block_overlay = $(option.message_overlay_id).overlay(
		{
			api: true,
			oneInstance : false,
			top : option.message_overlay_top,
			close: option.overlay_close,
			left: 'left',
			mask: {
				color: '#000000',
				loadSpeed: 200,
				opacity: 0.8
				}
		});
		
		var methods = new Object(
		{
			init : function(settings)
			{
				option = $.extend(option, settings);
				return this.each(function(i,el)
				{
					var $this = $(el);
					var data = $this.data('products_short');
					if(!data)
					{
						$this.data('products_short', true);
						
						methods.init_add_to_favorites.apply($this, Array());
					}
				});
			},
			
			init_add_to_favorites : function()
			{
				$this = $(this);
				$this.find(option.to_favorites_button_id).on('click', function()
				{
					jQuery.ajaxAG({
						url: $(this).attr('rel'),
						type: "GET",
						data: {},
						dataType : 'json',
						success: function(d)
						{
							if(d.success == 1)
							{
								$(option.favorites_block_id).html(d.favorites_html);
								$(option.message_overlay_content_id).html(d.site_messages);
								$(option.message_overlay_content_id).find(option.message_block_id).find('#success').css('display', 'block');
								$(option.message_overlay_content_id).find(option.message_block_id).css('display', 'block');
								message_block_overlay.load();
								setTimeout(function($message_block_overlay)
								{
									message_block_overlay.close();
								}, 3000, message_block_overlay);
							}
							else
							{
								$(option.message_overlay_content_id).html(d.site_messages);
								$(option.message_overlay_content_id).find(option.message_block_id).find('#error').css('display', 'block');
								$(option.message_overlay_content_id).find(option.message_block_id).css('display', 'block');
								message_block_overlay.load();
								setTimeout(function($message_block_overlay)
								{
									message_block_overlay.close();
								}, 3000, message_block_overlay);
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