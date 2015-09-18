(function($, window, undefined)
{
$.fn.gbc_dropdown_menu = function(option, method)
{
	var option = $.extend(
	{
		hide_delay : 400,
	},option);
	
	var val = new Object(
	{
		open_elements : null
	});
	
	var methods = new Object(
	{
		init : function()
		{
			return this.each(function(i,el)
			{
				var $this = el;
				$($this).find('li').bind('click', function(event)
				{
					methods.on_show.apply($this, Array(this));
					event.stopPropagation();
				});
				$(document).bind('click', function()
				{
					if(val.open_elements)
					{
						$($this).find('ul').hide();
						val.open_elements = null;
					}	
				});
			});
		},
		on_show : function($li)
		{
			methods.on_show_close.apply(this, Array($li));
			val.open_elements = null;
			if($($li).find('ul:first').css('display') == 'block')
			{
				val.open_elements = $($li).find('ul:first').hide();
			}
			else
			{
				val.open_elements = $($li).find('ul:first').show();
			}
		},
		on_show_close : function($li)
		{
			$(this).find('ul').not($($li).parents('ul')).not($($li).children('ul')).hide();
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
})(jQuery, window);