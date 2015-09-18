(function($) {
	$.fn.gbc_orders_add = function(option)
	{
		var option = $.extend(
		{
			url : '',
			but_id : 'orders_add_product',
			grid_block_id : 'orders_products_grid_overlay',
			change_currency_ulr : '/sales/orders/change_currency'
		},option);
		
		var OBJ = this;
		var orders_products_grid = $('#'+option.grid_block_id).overlay({api: true, top : '0%', oneInstance : false});
		
		$(OBJ).find('#orders_currency').live('change', function()
		{
			var id = $(this).val();
			$.ajaxAG(
			{
				url: option.change_currency_ulr,
				type: "POST",
				data: {id_m_c_currency : id},
				success: function(d)
				{
					$('#cart').html(d);
				}
			});
		});
		
		$(OBJ).find('.JQ_products_view').live('click', function()
		{
			var orders_products_view = $('#orders_products_view_overlay').overlay({api: true, top: '0', oneInstance : false});
			jQuery.ajaxAG(
				{
					url: $(this).attr('href'),
					type: "GET",
					data: {},
					success: function(d)
					{
						$('#orders_products_view_overlay #content').html(d);
						orders_products_view.load();
					}
				}
			);
			return false;
		});
		
		$(OBJ).find('#'+option.but_id).live('click', function()
		{
			url = $(this).attr('href');
			$.ajaxAG(
			{
				url: url,
				type: "GET",
				data: {},
				success: function(d)
				{
					$(OBJ).find('#'+option.grid_block_id+' #content').html(d);
					orders_products_grid.load();
					//$('#'+option.grid_block_id).create_grid({url : url, init_fixed_buttons : false});
				}
			});
			return false;
		});
		
		$(OBJ).find('#customers').live('change', function()
		{
			url = $(this).attr('rel');
			id = $(this).val();
			if(id != '0')
			{
				$.ajaxAG(
				{
					url: url,
					type: "POST",
					data: {id : id},
					dataType: "json",
					success: function(d)
					{
						for(var i in d)
						{
							for(var j in d[i])
							{
								$(OBJ).find('input[name="order_address['+i+']['+j+']"]').val(d[i][j]);
							}
						}
					}
				});
			}	
			return false;
		});
	}
})(jQuery);