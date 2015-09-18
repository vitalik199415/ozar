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
			
			product_id : false,
			write_admin_url : false,
			
			add_pr_timeout_id : false,

			to_waitlist_url: false,
			waitlist_id: '#waitlist_overlay',
			waitlist_content_id: '#waitlist_overlay_content',
			waitlist_message_block_id: '#waitlist_message_block',
			to_waitlist_id: '#to_waitlist',
			error_submit: false,
			success_block_id : '.success_message',
			error_block_id : '.error_message'
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

		var waitlist_block = $('#waitlist_overlay').overlay({
			api: true,
			oneInstance : false,
			top : option.overlay_top,
			close : option.overlay_close,
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
					var data = $this.data('products_detail'+option.product_id);
					if(!data)
					{
						$this.data('products_detail'+option.product_id, true);
						
						methods.init_price_block.apply($this, Array());
						methods.init_attributes_block.apply($this, Array());
						methods.init_albums_block.apply($this, Array());
						methods.init_qty_block.apply($this, Array());
						methods.init_add_to_cart.apply($this, Array());
						methods.init_add_to_favorites.apply($this, Array());
						methods.init_write_admin($this);
						methods.init_waitlist($this);
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
				//$this.find('input[name=qty]').inputmask("Regex", {regex : "^([1-9][0-9]{5})$"});
				$this.find('input[name=qty]').on('focusout', function()
				{
					$price = option.prices_checked;
					$real_qty = $price.data('prices_rules').real_qty;
					$min_qty = $price.data('prices_rules').min_qty;
					if($(this).val() == '' || $(this).val() < $min_qty)
					{
						methods.update_real_qty_block.apply($this, Array($min_qty, $real_qty));
					}
				});
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
							data: {},
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

			init_waitlist : function($this)
			{
				if(option.to_waitlist_url !== false)
				{

					$this.find(option.to_waitlist_id).click(function()
					{

						jQuery.ajaxAG({
							url: option.to_waitlist_url,
							type: "POST",
							method: "POST",
							data: {},
							dataType : 'json',
							success: function(d)
							{
								if(d.success == 2) {
									$(option.message_overlay_content_id).html(d.site_messages);
									$(option.message_overlay_content_id).find(option.message_block_id).find('#success').css('display', 'block');
									$(option.message_overlay_content_id).find(option.message_block_id).css('display', 'block');
									message_block_overlay.load();
									setTimeout(function($message_block_overlay)
									{
										message_block_overlay.close();
									}, 3000, message_block_overlay);
								} else
								if(d.success == 1)
								{
									$(option.waitlist_content_id).html(d.wait_form_html);
									waitlist_block.load();
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
			},

			init_waitlist_send_email : function(err)
			{
				$this = $(this);
				$this.validate({
					rules : {
						"email" : 			{required: true, email: true}
					},
					errorPlacement: function(error, element) {
						error.insertAfter(element.parent('div').find('div'));
					}
				});

				$this.find('#submit').click(function()
				{
					methods.hide_message_block.apply($this);
					var options = {
						beforeSubmit: function() { return methods.form_validate.apply($this, Array(err)) },
						success: methods.form_waitlist_success,
						dataType:  'json'
					};
					$($this).ajaxSubmit(options);
					return false;
				});
			},

			form_waitlist_success : function(responseText, statusText, xhr, $form)
			{
				var $this = $($form);
				if(responseText.success == 0)
				{

					methods.show_errors.apply($this, Array(responseText.site_messages));
				}
				if(responseText.success == 2)
				{

					methods.show_success.apply($this, Array(responseText.site_messages));
				}
				loading_stop();
			},

			form_validate : function(error)
			{
				var $this = $(this);
				if($this.valid())
				{
					loading_start();
					return true;
				}
				else
				{
					methods.show_errors.apply($this, Array(error));
					return false;
				}
			},

			show_errors : function(errors)
			{
				console.log(errors);
				var $this = $(this);
				$this.find(option.waitlist_message_block_id).find(option.error_block_id).find('div').html(errors);
				$this.find(option.waitlist_message_block_id).find(option.error_block_id).show();
				$this.find(option.waitlist_message_block_id).show();
				setTimeout(function() {
					$this.find(option.waitlist_message_block_id).find(option.error_block_id).find('div').html('');
					$this.find(option.waitlist_message_block_id).find(option.error_block_id).hide();
					$this.find(option.waitlist_message_block_id).hide();
					waitlist_block.close();
				}, 4000);
			},

			show_success : function(success)
			{
				var $this = $(this);
				$this.find(option.waitlist_message_block_id).find(option.success_block_id).find('div').html(success);
				$this.find(option.waitlist_message_block_id).find(option.success_block_id).show();
				$this.find(option.waitlist_message_block_id).show();
				setTimeout(function() {
					$this.find(option.waitlist_message_block_id).find(option.success_block_id).find('div').html('');
					$this.find(option.waitlist_message_block_id).find(option.success_block_id).hide();
					$this.find(option.waitlist_message_block_id).hide();
					waitlist_block.close();
				}, 4000);
			},

			init_add_to_favorites : function()
			{
				var $this = $(this);
				$this.find(option.to_favorites_button_id).on('click', function()
				{
					jQuery.ajaxAG({
						url: option.to_favorites_url,
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
			},
			
			init_add_to_cart : function()
			{
				var $this = $(this);
				$this.find(option.to_cart_button_id).on('click', function()
				{
					methods.hide_message_block(100);
					
					url = option.to_cart_url;
					
					data = {};
					data['id'] = option.product_id;
					data['price_id'] = $this.find('input[name="price"]:checked').val();
					data['attributes'] = {};
					
					$this.find(option.attributes_block_id).find('select').each(function()
					{
						data['attributes'][$(this).attr('rel')] = $(this).val();
					});
					data['qty'] = $this.find('input[name="qty"]').val();
					data['qty'] = Number(data['qty']);
					if(!data['qty'])
					{
						$this.find('input[name="qty"]').val(1);
						return false;
					}
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
								methods.show_success_message_block(d.site_messages);
								methods.hide_message_block(6000);
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
								
								methods.show_error_message_block(d.site_messages);
								methods.hide_message_block(10000);
							}
						}
					});
					return false;
				});
			},
			
			show_success_message_block : function(success)
			{
				var $ms_block = $(option.message_overlay_content_id);
				$ms_block.html(success);
				$ms_block.find(option.message_block_id).find('#success').show();
				$ms_block.find(option.message_block_id).show();
				message_block_overlay.load();
			},
			
			show_error_message_block : function(error)
			{
				var $ms_block = $(option.message_overlay_content_id);
				$ms_block.html(error);
				$ms_block.find(option.message_block_id).find('#error').show();
				$ms_block.find(option.message_block_id).show();
				message_block_overlay.load();
			},
			
			hide_message_block : function(time)
			{
				if(option.add_pr_timeout_id)
				{
					clearTimeout(option.add_pr_timeout_id);
					var $ms_block = $(option.message_overlay_content_id);
					option.add_pr_timeout_id = setTimeout(function($ms_block)
					{
						$ms_block.html('');
						message_block_overlay.close();
					}, time, $ms_block);
				}
				else
				{
					var $ms_block = $(option.message_overlay_content_id);
					option.add_pr_timeout_id = setTimeout(function($ms_block)
					{
						$ms_block.html('');
						message_block_overlay.close();
					}, time, $ms_block);
				}
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