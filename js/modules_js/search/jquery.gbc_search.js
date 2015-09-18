(function( $ )
{
$.fn.gbc_search = function(method, option)
{
	var option = $.extend(
	{
		search_id : '#search_block_form',
		default_search_text : 'Product SKU...',
		search_text : ''
	},option);
	
	var methods = 
	{
		init_search_block : function()
		{
			var $this = $(this);
			var $input = $this.find('input[name="search_string"]');
			
			if($input.val() == '')
			{
				$input.val(option.default_search_text);
			}	
			
			$input.bind('focus', function()
			{
				methods.focus_in($(this));	
			});
			$input.bind('focusout', function()
			{
				methods.focus_out($(this));	
			});
			
			$this.find('#search_button').bind('click', function()
			{
				methods.search_submit($this);
				return false;
			});
		},
		focus_in : function($this)
		{
			$this.removeClass('focus_out');
			T = jQuery.trim($this.val());
			if(T == option.default_search_text)
			{
				$this.val('');
			}
			if(T == '')
			{
				methods.focus_out($this);
			}
		},
		focus_out : function($this)
		{
			if(jQuery.trim($this.val()) == '' || jQuery.trim($this.val()).length < 3)
			{
				$this.addClass('focus_out');
				$this.val(option.default_search_text);
			}	
		},
		search_submit : function($this)
		{
			T = jQuery.trim($this.find('input[name="search_string"]').val());
			if(T.length >= 3 && T != option.default_search_text)
			{
				$this.submit();
			}
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