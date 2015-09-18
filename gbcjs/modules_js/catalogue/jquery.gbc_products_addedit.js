(function( $ )
{
$.fn.gbc_products_addedit = function(method, options)
{
	var option = new Object(
	{
		price_attributes : false,
		
		overlay_top : '2%',
		overlay_id : '#overlay_block',
		overlay_content : '#overlay_content'
	});
	
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
			option = $.extend(option,options);
			return this.each(function(i,el)
			{
				$this = $(el);
				methods.init_show_price_attributes.apply($this, Array());
				methods.init_attributes_block.apply($this, Array());
				methods.init_types_block.apply($this, Array());
				methods.init_related_block.apply($this, Array());
				methods.init_similar_block.apply($this, Array());
			});
		},
		
		init_show_price_attributes : function()
		{
			var $prices_attr_selects = $(this).find('#show_prices_attributes');
			$prices_attr_selects.each(function(i,el)
			{
				if($(el).val() == 2)
				{
					$(el).parents('fieldset').find('#products_prices_attributes_block').css('display', 'block');
				}
			});
			
			$(this).on('change', '#show_prices_attributes', function()
			{
				if($(this).val() == 2)
				{
					$(this).parents('fieldset').find('#products_prices_attributes_block').css('display', 'block');
				}
				else
				{
					$(this).parents('fieldset').find('#products_prices_attributes_block').css('display', 'none');
				}
			});
		},
		/*build_price_attributes : function()
		{
			if(option.price_attributes)
			{
				$.each(option.price_attributes, function(key, value)
				{
					option.price_attributes[key] = {};
					temp_id = value.split(',');
					$.each(temp_id, function(tkey, tvalue)
					{
						option.price_attributes[key][tvalue] = tvalue;
					});
				});
			}
		},
		init_show_attributes_button : function()
		{
			$(this).on('change', '#show_attributes', function()
			{
				methods.show_select_attributes_button(this);
			});
			$(this).find('#show_attributes').each(function()
			{
				methods.show_select_attributes_button(this);
			});
		},*/
		/*show_select_attributes_button : function($this)
		{
			if($($this).val() == 2)
			{
				$($this).parents('fieldset').find('#show_attributes_button').css('display', 'inline-block');
			}
			else
			{
				$($this).parents('fieldset').find('#show_attributes_button').css('display', 'none');
			}
		},
		init_show_attributes : function()
		{
			var $this = this;
			$(this).on('click', '#show_attributes_button', function()
			{
				fielset = $(this).parents('fieldset');
				id_name = $(fielset).find('select[id=show_attributes]').attr('name');
				price_block_id = id_name.substring(id_name.indexOf('[')+1, id_name.indexOf(']'));
				
				attr_div = $($this).find("input[class='attributes']:checked").parents('.block_w_field_main');
				
				$(option.overlay_id).find(option.overlay_content).html("<div class='form_block'><div class='form_block_padding'>");
				
				$(attr_div.clone()).each(function()
				{
					$(option.overlay_id).find(option.overlay_content).find('.form_block_padding').prepend(this);
				});
				
				$(option.overlay_id).find("input[class='attributes']").each(function()
				{
					$(this).attr('class', 'price_attributes_checkbox');
					$(this).attr('name', 'products_price['+price_block_id+'][id_attributes][]');
					$(this).attr('rel', price_block_id);
					$(this).prop('checked', false);
					chk = this;
					
					if(option.price_attributes[price_block_id])
					{
						if(option.price_attributes[price_block_id][$(chk).val()])
						{
							$(chk).prop('checked', true);
						}
					}
					else
					{
						$(chk).prop('checked', false);
					}
				});
				
				$(option.overlay_id).find(option.overlay_content).append("</div></div>");
				
				$(overlay).load();
				return false;
			});
		},
		init_price_attributes_checkbox : function()
		{
			var $this = this;
			$(option.overlay_id).bind('change', '.price_attributes_checkbox', function()
			{
				var price_block_id = $(this).attr('rel');
				if($(this).prop('checked'))
				{
					$($this).find('input[name='+price_block_id+'][id_attributes]')
					if(!option.price_attributes[price_block_id])
					{
						option.price_attributes[price_block_id] = {};
					}
					option.price_attributes[price_block_id][$(this).val()] = $(this).val();
				}
				else
				{
					delete option.price_attributes[price_block_id][$(this).val()];
				}
				return false;
			});
		},*/
		init_attributes_block : function()
		{
			$this = this;
			$(this).find('.attributes').bind('change',function()
			{
				if($(this).prop('checked'))
				{
					$($this).find('#attributes_options_'+$(this).val()).css('display','block');
					methods.update_prices_attributes_block($this, $(this).val(), true);
				}
				else
				{
					$($this).find('#attributes_options_'+$(this).val()).css('display','none');
					methods.update_prices_attributes_block($this, $(this).val(), false);
				}
			});
		},
		
		update_prices_attributes_block : function($this, $val, $show)
		{
			var $checkbox = $($this).find('#products_prices_attributes_checkbox_'+$val);
			if($show)
			{
				$checkbox.css('display','block');
				$checkbox.prop('checked', true);
				$($this).find('.products_prices_attributes_checkbox_block_'+$val).css('display','block');
			}
			else
			{
				$checkbox.css('display','none');
				$checkbox.prop('checked', false);
				$($this).find('.products_prices_attributes_checkbox_block_'+$val).css('display','none');
			}
		},
		
		init_types_block : function()
		{
			var $this = this;
			$(this).find('.types').bind('change',function()
			{
				if($(this).prop('checked'))
				{
					$($this).find('#properties_'+$(this).val()).css('display','block');
				}
				else
				{
					$($this).find('#properties_'+$(this).val()).css('display','none');
				}
			});
		},
		
		init_related_block : function()
		{
			methods.delete_related_pr.apply(this, Array());
		},
		
		delete_related_pr : function()
		{
			var $this = $(this);
			$this.find('#related_products_block').on('click', '.delete_related_pr', function()
			{
				var href = $(this).attr('href');
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
							$this.find('#related_products_block').html(data.html);
						}
					}
				});
				return false;
			});
		},
		
		init_similar_block : function()
		{
			methods.delete_similar_pr.apply(this, Array());
		},
		
		delete_similar_pr : function()
		{
			var $this = $(this);
			$this.find('#similar_products_block').on('click', '.delete_similar_pr', function()
			{
				var href = $(this).attr('href');
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
							$this.find('#similar_products_block').html(data.html);
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