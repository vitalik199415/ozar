(function( $ )
{
$.fn.gbc_categories_tree = function(method, option)
{
	var option = $.extend(
	{
		categories_ajax_url : '/catalogue/categories/get_categories_ajax',
		categories_block_id : '#categories_block',
		categories_plus_class : 'plus',
		categories_minus_class : 'minus',
		plus_minus_parent : 'div'
	},option);
	
	var methods =
	{
		init_categories : function()
		{
			$this = $(this);
			$this.find('.'+option.categories_plus_class).live('click', function()
			{
				var $element = $(this);
				var id = parseInt($element.attr('rel'));
				if(id > 0)
				{
					jQuery.ajaxAG(
						{
							url: option.categories_ajax_url,
							type: "POST",
							data: {cat_id : id},
							success: function(html)
							{
								$element.parent(option.plus_minus_parent).after('<div id="categorie_chield_'+id+'">'+html+'</div>');
								$element.removeClass(option.categories_plus_class).addClass(option.categories_minus_class);
							}
						}
					);
				}
				return false;
			});
			$this.find('.'+option.categories_minus_class).live('click', function()
			{
				var $element = $(this);
				$('#categorie_chield_'+$(this).attr('rel')).remove();
				$element.removeClass(option.categories_minus_class).addClass(option.categories_plus_class);
				
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