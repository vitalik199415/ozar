(function( $ )
{
    var gbc_orders_options = {
        order_overlay_id : '#order_overlay',
		order_overlay_cn : '#order_overlay_content',
		overlay_close : '.close',
		
		cart_id : '#cart_block',
		cart_min_id : '#cart_min_block',
		
		customer_login : '#create_order_customer_login',
		customer_block_login : '#customer_login',
		
		shipping_method_id : '#order_select_shipping_method',
		shipping_block_id : '#order_shipping_block"',
		
		payment_method_id : '#order_select_payment_method',
		payment_block_id : '#order_billing_block',
		payment_method_desc : '#payment_methods_description',
		
		order_message_block_id : '#order_message_block',
		order_message_block_bot_id : '#order_message_block_bot',
		order_success_block_id : '.success_message',
		order_error_block_id : '.error_message',
		
		edit_message_block_id : '#order_cart_edit_message_block',
		edit_success_block_id : '#order_cart_edit_success',
		edit_error_block_id : '#order_cart_edit_error',
		
		submit_button_id : '#order_submit, #order_submit_bot',
		
		order_products_block_id : '#order_products',
		cart_edit : '.cart_edit_edit_item',
		cart_delete : '.cart_edit_delete_item',
		
		url_change_shipping_method : false,
		url_change_payment_method : false,
		
		url_login_form : false,
		url_edit_item : false,
		url_delete_item : false,
		
		url_edit_item : false,
		url_delete_item : false,
		
		success_create_order : false,
		error_submit : false,
		error_create_order : false,
		
		edit_pr_timeout_id : false,
		
		timeoutID : false,
		
		order_required_payment_fields : {
			"order_address[B][name]" : 			{required: true},
			"order_address[B][country]" : 		{required: true},
			"order_address[B][city]" : 			{required: true},
			"order_address[B][address]" : 		{required: true},
			"order_address[B][telephone]" : 	{required: true},
			"order_address[B][address_email]" : {required: true, email: true}
		},
		order_required_shipping_fields : {
			"order_address[S][name]" : 			{required: true},
			"order_address[S][country]" : 		{required: true},
			"order_address[S][city]" : 			{required: true},
			"order_address[S][address]" : 		{required: true},
			"order_address[S][telephone]" : 	{required: true},
			"order_address[S][address_email]" : {required: true, email: true}
		},

		activate_url: false,
		promocode_field_id: '#promocode',
		activate_message_block_id: '.activate_message_block',
		activate_button_id: '#activate',
		success_block_id : '.success_message',
		error_block_id : '.error_message',
		activate_err_mess: false
    };

    $.widget('gbc.gbc_orders' , {
        options: gbc_orders_options,

        _create: function() {
            this.init_order_form();
			this.init_customer_login();
			this.init_payment_method();
			this.init_shipping_method();
			this.init_copy_billing_to_shipping();
			this.init_cart_edit_buttons();
			this.init_promocode();
        },

		init_order_form : function()
		{
			var $this = this;
			if(this.options.timeoutID)
			{
				clearTimeout(this.options.timeoutID);
			}
			var submin_but = this.element.find(this.options.submit_button_id);
			this._on(submin_but, {
				'click' : function(){
					this._trigger('beforeSubmitOrder', 0, $this);
					this.element.validate({
						rules : $.extend({}, this.options.order_required_shipping_fields, this.options.order_required_payment_fields),
						errorPlacement: function(error, element) {
							error.insertAfter(element.parent('div').find('div'));
						}
					});
					
					this.hide_message_block();
					var options = { 
						beforeSubmit: function() { return $this.form_validate() },
						success: function(responseText, statusText, xhr, $form) { return $this.form_create_order_success.apply($this, Array(responseText, statusText, xhr, $form)) },
						dataType:  'json'
					};
					this.element.ajaxSubmit(options);					
				}
			});
		},
		
        init_customer_login : function()
		{
			this._on(this.element.find(this.options.customer_login), {
				'click' : function(e){
					$(this.options.order_overlay_id).find(this.options.overlay_close).trigger('click');
					setTimeout(function(customer_block_login){$(customer_block_login).trigger('click')}, 500, this.options.customer_block_login);
					return false;
				}
			});
		},
		
		init_payment_method : function()
		{
			var pm_select = this.element.find(this.options.payment_method_id);
			this._on(pm_select, {
				'change' : function(e){
					var el = $(e.currentTarget);
					this.change_payment_method(el.val());
				}
			});
		},
		
		change_payment_method : function($val)
		{
			var $this = this;
			if(this.options.url_change_payment_method)
			{
				var payment_method_id = $val;
				var data = new Object();
				this.element.find(this.options.payment_block_id).find('input, select').each(function()
				{
					data[$(this).attr('name')] = $(this).val();
				});
				data['payment_method_id'] = payment_method_id;
				jQuery.ajaxAG(
				{
					url: $this.options.url_change_payment_method,
					type: "POST",
					data: data,
					dataType : 'json',
					success: function(d)
					{
						if(d.status == 1)
						{
							$this.element.find($this.options.payment_method_desc).find('span').html(d.html);
						}	
					}
				});
			}
		},
		
		init_shipping_method : function()
		{
			var sm_select = this.element.find(this.options.shipping_method_id);
			this._on(sm_select, {
				'change' : function(e){
					var el = $(e.currentTarget);
					this.change_shipping_method(el.val());
				}
			});
		},
		
		change_shipping_method : function($val)
		{
			var $this = this;
			if(this.options.url_change_shipping_method)
			{
				var shipping_method_id = $val;
				var data = new Object();
				this.element.find(this.options.shipping_block_id).find('input, select').each(function()
				{
					data[$(this).attr('name')] = $(this).val();
				});
				data['shipping_method_id'] = shipping_method_id;
				jQuery.ajaxAG(
				{
					url: $this.options.url_change_shipping_method,
					type: "POST",
					data: data,
					dataType : 'json',
					success: function(d)
					{
						if(d.status == 1)
						{
							$this.element.find($this.options.shipping_block_id).html(d.html);
							$this.init_shipping_method();
							console.log($this.options.order_required_shipping_fields);
						}	
					}
				});
			}
		},
		
		init_copy_billing_to_shipping : function()
		{
			this.element.find(this.options.shipping_block_id).on('click', '#same_as_billing', function()
			{
				$.each($('#customer_address_b_fieldset').find('input[type=text], select'), function(i)
				{
					fname = $(this).attr('name');
					$('#customer_address_s_fieldset').find('input[name="'+fname.replace('B', 'S')+'"], select[name="'+fname.replace('B', 'S')+'"]').val($(this).val());
				});
			});
		},

		init_promocode : function()
		{
			var $this = this;
			if(this.options.timeoutID)
			{
				clearTimeout(this.options.timeoutID);
			}
			var submit_but = this.element.find(this.options.activate_button_id);
			var err_mess = this.options.activate_err_mess;
			this._on(submit_but, {
				'click' : function(){
					var code = $this.element.find($this.options.promocode_field_id);

					var reg = /\d/;
					var promocode = code.val();
					if(reg.test(promocode) && promocode.length == 16) {
						console.log(promocode);
						jQuery.ajaxAG({
							url: $this.options.activate_url,
							type: "POST",
							data: {code: promocode},
							dataType : 'json',
							success: function(d)
							{
								console.log('success');
								if(d.success == 1) {
									console.log('success');
									$this.show_activate_success(d.site_messages);
								} else
								if(d.success == 0)
								{
									console.log('error');
									$this.show_activate_errors(d.site_messages);
								}
							}
						});
					} else {
						this.show_activate_errors(err_mess);
					}
				}
			});
		},

		show_activate_errors : function(errors)
		{
			var mess_block = this.element.find(this.options.activate_message_block_id);
			var err_block = this.element.find('.activate_item_block').find(this.options.activate_message_block_id).find(this.options.error_block_id);
			err_block.find('div').html(errors);
			err_block.show();
			mess_block.show();
			setTimeout(function() {
				err_block.hide();
				mess_block.hide();
			}, 4000);
		},

		show_activate_success : function(success)
		{
			var mess_block = this.element.find(this.options.activate_message_block_id);
			var success_block = this.element.find('.activate_item_block').find(this.options.activate_message_block_id).find(this.options.success_block_id);
			success_block.find('div').html(success);
			success_block.show();
			mess_block.show();
			setTimeout(function() {
				success_block.hide();
				mess_block.hide();
			}, 4000);
		},
		
		form_validate : function()
		{
			if(this.element.valid())
			{
				loading_start();
				return true;
			}
			else
			{
				this.show_errors(this.options.error_submit);
				return false;
			}	
		},
		
		form_create_order_success : function(responseText, statusText, xhr, $form)
		{
			if(responseText.status == 1)
			{
				this.show_success(responseText.success);
				$(this.options.cart_id).html(responseText.cart_html);
				$(this.options.cart_min_id).html(responseText.cart_min_html);
				
				this.element.find(this.options.submit_button_id).remove();
				this.options.timeoutId = setTimeout(function(block_overlay, overlay_close)
				{
					$(block_overlay).find(overlay_close).trigger('click');
				}, 13000, this.options.order_overlay_id, this.options.overlay_close);
			}
			if(responseText.status == 0)
			{
				this.show_errors(responseText.errors);
			}
			loading_stop();
		},
		
		show_errors : function(errors)
		{
			var $mblock = this.element.find(this.options.order_message_block_id+','+this.options.order_message_block_bot_id);
			$mblock.find(this.options.order_error_block_id).find('div').html(errors);
			$mblock.find(this.options.order_error_block_id).show();
			$mblock.show();
			$(this.options.order_overlay_cn).scrollTo(this.options.order_message_block_id, {duration : 2000});
		},
		
		show_success : function(success)
		{
			var $mblock = this.element.find(this.options.order_message_block_id+','+this.options.order_message_block_bot_id);
			$mblock.find(this.options.order_success_block_id).find('div').html(success);
			$mblock.find(this.options.order_success_block_id).show();
			$mblock.show();
			$(this.options.order_overlay_cn).scrollTo(this.options.order_message_block_id, {duration : 2000});

		},
		
		hide_message_block : function()
		{
			var $mblock = this.element.find(this.options.order_message_block_id+','+this.options.order_message_block_bot_id);
			$mblock.find(this.options.order_error_block_id).find('div').html('');
			$mblock.find(this.options.order_success_block_id).find('div').html('');
			$mblock.find(this.options.order_error_block_id).hide();
			$mblock.find(this.options.order_success_block_id).hide();
			$mblock.hide();
		},
		
		init_cart_edit_buttons : function()
		{
			var PR_block = this.element.find(this.options.order_products_block_id);
			var event_func = {};
            event_func['click'+this.options.cart_edit] = function(event) {
                var el = $(event.currentTarget);
				this.edit_item(el);
                return false;
            };
			event_func['click'+this.options.cart_delete] = function(event) {
                var el = $(event.currentTarget);
				this.delete_item(el);
                return false;
            };
            this._on(PR_block , event_func);
		},
		
		edit_item : function($item)
		{
			var $this = this;
			this.hide_edit_message_block(100);
			var PR_block = this.element.find(this.options.order_products_block_id);
			var data = {};
			var block = PR_block.find($item).parents('tr');
			data['rowid'] = PR_block.find(block).find('input[name="rowid"]').val();
			data['qty'] = PR_block.find(block).find('input[name="qty"]').val();
			if(this.options.url_edit_item !== false)
			{
				jQuery.ajaxAG(
				{
					url: this.options.url_edit_item,
					type: "POST",
					data: data,
					dataType : "json",
					success: function(d)
					{
						if(d.success == 1)
						{
							if(d.cart_edit_html == false)
							{
								PR_block.html(d.cart_edit_html);
								$($this.options.cart_id).html(d.cart_html);
								$($this.options.cart_min_id).html(d.cart_min_html);
							}
							else
							{
								PR_block.html(d.cart_edit_html);
								$($this.options.cart_id).html(d.cart_html);
								$($this.options.cart_min_id).html(d.cart_min_html);
								
								$this.show_edit_success(d.site_messages);
								$this.hide_edit_message_block(d.delay);
							}
						}
						else
						{
							if(typeof(d.available_qty) != "undefined")
							{
								block.find('input[name="qty"]').val(d.available_qty);
							}
							
							$this.show_edit_errors(d.site_messages);
							$this.hide_edit_message_block(d.delay);
						}
					}
				});
			}
		},
		
		delete_item : function($item)
		{
			var $this = this;
			this.hide_edit_message_block(100);
			var PR_block = this.element.find(this.options.order_products_block_id);
			var data = {};
			var block = PR_block.find($item).parents('tr');
			data['rowid'] = PR_block.find(block).find('input[name="rowid"]').val();
			if(this.options.url_delete_item !== false)
			{
				jQuery.ajaxAG(
				{
					url: this.options.url_delete_item,
					type: "POST",
					data: data,
					dataType : "json",
					success: function(d)
					{
						if(d.success == 1)
						{
							if(d.cart_edit_html == false)
							{
								$($this.options.order_overlay_id).find($this.options.overlay_close).trigger('click');
								$($this.options.cart_id).html(d.cart_html);
								$($this.options.cart_min_id).html(d.cart_min_html);
							}
							else
							{
								PR_block.html(d.cart_edit_html);
								$($this.options.cart_id).html(d.cart_html);
								$($this.options.cart_min_id).html(d.cart_min_html);
								
								$this.show_edit_success(d.site_messages);
								$this.hide_edit_message_block(d.delay);
							}
						}
						else
						{
							if(d.cart_edit_html == false)
							{
								$($this.options.order_overlay_id).find($this.options.overlay_close).trigger('click');
								$($this.options.cart_id).html(d.cart_html);
								$($this.options.cart_min_id).html(d.cart_min_html);
							}
							else
							{
								PR_block.html(d.cart_edit_html);
								$($this.options.cart_id).html(d.cart_html);
								$($this.options.cart_min_id).html(d.cart_min_html);
								
								$this.show_edit_errors(d.site_messages);
								$this.hide_edit_message_block(d.delay);
							}
						}
					}
				});
			}
		},
		
		show_edit_errors : function(errors)
		{
			$(this.options.edit_message_block_id).find(this.options.edit_error_block_id).find('div').html(errors);
			$(this.options.edit_message_block_id).find(this.options.edit_error_block_id).show();
			$(this.options.edit_message_block_id).show();
		},
		
		show_edit_success : function(success)
		{
			$(this.options.edit_message_block_id).find(this.options.edit_success_block_id).find('div').html(success);
			$(this.options.edit_message_block_id).find(this.options.edit_success_block_id).show();
			$(this.options.edit_message_block_id).show();
		},
		
		hide_edit_message_block : function(time)
		{
			if(this.options.edit_pr_timeout_id)
			{
				clearTimeout(this.options.edit_pr_timeout_id);
				var $ms_block = $(this.options.edit_message_block_id);
				var $ms_block_s = $(this.options.edit_message_block_id).find(this.options.edit_success_block_id);
				var $ms_block_e = $(this.options.edit_message_block_id).find(this.options.edit_error_block_id);
				this.options.edit_pr_timeout_id = setTimeout(function($ms_block, $ms_block_s, $ms_block_e)
				{
					$ms_block_s.find('div').html('');
					$ms_block_e.find('div').html('');
					$ms_block_s.hide();
					$ms_block_e.hide();
					$ms_block.hide();
					
				}, time, $ms_block, $ms_block_s, $ms_block_e);
			}
			else
			{
				var $ms_block = $(this.options.edit_message_block_id);
				var $ms_block_s = $(this.options.edit_message_block_id).find(this.options.edit_success_block_id);
				var $ms_block_e = $(this.options.edit_message_block_id).find(this.options.edit_error_block_id);
				this.options.edit_pr_timeout_id = setTimeout(function($ms_block, $ms_block_s, $ms_block_e)
				{
					$ms_block_s.find('div').html('');
					$ms_block_e.find('div').html('');
					$ms_block_s.hide();
					$ms_block_e.hide();
					$ms_block.hide();
					
				}, time, $ms_block, $ms_block_s, $ms_block_e);
			}
		}
    });
})(jQuery);