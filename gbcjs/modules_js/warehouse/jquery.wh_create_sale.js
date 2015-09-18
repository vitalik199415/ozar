(function( $ )
{
$.fn.wh_create_sale = function(method, options)
{
	var option = $.extend(
	{
		order_id : false,
		
		overlay_top : '2%',
		overlay_id : '#overlay_block',
		overlay_content : '#overlay_content',
		
		wh_create_sale_products_grid : '#wh_create_sale_products_grid',
		sale_wh_add_product : '#sale_wh_add_product',
		view_pr_to_sale : '.view_pr_to_sale',
		view_create_sale_pr_block : '#view_create_sale_pr_block',
		back_to_wh_pr : '.back_to_wh_pr',
		to_sale_status : '#to_sale_status',
		
		delete_pr_from_sale : '.delete_pr_from_sale',
		view_edit_pr_sale_qty : '.view_edit_pr_sale_qty'
	},options);
	
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
		init : function(option)
		{
			return this.each(function(i,el)
			{
				$th = $(el);
				//$(this).find(".tabs_block ul").tabs(".block .block_padding div.field_block", {history: true});
				methods.init_add_pr.apply($th, Array());
				methods.init_delete_pr.apply($th, Array());
				methods.init_edit_pr.apply($th, Array());
				methods.init_view_pr_to_sale_bt.apply($th, Array());
			});
		},
		
		init_add_pr : function()
		{
			$(this).on('click', option.sale_wh_add_product, function()
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
							$(option.overlay_id).find(option.overlay_content).html(data.html);
							overlay.load();
						}
					}
				});
				return false;
			});
		},
		
		init_delete_pr : function()
		{
			$th = this;
			$(this).on('click', option.delete_pr_from_sale, function()
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
							$($th).find(option.wh_create_sale_products_grid).html(data.products);
						}
					}
				});
				return false;
			});
		},
		
		init_edit_pr : function()
		{
			$th = this;
			$(this).on('click', option.view_edit_pr_sale_qty, function()
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
		
		init_view_pr_to_sale_bt : function()
		{
			$(document).on('click', option.view_pr_to_sale, function()
			{
				methods.view_pr_to_sale($(this).attr('href'));
				return false;
			});
		},
		
		view_pr_to_sale : function($href)
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
		
		init_sale_add_pr : function()
		{
			var $th = this;
			var $product_block = $(option.view_create_sale_pr_block);
			
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
			$product_block.find('#to_sale').bind('click', function()
			{
				var url = $(this).attr('href');
				
				data = {};
				data['product_id'] = $product_block.find('input[name="product_id"]').val();
				data['qty'] = $product_block.find('input[name="qty"]').val();
				data['price'] = $product_block.find('input[name="price"]').val();
				
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
							$product_block.find(option.to_sale_status).html('<span style="color:#00CC00;">'+data.massage+'</span>');
							setTimeout(function($product_block, $id)
							{
								$($product_block).find($id).html('');
							}, 2000, $product_block, "'"+option.to_sale_status+"'");
							$($th).find(option.wh_create_sale_products_grid).html(data.products);
						}
						else
						{
							$product_block.find(option.to_sale_status).html('<span style="color:#EE0000;">'+data.massage+'</span>');
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
	return $this;
}	
})(jQuery);	