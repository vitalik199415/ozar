(function( $ )
{
$.fn.ag_form = function(method, options)
{
	var option = new Object(
	{
		FIX_TOP : "#fixed_top",
		submit_active : true,
		submit_back_active : true,
		
		submit_error_msg : 'Возникли ошибки при заполнении формы!',
	});
	
	var methods = new Object(
	{
		init : function()
		{
			return this.each(function(i,el)
			{
				$this = $(el);
				$(this).find(".tabs_block ul").tabs(".block .block_padding div.field_block", {history: true});
				
				methods.init_submit_buttons.apply($this, Array());
				methods.init_form_buttons.apply($this, Array());
			});
		},
		init_submit_buttons : function()
		{
			$this = this;
			$(this).find('#submit').bind('click', function()
			{
				if(option.submit_active)
				{
					var form = $($this).find('#form_' + $($this).attr('id'));
					if(form.valid())
					{
						loading_start();
						form.submit();
						setTimeout("loading_stop()", 4000);
					}
					else
					{
						alert(option.submit_error_msg);
					}
				}
				return false;
			});
			
			$(this).find('#submit_back').bind('click', function()
			{
				if(option.submit_back_active)
				{
					var form = $($this).find('#form_' + $($this).attr('id'));
					if(form.valid())
					{
						var LOCATION = new String(window.location);
						var POS = LOCATION.lastIndexOf('#');
						var RET = '';
						if(POS>0)
						{
							RET = '&tab='+LOCATION.substring(POS);
						}
						form.attr('action', form.attr('action')+'?return=1'+RET)
						loading_start();
						form.submit();
						setTimeout("loading_stop()", 4000);
					}
					else
					{
						alert(option.submit_error_msg);
					}
				}
				return false;
			});
		},
		init_form_buttons : function()
		{
			var $this = this;
			var B = $(this).find('#form_buttons');
			if(B.length > 0)
			{
				var offset = B.offset().top;
				var height = B.height();
				$(window).on("scroll", function()
				{
					methods.edit_position_form_buttons.apply(B, Array(offset, height));
				});
			}
		},
		edit_position_form_buttons : function(offset, height)
		{
			if($(window).scrollTop() > offset + height + 10)
			{
				methods.show_top_fixed_buttons(this);
			}
			else
			{
				methods.hide_top_fixed_buttons(this);
			}
		},
		show_top_fixed_buttons : function(B)
		{
			$(B).addClass('fixed_top_button');
		},
		hide_top_fixed_buttons : function(B)
		{
			$(B).removeClass('fixed_top_button');
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