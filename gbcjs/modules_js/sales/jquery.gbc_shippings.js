(function( $ )
{
$.fn.gbc_shippings = function(method, option)
{
	$this = this;
	$JQ_this = $(this);

	this.option = $.extend(
	{
		overlay_top : '2%',
		overlay_id : '#overlay_block',
		overlay_content : '#overlay_content',
		view_product_class : '.view_product',
		show_products_with_photo : '#show_products_with_photo',
		
		view_product_block : '#view_product',
		product_block_price_select : '.price_select'
	},option);
	
	this.order_overlay = $($this.option.overlay_id).overlay(
	{
		api: true, 
		oneInstance : false,
		top : $this.option.overlay_top,
		left: 'left',
		mask: {
			color: '#000000',
			loadSpeed: 200,
			opacity: 0.8
			}
	});
	
	this.methods = new Object(
	{
		init_view_shipping : function(option)
		{
			$JQ_this.on('click', $this.option.view_product_class, function()
			{
				$this.methods.view_product($(this).attr('href'));
				return false;
			});
			$JQ_this.on('click', $this.option.show_products_with_photo, function()
			{
				$this.methods.show_products_with_photo($(this).attr('href'));
				return false;
			});
			return $this;
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
						$($this.option.overlay_id).find($this.option.overlay_content).html(data.html);
						$this.order_overlay.load();
					}
				}
			});
		},
		
		show_products_with_photo : function(href)
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
						$($this.option.overlay_id).find($this.option.overlay_content).html(data.html);
						$this.order_overlay.load();
					}
				}
			});
		},
		
		init_view_product : function(price_attributes)
		{
			var $product_block = $($this.option.view_product_block);
			
			$product_block.find($this.option.product_block_price_select).change(function()
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
	
	if ( this.methods[method] )
	{
		return this.methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
	}
	else if ( typeof method === 'object' || ! method )
	{
		return this.methods.init.apply( this, arguments );
	}
	else
	{
		$.error( 'Метод ' +  method + ' не существует' );
	}
	return $this;
}	
})(jQuery);	