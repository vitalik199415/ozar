(function( $ )
{
	$.fn.gbc_show_product = function(method, settings)
	{
		var option = new Object(
		{	
			overlay_id : '#overlay_block',
			overlay_content_id : '#overlay_content',
			overlay_top : '2%',
			overlay_close : '.close',
			
			show_product_link : '.show_product_link'
		});
		
		var block_overlay = $(option.overlay_id).overlay(
		{
			api: true,
			oneInstance : true,
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
			init : function(settings)
			{
				option = $.extend(option, settings);
				return this.each(function(i,el)
				{
					var $this = $(el);
					var data = $this.data('gbc_show_product');
					if(!data)
					{
						$this.data('gbc_show_product', true);
						
						methods.init_link.apply($this, Array());
					}
				});				
			},
			
			init_link : function()
			{
				var $this = $(this);
				$this.on('click', option.show_product_link, function()
				{
					methods.show_product.apply($(this), Array());
					return false;
				});
			},
			
			show_product : function()
			{
				var $this = $(this);
				url = $this.attr('href');
					
				jQuery.ajaxAG(
				{
					url: url,
					type: "GET",
					data: data,
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
							$(option.overlay_content_id).html(data.html);
							block_overlay.load();
						}
					}
				});
				return false;
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