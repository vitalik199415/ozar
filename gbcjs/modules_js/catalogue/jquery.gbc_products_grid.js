(function( $ )
{
$.fn.gbc_products_grid = function(method, option)
{
	var option = $.extend(
	{
		grid_id : '#products_grid',
		products_view : '.products_view',
		
		view_product_block : '#view_product_block',
		product_block_price_select : '.price_select',
		
		overlay_top : '2%',
		overlay_id : '#overlay_block',
		overlay_content : '#overlay_content'
	},option);
	
	var products_overlay = $(option.overlay_id).overlay(
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
		init_products_grid : function()
		{
			return this.each(function(i,el)
			{
				//console.log(option);
				$this = $(el);
				$(this).on('click', option.products_view, function()
				{
					methods.view_product.apply($this, Array($(this).attr('href')));
					return false;
				});
			});
		},
		
		view_product : function(href)
		{
			jQuery.ajaxAG(
			{
				url: href,
				type: "GET",
				data: {},
				dataType : 'json',
				success: function(data)
				{
					if(data.success == 1)
					{
						$(option.overlay_id).find(option.overlay_content).html(data.html);
						products_overlay.load();
					}
				}
			});
		},
		
		init_product_view : function(price_attributes)
		{
			var $product_block = $(option.view_product_block);
			
			$product_block.find(option.product_block_price_select).change(function()
			{
				var id = $(this).val();
				if(price_attributes[id]['show_attributes'] == 0)
				{
					$product_block.find('.attributes_select').closest('tr').addClass('hidden');
				}
				else if(price_attributes[id]['show_attributes'] == 1)
				{
					$product_block.find('.attributes_select').closest('tr').removeClass('hidden');
				}
				else if(price_attributes[id]['show_attributes'] == 2)
				{
					$product_block.find('.attributes_select').each(function()
					{
						if(price_attributes[id]['id_attributes'][$(this).attr('rel')])
						{
							$(this).closest('tr').removeClass('hidden');
						}
						else
						{
							$(this).closest('tr').addClass('hidden');
						}
					});
				}
			});
		}
	});
	
	if ( methods[method] )
	{
		return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
	}
	else if ( typeof method === 'object' || ! method )
	{
		return methods.init_products_grid.apply( this, arguments );
	}
	else
	{
		$.error( 'Метод ' +  method + ' не существует' );
	}
}	
})(jQuery);	