(function( $ )
{
    $.gbc_wh_transfers = {
        defaults_options : {

            message_overlay : false,
            message_overlay_top : '35%',
            message_overlay_zindex : '9001',
            message_overlay_id : 'gbc_wh_transfer_messages_overlay',
            message_overlay_content_id : 'gbc_wh_transfer_messages_overlay_content',


            overlay : false,
            overlay_top : '2%',
            overlay_zindex : '9001',
            overlay_id : 'gbc_wh_transfer_overlay_block',
            overlay_content_id : 'gbc_wh_transfer_overlay_content',

            pr_message_block_id : 'pr_message_block',
            pr_success_block_class : 'success_message',
            pr_error_block_class : 'error_message',

            tr_pr_grid_id : 'wh_transfer_products_grid',
            tr_add_product_id : 'add_product_to_transfer',
            tr_unset_products_id : 'unset_products_temp_data',

            view_add_product_class : 'transfer_view_add_product',

            v_pr_to_cart_id : 'to_cart',
            v_pr_back_to_wh_pr_class : 'back_to_wh_pr',

            delete_product_qty_class : 'delete_product_qty',
            edit_product_qty_class : 'edit_product_qty',
            edit_product_qty_submit_class : 'edit_product_qty_submit',
            edit_product_qty_cancel_class : 'cancel_edit_product_qty',

            add_pr_timeout_id : false,
            edit_pr_timeout_id : false,

            wh_id_from : false,
            wh_id_to : false
        }
    };

    $.widget( 'gbc.gbc_wh_transfers' , {
        options: $.gbc_wh_transfers.defaults_options,

        _create: function() {
            this.init_overlay_object();
            this.init_message_overlay_object();
            this.init_add_product();
            this.init_edit_delete_products_actions();
        },

        init_overlay_object: function() {
            this.element.append('<div class="JQ_tools_overlay" id="'+this.options.overlay_id+'"><div class="JQ_tools_overlay_content" id="'+this.options.overlay_content_id+'"></div></div>');
            var overlay_el = this.element.find('#'+this.options.overlay_id);
            this.options.overlay = $(overlay_el).overlay({
                api: true,
                oneInstance : false,
                top : this.options.overlay_top,
                zIndex : this.options.overlay_zindex,
                left: 'left',
                mask: {
                    color: '#000000',
                    loadSpeed: 200,
                    opacity: 0.8
                }
            });
            this.init_overlay_view_product_event(overlay_el);
        },

        init_message_overlay_object: function() {
            this.element.append('<div class="JQ_tools_overlay JQ_tools_overlay_20 JQ_tools_site_messages" id="'+this.options.message_overlay_id+'"><div class="JQ_tools_overlay_20_content site_messages_overlay_content" id="'+this.options.message_overlay_content_id+'"></div></div>');
            var overlay_el = this.element.find('#'+this.options.message_overlay_id);
            this.options.message_overlay = $(overlay_el).overlay({
                api: true,
                oneInstance : false,
                top : this.options.message_overlay_top,
                zIndex : this.options.message_overlay_zindex,
                left: 'left',
                mask: {
                    color: '#000000',
                    loadSpeed: 200,
                    opacity: 0.8
                }
            });
        },

        init_overlay_view_product_event: function(overlay_el) {
            var event_func = {};
            event_func['click.'+this.options.view_add_product_class] = function(event) {
                var el = $(event.currentTarget);
                this.view_product(el);
                return false;
            };
            this._on(overlay_el , event_func);
        },

        init_add_product: function() {
            var event_func = {};
            event_func['click#'+this.options.tr_add_product_id] = function(event) {
                var el = $(event.currentTarget);
                this.add_product_show_products_grid(el);
                return false;
            };
            this._on(this.element , event_func);
        },

        init_edit_delete_products_actions: function() {
            var event_func = {};

            event_func['click#'+this.options.tr_unset_products_id] = function(event) {
                var el = $(event.currentTarget);
                this.unset_products_temp_data(el);
                return false;
            };

            event_func['click.'+this.options.edit_product_qty_class] = function(event) {
                var el = $(event.currentTarget);
                this.view_open_edit_product_qty(el);
                return false;
            };

            event_func['click.'+this.options.delete_product_qty_class] = function(event) {
                var el = $(event.currentTarget);
                this.delete_product_from_cart(el);
                return false;
            };

            event_func['click.'+this.options.edit_product_qty_submit_class] = function(event) {
                var el = $(event.currentTarget);
                this.edit_product_qty(el);
                return false;
            };

            event_func['click.'+this.options.edit_product_qty_cancel_class] = function(event) {
                var el = $(event.currentTarget);
                el.parent('div').remove();
                return false;
            };

            this._on(this.element.find('#'+this.options.tr_pr_grid_id) , event_func);
        },

        add_product_show_products_grid: function(el) {
            var $this = this;
            var overlay_el = this.element.find('#'+this.options.overlay_id);
            jQuery.ajaxAG({
                url: el.attr('href'),
                type: "GET",
                data: {},
                dataType : 'html',
                success: function(html_data) {
                    overlay_el.find('#'+$this.options.overlay_content_id).html(html_data);
                    $this.options.overlay.load();
                }
            });
            return false;
        },

        view_product: function(el) {
            var $this = this;
            var overlay_el = this.element.find('#'+this.options.overlay_id);
            jQuery.ajaxAG({
                url: el.attr('href'),
                type: "GET",
                data: {},
                dataType : 'json',
                success: function(data)
                {
                    if(data.success == 1) {
                        overlay_el.find('#'+$this.options.overlay_content_id).html(data.html);
                    }
                }
            });
            return false;
        },

        init_product: function(pr_block_id) {
            var $this = this;
            var overlay_el_pr = this.element.find('#'+this.options.overlay_id).find('#'+pr_block_id);

            var back_to_wh_pr = overlay_el_pr.find('.'+this.options.v_pr_back_to_wh_pr_class);
            var to_cart = overlay_el_pr.find('#'+this.options.v_pr_to_cart_id);

            this._on(back_to_wh_pr, {
                click : function(event) {
                    var el = $(event.currentTarget);
                    this.add_product_show_products_grid(el);
                    return false;
                }
            });
            this._on(to_cart, {
                click : function(event) {
                    var el = $(event.currentTarget);
                    var data = {
                        product_id : overlay_el_pr.find('input[name="product_id"]').val(),
                        qty : overlay_el_pr.find('input[name="qty"]').val()
                    };
                    this.add_product_to_transfer(pr_block_id, el, data);
                    return false;
                }
            });
        },

        view_open_edit_product_qty : function(el)
        {
            var $this = this;

            jQuery.ajaxAG( {
                url: el.attr('href'),
                type: "GET",
                data: {},
                dataType : 'json',
                success: function(data)
                {
                    if(data.success == 1)
                    {
                        var index = $this.element.find('.'+$this.options.edit_product_qty_class).index(el);
                        $this.element.find('.pr_qty').eq(index).prepend(data.html);
                        $this.element.find('.pr_qty').eq(index).find('input').focus();
                    }
                }
            });
        },

        edit_product_qty : function(el)
        {
            var $this = this;
            var qty = el.parent('div').find('input[name="edit_pr_qty"]').val()
            this.hide_message_block(100);
            jQuery.ajaxAG( {
                url: el.attr('href'),
                type: "POST",
                data: {'edit_pr_qty' : qty, 'ajax' : 1},
                dataType : 'json',
                success: function(data)
                {
                    if(data.success == 1)
                    {
                        $this.show_success_message_block(data.message);
                        $this.element.find('#'+$this.options.tr_pr_grid_id).html(data.products);
                        $this.hide_message_block(6000);
                    }
                    else
                    {
                        $this.show_error_message_block(data.message);
                        $this.hide_message_block(12000);
                    }
                }
            });
        },

        delete_product_from_cart : function(el)
        {
            var $this = this;
            this.hide_message_block(100);
            jQuery.ajaxAG( {
                url: el.attr('href'),
                type: "GET",
                data: {},
                dataType : 'json',
                success: function(data)
                {
                    if(data.success == 1)
                    {
                        $this.show_success_message_block(data.message);
                        $this.element.find('#'+$this.options.tr_pr_grid_id).html(data.products);
                        $this.hide_message_block(6000);
                    }
                    else
                    {
                        $this.show_error_message_block(data.message);
                        $this.hide_message_block(6000);
                    }
                }
            });
        },

        unset_products_temp_data : function(el)
        {
            var $this = this;
            jQuery.ajaxAG( {
                url: el.attr('href'),
                type: "GET",
                data: {},
                dataType : 'json',
                success: function(data)
                {
                    if(data.success == 1)
                    {
                        $this.element.find('#'+$this.options.tr_pr_grid_id).html(data.products);
                    }
                }
            });
        },

        add_product_to_transfer: function(pr_block_id, el, data) {
            var $this = this;
            var overlay_el_pr = this.element.find('#'+this.options.overlay_id).find('#'+pr_block_id);
            jQuery.ajaxAG({
                url: el.attr('href'),
                type: "POST",
                data: data,
                dataType : 'json',
                success: function(data)
                {
                    if(data.success == 1)
                    {
                        $this.element.find('#'+$this.options.tr_pr_grid_id).html(data.products);

                        $this.add_product_show_success(overlay_el_pr, data.message);
                        $this.add_product_hide_message_block(overlay_el_pr, 6000);
                    }
                    else
                    {
                        $this.add_product_show_errors(overlay_el_pr, data.message);
                        $this.add_product_hide_message_block(overlay_el_pr, 12000);
                    }
                }
            });
            return false;
        },

        add_product_show_errors : function(pr_block, errors) {
            var $pr_block_ms = $(pr_block).find('#'+this.options.pr_message_block_id);
            $pr_block_ms.find('.'+this.options.pr_error_block_class).find('div').html(errors);
            $pr_block_ms.find('.'+this.options.pr_error_block_class).show();
            $pr_block_ms.show();
        },

        add_product_show_success : function(pr_block, success) {
            var $pr_block_ms = $(pr_block).find('#'+this.options.pr_message_block_id);
            $pr_block_ms.find('.'+this.options.pr_success_block_class).find('div').html(success);
            $pr_block_ms.find('.'+this.options.pr_success_block_class).show();
            $pr_block_ms.show();
        },

        add_product_hide_message_block : function(pr_block, time) {
            if(this.options.add_pr_timeout_id)
            {
                clearTimeout(this.options.add_pr_timeout_id);
                this.options.add_pr_timeout_id = setTimeout(function(pr_block, time, $this)
                {
                    var $pr_block_ms = $(pr_block).find('#'+$this.options.pr_message_block_id);
                    $pr_block_ms.find('.'+$this.options.pr_error_block_class).find('div').html('');
                    $pr_block_ms.find('.'+$this.options.pr_success_block_class).find('div').html('');
                    $pr_block_ms.find('.'+$this.options.pr_error_block_class).hide();
                    $pr_block_ms.find('.'+$this.options.pr_success_block_class).hide();
                    $pr_block_ms.hide();
                }, time, pr_block, time, this);
            }
            else
            {
                this.options.add_pr_timeout_id = setTimeout(function($pr_block, time, $this)
                {
                    var $pr_block_ms = $(pr_block).find('#'+$this.options.pr_message_block_id);
                    $pr_block_ms.find('.'+$this.options.pr_error_block_class).find('div').html('');
                    $pr_block_ms.find('.'+$this.options.pr_success_block_class).find('div').html('');
                    $pr_block_ms.find('.'+$this.options.pr_error_block_class).hide();
                    $pr_block_ms.find('.'+$this.options.pr_success_block_class).hide();
                    $pr_block_ms.hide();
                }, time, pr_block, time, this);
            }
        },

        show_success_message_block : function(success)
        {
            var $ms_block = $('#'+this.options.message_overlay_content_id);
            $ms_block.html(success);
            $ms_block.find('#success').show();
            $ms_block.find('#'+this.options.message_block_id).show();
            this.options.message_overlay.load();
        },

        show_error_message_block : function(error)
        {
            var $ms_block = $('#'+this.options.message_overlay_content_id);
            $ms_block.html(error);
            $ms_block.find('#error').show();
            $ms_block.find('#'+this.options.message_block_id).show();
            this.options.message_overlay.load();
        },

        hide_message_block : function(time)
        {
            if(this.options.edit_pr_timeout_id)
            {
                clearTimeout(this.options.edit_pr_timeout_id);
                this.options.edit_pr_timeout_id = setTimeout(function($this)
                {
                    $this.element.find('#'+$this.options.message_overlay_content_id).html('');
                    $this.options.message_overlay.close();
                }, time, this);
            }
            else
            {
                this.options.edit_pr_timeout_id = setTimeout(function($this)
                {
                    $this.element.find('#'+$this.options.message_overlay_content_id).html('');
                    $this.options.message_overlay.close();
                }, time, this);
            }
        }
    });


/*
        = function(method, options)
{
	var overlay = $(option.overlay_id).overlay(
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
		init : function(options)
		{
            option = $.extend(option, options);

            return this.each(function(i,el)
			{
				$th = $(el);
                console.log($th);
				methods.init_add_pr.apply($th, Array());
				//methods.init_delete_pr.apply($th, Array());
				//methods.init_edit_pr.apply($th, Array());
				//methods.init_view_pr_to_transfer_bt.apply($th, Array());
			});
		},

        test : function()
        {
            console.log(option);
        },
		
		init_add_pr : function()
		{
			var $this = $(this);
            $this.on('click', option.tr_add_product, function()
			{
				jQuery.ajaxAG(
				{
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					dataType : 'html',
					success: function(data)
					{
                        $(option.overlay_id).find(option.overlay_content).html(data);
                        overlay.load();
					}
				});
				return false;
			});
		},
		
		init_delete_pr : function()
		{
			$th = this;
			$(this).on('click', option.delete_pr_from_transfer, function()
			{
				jQuery.ajaxAG(
				{
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
							$($th).find(option.wh_create_transfer_products_grid).html(data.products);
						}
					}
				});
				return false;
			});
		},
		
		init_edit_pr : function()
		{
			$th = this;
			$(this).on('click', option.view_edit_pr_transfer_qty, function()
			{
				jQuery.ajaxAG(
				{
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
							//$($th).find(option.wh_create_sale_products_grid).html(data.products);
							$(option.overlay_id).find(option.overlay_content).html(data.html);
							overlay.load();
						}
					}
				});
				return false;
			});
		},
		
		init_view_pr_to_transfer_bt : function()
		{
			$(document).on('click', option.view_pr_to_transfer, function()
			{
				methods.view_pr_to_transfer($(this).attr('href'));
				return false;
			});
		},
		
		view_pr_to_transfer : function($href)
		{
			jQuery.ajaxAG(
			{
				url: $href,
				type: "GET",
				data: {},
				dataType : 'json',
				success: function(data)
				{
					if(data.success == 1)
					{
						$(option.overlay_id).find(option.overlay_content).html(data.html);
						//overlay.load();
					}
				}
			});
			return false;
		},
		
		init_transfer_add_pr : function()
		{
			var $th = this;
			var $product_block = $(option.view_create_transfer_pr_block);
			
			$product_block.find(option.back_to_wh_pr).bind('click', function()
			{
				var $href = $(this).attr('href');
				jQuery.ajaxAG(
				{
					url: $href,
					type: "GET",
					data: {},
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
							$(option.overlay_id).find(option.overlay_content).html(data.html);
						}
					}
				});
				return false;
			});
			$product_block.find('#to_transfer').bind('click', function()
			{
				var url = $(this).attr('href');
				
				data = {};
				data['product_id'] = $product_block.find('input[name="product_id"]').val();
				data['qty'] = $product_block.find('input[name="qty"]').val();
				//data['price'] = $product_block.find('input[name="price"]').val();
				
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
							//to_sale_status
							$product_block.find(option.to_transfer_status).html('<span style="color:#00CC00;">'+data.massage+'</span>');
							setTimeout(function($product_block, $id)
							{
								$($product_block).find($id).html('');
							}, 2000, $product_block, "'"+option.to_transfer_status+"'");
							$($th).find(option.wh_create_transfer_products_grid).html(data.products);
						}
						else
						{
							$product_block.find(option.to_transfer_status).html('<span style="color:#EE0000;">'+data.massage+'</span>');
							setTimeout(function($product_block, $id)
							{
								$($product_block).find($id).html('');
							}, 2000, $product_block, "'"+option.to_sale_status+"'");
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
	*/
})(jQuery);	