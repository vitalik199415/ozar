(function( $ )
{
$.fn.gbc_types = function(method, settings)
{
	var option = $.extend(
	{
		filters_id : '#filters_block'
	},option);
	
	var methods = 
	{
		init : function(settings)
		{
			option = $.extend(option, settings);
			return this.each(function(i,el)
			{
				var $this = $(el);
				var data = $this.data('gbc_product_filters');
				if(!data)
				{
					$this.data('gbc_product_filters', true);
					
					methods.init_filters.apply($this, Array());
				}
			});				
		},
		init_filters : function()
		{
			var $this = $(this);
			$this.find('#filter_block_activate_button').click(function()
			{
				console.log($this);
				$this.find('#filters_form').submit();				
				return false;
			});
			$this.find('#filter_block_clear_button').click(function()
			{
				$this.find('#filters_clear_form').submit();
				return false;
			});
		}		
	}	
	
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