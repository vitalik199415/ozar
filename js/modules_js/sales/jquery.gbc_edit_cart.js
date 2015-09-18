(function( $ )
{
var option = new Object(
{
	cart_overlay_id : '#cart_overlay',
	cart_overlay_cn : '#cart_overlay_content',
	overlay_top : '9%',
	overlay_close : '.close',
	
	cart_id : '#cart_block',
	cart_min_id : '#cart_min_block',
	cart_create_order : '#cart_create_order_button',
	
	cart_edit : '.cart_edit_edit_item',
	cart_delete : '.cart_edit_delete_item',
	cart_checkout : '.cart_edit_checkout',
	
	message_block_id : '#cart_edit_message_block',
	success_block : '.success_message',
	error_block : '.error_message',
	
	url_edit_item : false,
	url_delete_item : false
});

$.fn.gbc_edit_cart = function(method, settings)
{	
	var methods = new Object(
	{
		init : function(settings)
		{
			option = $.extend(option, settings);
			return this.each(function(i,el)
			{
				var $this = $(el);
				var data = $this.data('gbc_edit_cart');
				if(!data)
				{	
					$this.data('gbc_edit_cart', true);
					methods.init_cart_edit_buttons.apply($this, Array());
				}
			});
		},
		
		init_cart_edit_buttons : function()
		{
			var $this = $(this);
			$this.find(option.cart_edit).click(function()
			{
				methods.hide_message_block();
				methods.edit_item($(this));
				return false;
			});
			
			$this.find(option.cart_delete).click(function()
			{
				methods.hide_message_block();
				methods.delete_item($(this));
				return false;
			});
			
			$this.find(option.cart_checkout).click(function()
			{
				$(option.cart_overlay_id).find(option.overlay_close).trigger('click');
				$(option.cart_id).find(option.cart_create_order).trigger('click');
				return false;
			});
			
		},
		
		edit_item : function($item)
		{
			var data = {};
			var block = $item.parents('tr');
			data['rowid'] = block.find('input[name="rowid"]').val();
			data['qty'] = block.find('input[name="qty"]').val();
			if(option.url_edit_item !== false)
			{
				jQuery.ajaxAG(
				{
					url: option.url_edit_item,
					type: "POST",
					data: data,
					dataType : "json",
					success: function(d)
					{
						if(d.success == 1)
						{
							$(option.cart_overlay_cn).html(d.cart_edit_html);
							$(option.cart_id).html(d.cart_html);
							$(option.cart_min_id).html(d.cart_min_html);
							
							methods.show_success(d.site_messages);
							
							var $timeoutId = setTimeout(methods.hide_message_block, d.delay);
						}
						else
						{
							if(typeof(d.available_qty) != "undefined")
							{
								block.find('input[name="qty"]').val(d.available_qty);
							}
							
							methods.show_errors(d.site_messages);
							var $timeoutId = setTimeout(methods.hide_message_block, d.delay);
						}
					}
				});
			}
		},
		
		delete_item : function($item)
		{
			var data = {};
			var block = $item.parents('tr');
			data['rowid'] = block.find('input[name="rowid"]').val();
			if(option.url_delete_item !== false)
			{
				jQuery.ajaxAG(
				{
					url: option.url_delete_item,
					type: "POST",
					data: data,
					dataType : "json",
					success: function(d)
					{
						if(d.success == 1)
						{
							$(option.cart_overlay_cn).html(d.cart_edit_html);
							$(option.cart_id).html(d.cart_html);
							$(option.cart_min_id).html(d.cart_min_html);
							
							methods.show_success(d.site_messages);
							var $timeoutId = setTimeout(methods.hide_message_block, d.delay);
						}
						else
						{
							$(option.cart_overlay_cn).html(d.cart_edit_html);
							$(option.cart_id).html(d.cart_html);
							$(option.cart_min_id).html(d.cart_min_html);
							
							methods.show_errors(d.site_messages);
							var $timeoutId = setTimeout(methods.hide_message_block, d.delay);
						}
					}
				});
			}
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