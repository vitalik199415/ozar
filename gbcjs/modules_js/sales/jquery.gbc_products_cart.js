(function( $ )
{
	$.fn.gbc_products_cart = function(method, option)
	{
		var option = $.extend(
		{
			price_attributes : {}
		},option);
		
		var methods = 
		{
			init : function()
			{
				return this.each(function()
				{
					var $this = $(this);
					data = $this.data('create_grid');
					
					if(!data)
					{
						$(this).data('create_grid',{
							target : $this,
							form : $this.find('form')
						});
						methods.init_fields($this);
						methods.init_buttons($this);
					}
				})
			},
			destroy : function($this)
			{
				return $this.each(function(){
					var $this = $(this),
					data = $this.data('create_grid');
					
					$this.removeData('create_grid');
				})
			},
			init_add_product : function()
			{
				var $this = $(this);
				$this.find('input[type="radio"][name="price"]').change(function()
				{
					var id = $(this).val();
					if(option.price_attributes[id]['show_attributes'] == 0)
					{
						$this.find('.attributes_select').css('display','none');
					}
					else if(option.price_attributes[id]['show_attributes'] == 1)
					{
						$this.find('.attributes_select').css('display','block');
					}
					else if(option.price_attributes[id]['show_attributes'] == 2)
					{
						$this.find('.attributes_select').each(function()
						{
							if(option.price_attributes[id]['id_attributes'][$(this).attr('rel')])
							{
								$(this).css('display','block');
							}
							else
							{
								$(this).css('display','none');
							}
						});
					}
				});
				methods.add_to_cart($this);
			},
			add_to_cart : function($this)
			{
				$this.find('#to_cart').click(function()
				{
					url = $(this).attr('href');
					
					data = {};
					data['id'] = $this.find('input[name="id"]').val();
					data['price'] = $this.find('input[name="price"]:checked').val();
					data['attributes'] = {};
					
					$this.find('select').each(function()
					{
						data['attributes'][$(this).attr('rel')] = $(this).val();
					});
					data['qty'] = $this.find('input[name="qty"]').val();
					
					jQuery.ajaxAG(
					{
						url: url,
						type: "POST",
						data: data,
						success: function(d)
						{
							$('#cart').html(d);
						}
					});
					return false;
				});
			},
			init_cart : function()
			{
				var $this = $(this);
				$this.find('#delete_cart_items').click(function()
				{
					var url = $(this).attr('href');
					var checkbox = $this.find('.cart_products:checked');
					if(checkbox.length > 0)
					{
						var AJAXsend = {};
						var checkbox_array = {};
						$.each(checkbox,
							function(i)
							{
								checkbox_array[i] = $(checkbox[i]).val();
							}
						);
						AJAXsend['cart_products'] = checkbox_array;
						jQuery.ajaxAG(
						{
							url: url,
							type: "POST",
							data: AJAXsend,
							success: function(d)
							{
								$('#cart').html(d);
							}
						});
					}	
					return false;
				});
			}
		}
		
		//return methods.init.apply( this );
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