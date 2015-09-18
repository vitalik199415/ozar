(function( $ )
{
	$.fn.gbc_products_full = function(option)
	{
		var option = $.extend(
		{
			cart_id : '#cart_block',
			overlay_id : '#product_overlay_content',
			customers_overlay_id : '#customers_overlay_content',
			
			price_attributes : {},
			product_id : false,
			add_item_url : false,
			write_admin_url : false
		},option);
		
		var product_block = $('#product_overlay').overlay({
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
		
		var methods = 
		{
			init : function()
			{
				return this.each(function()
				{
					var $this = $(this);
					data = $this.data('products_full');
					if(!data)
					{
						$(this).data('products_full',{
							target : $this,
						});
						methods.init_price($this);
						methods.init_write_admin($this);
						//methods.init_buttons($this);
					}
				})
			},
			destroy : function($this)
			{
				return $this.each(function(){
					var $this = $(this),
					data = $this.data('products_full');
					
					$this.removeData('products_full');
				})
			},
			init_price : function($this)
			{
				$this.find('.price_select').change(function()
				{
					var id = $(this).val();
					if(option.price_attributes[id]['show_attributes'] == 0)
					{
						$this.find('.attributes_select').removeClass('visible');
						$this.find('.attributes_select').addClass('hidden');
					}
					else if(option.price_attributes[id]['show_attributes'] == 1)
					{
						$this.find('.attributes_select').removeClass('hidden');
						$this.find('.attributes_select').addClass('visible');
					}
					else if(option.price_attributes[id]['show_attributes'] == 2)
					{
						$this.find('.attributes_select').each(function()
						{
							if(option.price_attributes[id]['id_attributes'][$(this).attr('rel')])
							{
								$(this).removeClass('hidden');
								$(this).addClass('visible');
							}
							else
							{
								$(this).removeClass('visible');
								$(this).addClass('hidden');
							}
						});
					}
				});
				methods.add_to_cart($this);
			},
			
			init_write_admin : function($this)
			{
				if(option.write_admin_url !== false)
				{
					$this.find('#products_write_admin').click(function()
					{
						var customers_block = $('#customers_overlay').overlay({
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
						jQuery.ajaxAG(
						{
							url: option.write_admin_url,
							type: "POST",
							data: data,
							success: function(d)
							{
								$(option.customers_overlay_id).html(d);
								customers_block.load();
							}
						});
						return false;
					});
				}	
			},
			
			add_to_cart : function($this)
			{
				$this.find('#to_cart').click(function()
				{
					url = option.add_item_url;
					
					data = {};
					data['id'] = option.product_id;
					data['price'] = $this.find('input[name="price"]:checked').val();
					data['attributes'] = {};
					
					$this.find('select[class="select_attributes"]').each(function()
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
							$(option.cart_id).html(d);
						}
					});
					return false;
				});
			}
		}
		
		return methods.init.apply( this );
		/*if ( methods[method] )
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
		}*/
	}
})(jQuery);