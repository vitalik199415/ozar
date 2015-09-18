(function( $ )
{
	$.fn.gbc_products_detail = function(method, settings)
	{
		var option = new Object(
		{
			favorites_block_id : '#favorites_block',
			to_favorites_button_id : '#to_favorites',
			to_favorites_url : false,
			
			cart_block_id : '#cart_block',
			cart_min_block_id : '#cart_min_block',
			
			to_cart_button_id : '#to_cart',
			cart_id : '#cart_block',
			to_cart_url : false,
			
			message_overlay_id : '#site_messages_overlay',
			message_overlay_top : '35%',
			message_overlay_close : '.close',
			message_overlay_content_id : '#site_messages_overlay_content',
			message_block_id : '#site_message_message_block',
			
			overlay_id : '#product_overlay_content',
			customers_overlay_id : '#customers_overlay_content',
			
			albums_block : '#albums_block',
			prices_block_id : '#prices_block',
			price_block_id : '#price_block',
			price_desc_id : '#price_description',
			prices_radio_id : '#price_radio',
			prices_checked : false,
			prices_attributes : {},
			prices_rules : {},
			albums_attributes : {},
			pcs : 'pcs.',
			real_qty_block : '#real_qty_block',
			attributes_block_id : '#attributes_block',
			attribute_select_tr : '.attribute_select_tr',
			
			product_id : false
		});
		
		var message_block_overlay = $(option.message_overlay_id).overlay(
		{
			api: true,
			oneInstance : false,
			top : option.message_overlay_top,
			close : option.message_overlay_close,
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
					var data = $this.data('products_detail');
					if(!data)
					{
						$this.data('products_detail', true);
						
						methods.init_price_block.apply($this, Array());
						methods.init_attributes_block.apply($this, Array());
						methods.init_albums_block.apply($this, Array());
						methods.init_qty_block.apply($this, Array());
						//methods.init_add_to_cart.apply($this, Array());
						//methods.init_add_to_favorites.apply($this, Array());
						//methods.init_write_admin($this);
					}
				});				
			},
			
			init_price_block : function()
			{
				var $this = $(this);
				$this.find(option.prices_radio_id).not(':checked').parent(option.price_block_id).find(option.price_desc_id).hide();
				
				$price = $this.find(option.prices_radio_id+':checked');
				methods.update_checked_price.apply($this, Array($price));
				methods.update_attributes_block.apply($this, Array($price));
				
				$this.find(option.prices_radio_id).on('change', function()
				{
					$price = $(this);
					methods.update_checked_price.apply($this, Array($price));
					methods.update_attributes_block.apply($this, Array($price));
					methods.show_price_description.apply($this, Array($price));
				});
			},
			
			init_attributes_block : function()
			{
				var $this = $(this);
				$this.find(option.attributes_block_id+' select').on('change', function()
				{
					var $id = $(this).attr('rel');
					var $val = $(this).val();
					$.each(option.albums_attributes, function(i,el)
					{
						if(el['attr'] == $id && el['opt'] == $val)
						{
							$this.find(option.albums_block).find('a[rel='+i+']').trigger('click');
						}
					});
					return false;
				});
			},
			
			init_albums_block : function()
			{
				var $this = $(this);
				$this.find(option.albums_block+' a').on('click', function()
				{
					var $id = $(this).attr('rel');
					if(typeof(option.albums_attributes[$id]) != 'undefined')
					{
						$this.find("select[name='attributes["+option.albums_attributes[$id]['attr']+"]']").val(option.albums_attributes[$id]['opt']);
					}
					return false;
				});
			},
			
			update_attributes_block : function($price)
			{
				var $this = $(this);
				var id = $price.val();
				if(typeof(option.prices_attributes[id]) == "object")
				{
					if(option.prices_attributes[id]['show_attributes'] == 0)
					{
						$this.find(option.attributes_block_id).find('select').prop('disabled', true);
						$this.find(option.attributes_block_id).find('select').parents(option.attribute_select_tr).addClass('disabled');
					}
					else if(option.prices_attributes[id]['show_attributes'] == 1)
					{
						$this.find(option.attributes_block_id).find('select').prop('disabled', false);
						$this.find(option.attributes_block_id).find('select').parents(option.attribute_select_tr).removeClass('disabled');
					}
					else if(option.prices_attributes[id]['show_attributes'] == 2)
					{
						$this.find(option.attributes_block_id).find('select').each(function()
						{
							if(typeof(option.prices_attributes[id]['id_attributes'][$(this).attr('rel')]) != "undefined")
							{
								$(this).prop('disabled', false);
								$(this).parents(option.attribute_select_tr).removeClass('disabled');
							}
							else
							{
								$(this).prop('disabled', true);
								$(this).parents(option.attribute_select_tr).addClass('disabled');
							}
						});
					}
				}
			},
			
			update_checked_price : function($price)
			{
				var $this = $(this);
				option.prices_checked = $price;
				if(typeof(option.prices_rules[$price.val()]) == "object")
				{
					$min_qty = option.prices_rules[$price.val()]['min_qty'];
					$real_qty = option.prices_rules[$price.val()]['real_qty'];
				}
				else
				{
					$min_qty = 1;
					$real_qty = 1;
				}
				option.prices_checked.data('prices_rules', {min_qty : $min_qty, real_qty : $real_qty});
				methods.update_real_qty_block.apply($this, Array($min_qty, $real_qty));
			},
			
			show_price_description : function($price)
			{
				var $this = $(this);
				$this.find(option.price_desc_id+':visible').stop(true, true);
				$this.find($price).parent(option.price_block_id).find(option.price_desc_id).stop(true, true);
				
				$this.find(option.price_desc_id+':visible').slideToggle(1000);
				$this.find($price).parent(option.price_block_id).find(option.price_desc_id).slideToggle(1000);
			},
			
			init_qty_block : function()
			{
				var $this = $(this);
				/*$this.find('input[name=qty]').on('focusout', function()
				{
					$price = option.prices_checked;
					$real_qty = $price.data('prices_rules').real_qty;
					$min_qty = $price.data('prices_rules').min_qty;
					if($(this).val() == '' || $(this).val() < $min_qty)
					{
						methods.update_real_qty_block.apply($this, Array($min_qty, $real_qty));
					}
				});*/
				$this.find('input[name=qty]').on('keyup', function(event)
				{
					$price = option.prices_checked;
					$qty = $(this).val();
					$real_qty = $price.data('prices_rules').real_qty;
					methods.update_real_qty_block.apply($this, Array($qty, $real_qty));
				});
			},
			
			update_real_qty_block : function($qty, $real_qty)
			{
				var $this = $(this);
				if($real_qty != 1 && $qty > 0)
				{
					$this.find(option.real_qty_block).html($real_qty*$qty+' '+option.pcs);
				}
				else
				{
					$this.find(option.real_qty_block).html('');
				}
				$this.find('input[name=qty]').val($qty);
			},
			
			init_add_to_cart : function()
			{
				var $this = $(this);
				$this.find(option.to_cart_button_id).on('click', function()
				{
					if(!option.product_id) return false;
					url = option.to_cart_url;
					
					data = {};
					data['id'] = option.product_id;
					data['price_id'] = $this.find('input[name="price"]:checked').val();
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
						dataType : 'json',
						success: function(d)
						{
							if(d.success == 1)
							{
								$(option.cart_block_id).html(d.cart_html);
								$(option.cart_min_block_id).html(d.cart_min_html);
								$(option.message_overlay_content_id).html(d.site_messages);
								$(option.message_overlay_content_id).find(option.message_block_id).find('#success').css('display', 'block');
								$(option.message_overlay_content_id).find(option.message_block_id).css('display', 'block');
								
								message_block_overlay.load();
								
								setTimeout(function($message_block_overlay)
								{
									message_block_overlay.close();
								}, 8000, message_block_overlay);
							}
							else
							{
								if(typeof(d.available_qty) != "undefined")
								{
									$this.find('input[name="qty"]').val(d.available_qty);
									$price = option.prices_checked;
									$real_qty = $price.data('prices_rules').real_qty;
									methods.update_real_qty_block.apply($this, Array(d.available_qty, $real_qty));
								}
								
								$(option.message_overlay_content_id).html(d.site_messages);
								$(option.message_overlay_content_id).find(option.message_block_id).find('#error').css('display', 'block');
								$(option.message_overlay_content_id).find(option.message_block_id).css('display', 'block');
								
								message_block_overlay.load();
								
								setTimeout(function($message_block_overlay)
								{
									message_block_overlay.close();
								}, 8000, message_block_overlay);
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