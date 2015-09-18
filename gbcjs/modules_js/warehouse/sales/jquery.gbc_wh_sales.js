(function( $ )
{
$.fn.gbc_wh_sales = function(method)
{
	var $this = this;

	var option = new Object(
	{
		message_overlay_id : '#site_messages_overlay',
		message_overlay_top : '35%',
		message_overlay_close : '.close',
		message_overlay_content_id : '#site_messages_overlay_content',
		message_block_id : '#site_message_message_block',
		
		sale_id : 0,
        wh_id : 0,
		
		overlay_top : '2%',
		overlay_id : '#overlay_block',
		overlay_content : '#overlay_content',

        select_customer : '#select_customer',
        set_sale_customer : '.set_sale_customer',
        unset_sale_customer : '#unset_sale_customer',

		add_product_to_sale : '#add_product_to_sale',
        unset_products_temp_data : '#unset_products_temp_data',
		edit_product_qty_class : '.edit_product_qty',
		delete_product_qty_class : '.delete_product_qty.',
		edit_product_qty_submit_class : '.edit_product_qty_submit',
		edit_product_qty_cancel_class : '.cancel_edit_product_qty',

        sale_discount : '#sale_discount',

        sale_currency : '#sale_currency',
        sale_currency_rate : '#sale_currency_rate',

        copy_billing_to_shipping : '#copy_billing_to_shipping',
        billing_fieldset : '#customer_address_b_fieldset',
        shipping_fieldset : '#customer_address_s_fieldset',

		show_products_with_photo : '#show_products_with_photo',
		
		view_product_block : '#PRD_block',
		pr_attributes_block_id : '#attributes_block',
		pr_sale_message_block_id : '#pr_sale_message_block',
		pr_sale_success_block_id : '.success_message',
		pr_sale_error_block_id : '.error_message',
		
		add_pr_timeout_id : false,
		edit_pr_timeout_id : false
	});
	
	var sale_overlay = $(option.overlay_id).overlay(
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
		init : function(options)
		{
			option = $.extend(option, options);
			return this.each(function(i,el)
			{
				$th = $(el);

                $($th).on('click', option.select_customer, function()
                {
                    methods.show_customers_grid($(this).attr('href'));
                    return false;
                });
                $(document).on('click', option.set_sale_customer, function()
                {
                    methods.set_sale_customer.apply($th, Array($(this).attr('href')));
                    return false;
                });
                $($th).on('click', option.unset_sale_customer, function()
                {
                    methods.unset_sale_customer($(this).attr('href'));
                    return false;
                });

				$($th).on('click', option.add_product_to_sale, function()
				{
					methods.add_product_show_products($(this).attr('href'));
					return false;
				});
				$($th).on('click', option.edit_product_qty_class, function()
				{
					methods.view_open_edit_product_qty.apply($th, Array($(this).attr('href'), $($th).find(option.edit_product_qty_class).index(this)));
					return false;
				});
				$($th).on('click', option.delete_product_qty_class, function()
				{
					methods.delete_product_from_cart.apply($th, Array($(this).attr('href')));
					return false;
				});
				$($th).on('click', option.unset_products_temp_data, function()
				{
					methods.unset_products_temp_data.apply($th, Array($(this).attr('href')));
					return false;
				});
				$($th).on('click', option.edit_product_qty_submit_class, function()
				{
					methods.view_edit_product_qty.apply($th, Array($(this).attr('href'), $(this).parent('div').find('input[name="edit_pr_qty"]').val(), this));
					return false;
				});
				$($th).on('click', option.edit_product_qty_cancel_class, function()
				{
					$(this).parent('div').remove();
					return false;
				});
				$($th).on('click', option.show_products_with_photo, function()
				{
					methods.show_products_with_photo($(this).attr('href'));
					return false;
				});
				$(document).on('click', '.sale_view_add_product', function()
				{
					methods.add_product_show_product($(this).attr('href'));
					return false;
				});
                $($th).find(option.sale_discount).on('change', function()
                {
                    methods.set_sale_discount($(this).val());
                });
                $($th).find(option.sale_currency).on('change', function()
                {
                    methods.set_sale_currency($(this).val());
                });
                $($th).find(option.sale_currency_rate).on('change', function()
                {
                    methods.set_sale_currency_rate($(this).val());
                });
                $($th).find(option.copy_billing_to_shipping).on('click', function()
                {
                    methods.copy_billing_to_shipping.apply($th, Array());
                    return false;
                });
			});
		},

        show_customers_grid : function(href)
        {
            jQuery.ajaxAG({
                url: href,
                type: "GET",
                data: {},
                dataType : 'html',
                success: function(html)
                {
                    $(option.overlay_id).find(option.overlay_content).html(html);
                    sale_overlay.load();
                }
            });
        },

        set_sale_customer : function(href)
        {
            $th = $(this);
            jQuery.ajaxAG({
                url: href,
                type: "GET",
                data: {},
                dataType : 'json',
                success: function(data)
                {
                    $('#sale_customer').html(data.ct_html);
                    if(typeof(data.ct_addresses) != "undefined")
                    {
                        $.each(data.ct_addresses, function(key, value)
                        {
                            $.each(value, function(key1, value1)
                            {
                                $($th).find('input[name="addresses['+key+']['+key1+']"]').val(value1);
                            });
                        });
                    }
                    sale_overlay.close();
                }
            });
        },

        unset_sale_customer : function(href)
        {
            jQuery.ajaxAG({
                url: href,
                type: "GET",
                data: {},
                dataType : 'html',
                success: function(html)
                {
                    $('#sale_customer').html(html);
                }
            });
        },

		add_product_show_products : function(href)
		{
			jQuery.ajaxAG(
			{
				url: href,
				type: "GET",
				data: {},
				dataType : 'html',
				success: function(html)
				{
                    $(option.overlay_id).find(option.overlay_content).html(html);
                    sale_overlay.load();
				}
			});
		},

        unset_products_temp_data : function(href)
        {
            var $th = this;
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
                            $($th).find('#wh_sale_products_grid').html(data.products);
                            $.each(data.sale_data, function(key, value)
                            {
                                $($th).find('input[name="sale['+key+']"]').val(value);
                            });
                        }
                    }
                });
        },

        view_open_edit_product_qty : function(href, index)
        {
            var $th = this;
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
                            $($th).find('.pr_qty').eq(index).prepend(data.html);
                            $($th).find('.pr_qty').eq(index).find('input').focus();
                        }
                    }
                });
        },

        view_edit_product_qty : function(href, qty, element)
        {
            var $th = this;
            methods.hide_message_block(100);
            jQuery.ajaxAG(
                {
                    url: href,
                    type: "POST",
                    data: {'sale_pr_qty' : qty, 'ajax' : 1},
                    dataType : 'json',
                    success: function(data)
                    {
                        if(data.success == 1)
                        {
                            methods.show_success_message_block(data.message);
                            $($th).find('#wh_sale_products_grid').html(data.products);
                            $.each(data.sale_data, function(key, value)
                            {
                                $($th).find('input[name="sale['+key+']"]').val(value);
                            });
                            methods.hide_message_block(6000);
                        }
                        else
                        {
                            methods.show_error_message_block(data.message);
                            methods.hide_message_block(12000);
                            //$(element).parent('div').remove();
                        }
                    }
                });
        },

        delete_product_from_cart : function(href)
        {
            var $th = this;
            methods.hide_message_block(100);
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
                            methods.show_success_message_block(data.message);
                            $($th).find('#wh_sale_products_grid').html(data.products);
                            $.each(data.sale_data, function(key, value)
                            {
                                $($th).find('input[name="sale['+key+']"]').val(value);
                            });
                            methods.hide_message_block(6000);
                        }
                        else
                        {
                            methods.show_error_message_block(data.message);
                            methods.hide_message_block(6000);
                        }
                    }
                });
        },
		
		show_success_message_block : function(success)
		{
			var $ms_block = $(option.message_overlay_content_id);
			$ms_block.html(success);
			$ms_block.find('#success').show();
			$ms_block.find(option.message_block_id).show();
			message_block_overlay.load();
		},
		
		show_error_message_block : function(error)
		{
			var $ms_block = $(option.message_overlay_content_id);
			$ms_block.html(error);
			$ms_block.find('#error').show();
			$ms_block.find(option.message_block_id).show();
			message_block_overlay.load();
		},
		
		hide_message_block : function(time)
		{
			if(option.edit_pr_timeout_id)
			{
				clearTimeout(option.edit_pr_timeout_id);
				var $ms_block = $(option.message_overlay_content_id);
				option.edit_pr_timeout_id = setTimeout(function($ms_block)
				{
					$ms_block.html('');
					message_block_overlay.close();
				}, time, $ms_block);
			}
			else
			{
				var $ms_block = $(option.message_overlay_content_id);
				option.edit_pr_timeout_id = setTimeout(function($ms_block)
				{
					$ms_block.html('');
					message_block_overlay.close();
				}, time, $ms_block);
			}
		},
		
		add_product_show_product : function(href)
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
						sale_overlay.load();
					}
				}
			});
		},
		
		init_view_product : function(product_id, prices_array, price_attributes, sale_id)
		{
			var $th = this;
			var $product_block = $(option.view_product_block);

            $product_block.find('#back_to_products_top, #back_to_products_bot').on('click', function()
			{
				methods.add_product_show_products($(this).attr('href'));
				return false;
			});
            var price_id = $($product_block).find('input[name="price"]:checked').val();
            if(typeof(prices_array[price_id]) != "undefined") $($product_block).find('input[name="price_value"]').val(prices_array[price_id]);
            $($product_block).find('input[name="price"]').bind('change', function()
            {
                if(typeof(prices_array[$(this).val()]) != "undefined") $($product_block).find('input[name="price_value"]').val(prices_array[$(this).val()]);
            });

			$product_block.find('#to_cart').bind('click', function()
			{
				var url = $(this).attr('href');
				
				data = {};
				data['sale_id'] = sale_id;
				data['product_id'] = product_id;
				data['price_id'] = $product_block.find('input[name="price"]:checked').val();
                data['price'] = $product_block.find('input[name="price_value"]').val();
                data['attributes'] = {};
				
				$product_block.find(option.pr_attributes_block_id).find('select').each(function()
				{
					data['attributes'][$(this).attr('rel')] = $(this).val();
				});
				data['qty'] = $product_block.find('input[name="qty"]').val();
				
				methods.pr_hide_message_block($product_block, 100);
				jQuery.ajaxAG(
				{
					url: url,
					type: "POST",
					data: data,
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
                            $($th).find('#wh_sale_products_grid').html(data.products);

                            methods.pr_show_success($product_block, data.message);
                            methods.pr_hide_message_block($product_block, 6000);
                            $.each(data.sale_data, function(key, value)
                            {
                                $($th).find('input[name="sale['+key+']"]').val(value);
                            });
						}
						else
						{
							/*if(typeof(data.available_qty) != "undefined")
							{
								$this.find('input[name="qty"]').val(d.available_qty);
								$price = option.prices_checked;
								$real_qty = $price.data('prices_rules').real_qty;
								methods.update_real_qty_block.apply($this, Array(d.available_qty, $real_qty));
							}*/
							methods.pr_show_errors($product_block, data.message);
							methods.pr_hide_message_block($product_block, 12000);
						}
					}
				});
				return false;
			});
		},
		
		pr_show_errors : function($pr_block, errors)
		{
			var $pr_block_ms = $($pr_block).find(option.pr_sale_message_block_id)
			$pr_block_ms.find(option.pr_sale_error_block_id).find('div').html(errors);
			$pr_block_ms.find(option.pr_sale_error_block_id).show();
			$pr_block_ms.show();
		},
		
		pr_show_success : function($pr_block, success)
		{
			var $pr_block_ms = $($pr_block).find(option.pr_sale_message_block_id)
			$pr_block_ms.find(option.pr_sale_success_block_id).find('div').html(success);
			$pr_block_ms.find(option.pr_sale_success_block_id).show();
			$pr_block_ms.show();
		},
		
		pr_hide_message_block : function($pr_block, time)
		{
			if(option.add_pr_timeout_id)
			{
				clearTimeout(option.add_pr_timeout_id);
				option.add_pr_timeout_id = setTimeout(function($pr_block, time)
				{
					var $pr_block_ms = $($pr_block).find(option.pr_sale_message_block_id)
					$pr_block_ms.find(option.pr_sale_error_block_id).find('div').html('');
					$pr_block_ms.find(option.pr_sale_success_block_id).find('div').html('');
					$pr_block_ms.find(option.pr_sale_error_block_id).hide();
					$pr_block_ms.find(option.pr_sale_success_block_id).hide();
					$pr_block_ms.hide();
				}, time, $pr_block, time);
			}
			else
			{
				option.add_pr_timeout_id = setTimeout(function($pr_block, time)
				{
					var $pr_block_ms = $($pr_block).find(option.pr_sale_message_block_id)
					$pr_block_ms.find(option.pr_sale_error_block_id).find('div').html('');
					$pr_block_ms.find(option.pr_sale_success_block_id).find('div').html('');
					$pr_block_ms.find(option.pr_sale_error_block_id).hide();
					$pr_block_ms.find(option.pr_sale_success_block_id).hide();
					$pr_block_ms.hide();
				}, time, $pr_block, time);
			}
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
                            $(option.overlay_id).find(option.overlay_content).html(data.html);
                            sale_overlay.load();
                        }
                    }
                });
            return false;
        },

        set_sale_discount : function($val)
        {
            jQuery.ajaxAG(
            {
                url: '/warehouse/warehouses_sales/ajax_set_sale_discount/wh_id/'+option.wh_id+'/sale_id/'+option.sale_id,
                type: "POST",
                data: {'discount' : $val},
                dataType : 'json',
                success: function(data)
                {
                    $.each(data.sale_data, function(key, value)
                    {
                        $($th).find('input[name="sale['+key+']"]').val(value);
                    });
                }
            });
        },

        set_sale_currency : function($val)
        {
            jQuery.ajaxAG(
            {
                url: '/warehouse/warehouses_sales/ajax_set_sale_currency/wh_id/'+option.wh_id+'/sale_id/'+option.sale_id,
                type: "POST",
                data: {'currency_id' : $val},
                dataType : 'json',
                success: function(data)
                {
                    $.each(data.sale_data, function(key, value)
                    {
                        $($th).find('input[name="sale['+key+']"]').val(value);
                    });
                }
            });
        },

        set_sale_currency_rate : function($val)
        {
            jQuery.ajaxAG(
            {
                url: '/warehouse/warehouses_sales/ajax_set_sale_currency_rate/wh_id/'+option.wh_id+'/sale_id/'+option.sale_id,
                type: "POST",
                data: {'currency_rate' : $val},
                dataType : 'json',
                success: function(data)
                {
                    $.each(data.sale_data, function(key, value)
                    {
                        $($th).find('input[name="sale['+key+']"]').val(value);
                    });
                }
            });
        },

        copy_billing_to_shipping : function()
        {
            $this = $(this);
            var b_f = $this.find(option.billing_fieldset).find('input');
            var s_f = $this.find(option.shipping_fieldset).find('input');
            $.each(b_f, function(key, value)
            {
                $(s_f[key]).val($(value).val());
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