(function( $ )
{
	$.fn.gbc_products_albums = function(method, option)
	{
		var option = new Object(
		{
			albums_block : '#albums_block',
			
			albums_img_class : '.images_block',
			albums_img_id : '#album_img_',
			albums_active_class : 'active',
			
			product_id : false
		});
		
		var methods = new Object(
		{
			init : function(options)
			{
				option = $.extend(option, options);
				if(option.product_id)
				{
					return this.each(function(i,el)
					{
						var $this = $(el);
						var data = $this.data('products_albums_detail'+option.product_id);
						if(!data)
						{
							$(this).data('products_albums_detail'+option.product_id, true);
							methods.init_albums_block.apply($this, Array());
						}
					});
				}
				return false;
			},
			
			init_albums_block : function()
			{
				var $this = $(this);
				$this.find(option.albums_block).find('a').on('click', function()
				{
					var $id = $(this).attr('rel');
					methods.change_album.apply($this, Array($(this)));
					return false;
				});
				$first_album = $this.find(option.albums_block).find('a:first');
				$first_album.trigger('click');
			},
			
			change_album : function($album)
			{
				var $this = $(this);
				$id = $album.attr('rel');
				$this.find(option.albums_img_class).hide();
				$this.find(option.albums_img_id+$id).show();
				$this.find(option.albums_block+' a').removeClass(option.albums_active_class);
				$album.addClass(option.albums_active_class);
			}
		});
		
		//return methods.init.apply( this );
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