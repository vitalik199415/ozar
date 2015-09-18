(function( $ )
{
$.fn.gbc_select_currency = function(method, options)
{
	var option = $.extend(
	{
		change_currency_url : '/ajax/catalogue/currency/ajax_change_currency',
		select_currency_select : '#select_currency_select'
	},options);
	
	var methods = new Object(
	{
		init : function()
		{
			return this.each(function(i,el)
			{
				$th = $(el);
				methods.init_change_currency.apply($th, Array());
			});
		},
		
		init_change_currency : function()
		{
			var $this = $(this);
			$this.on('change', option.select_currency_select, function()
			{
				var $val = $(this).val();
				jQuery.ajaxAG(
				{
					url: option.change_currency_url,
					type: "POST",
					data: {currency : $val},
					dataType : 'json',
					success: function(data)
					{
						if(data.success == 1)
						{
							window.location.reload();
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