(function( $ )
{	
	$.fn.create_grid = function(options)
	{
		var options = jQuery.extend(
		{
			url : '',
			init_fixed_buttons : 1
		},options);
		
		var check_height = false;
	
		var methods = 
		{
			init : function()
			{
				return this.each(function()
				{
					var $this = $(this);
					data = $this.data('create_grid');
					
					if(!data)
					{
						$(this).data('create_grid',{
							target : $this,
							form : $this.find('form')
						});
						methods.init_fields($this);
						methods.init_buttons($this);
					}
				})
			},
			destroy : function($this)
			{
				return $this.each(function(){
					var $this = $(this),
					data = $this.data('create_grid');
					
					$this.removeData('create_grid');
				})
			},
			init_fields : function($this)
			{
				$('#fixed_top').append($this.find('#top_fixed_buttons').html());
				$this.find('.field_data').live('mouseover', function()
				{
					$(this).addClass('field_data_hover');
				});
				$this.find('.field_data').live('mouseout', function()
				{
					$(this).removeClass('field_data_hover');
				});
			},
			init_buttons : function($this)
			{
				$this.find('.search_button').live('click',
					function()
					{
						if($(this).attr('rel')=='search')
						{
							methods.sendData($this);
							return false;
						}
						else
						{
							$this.find('.grid_table').find('input,select').val('');
							methods.sendData($this);
							return false;
						}
					}
				);
				$this.find('.up , .down').live('click',
					function()
					{
						var $sortInput = $this.find('input[name=sort]');
						$sortInput.val($(this).attr('rel'));
						methods.sendData($this);
						return false;
					}
				);
				$this.find('.pages_block .page').live('click',
					function()
					{
						var $pageInput = $this.find('input[name=page]');
						$pageInput.val($(this).attr('rel'));
						methods.sendData($this);
						return false;
					}
				);
				$this.find('.pages_block select[name=limit]').live('change',
					function()
					{
						methods.sendData($this);
						return false;
					}
				);
				$this.find('#action_checkbox').live('change',
					function()
					{
						if($(this).attr('checked'))
						{
							$(this).parents('tr').addClass('field_data_checked');
						}
						else
						{
							$(this).parents('tr').removeClass('field_data_checked');
						}
					}
				);
				$this.find('.select_all a').live('click',
					function()
					{
						var $checkbox = $this.find('#action_checkbox');
						if($(this).attr('rel')=='check')
						{
							$checkbox.attr("checked","checked");
							$checkbox.parents('tr').addClass('field_data_checked');
						}
						else if($(this).attr('rel')=='uncheck')
						{
							$checkbox.removeAttr("checked");
							$checkbox.parents('tr').removeClass('field_data_checked');
						}
						return false;
					}
				);
				$this.find('#submit').live('click',
					function()
					{
						var $checkbox = $this.find('#action_checkbox');
						$.each($checkbox,
							function()
							{
								if($(this).attr('checked'))
								{
									$(this).attr('id','action_checkbox_checked');
								}
							}
						)
						methods.sendData($this, true);
						return false;
					}
				);
				
				$this.find('.arrow_down').live('click',function()
					{
						var location = $(this).attr('href');
						methods.change_row_position($this, location);
						return false;
					}
				);
				$this.find('.arrow_up').live('click',function()
					{
						var location = $(this).attr('href');
						methods.change_row_position($this, location);
						return false;
					}
				);
				if(options.init_fixed_buttons)
				{
					methods.initFixedButtons($this);
				}
				return false;
			},
			change_row_position : function($this, location)
			{
				var location = location;
				var page = $this.find('input[name=page]').val();
				var sort = $this.find('input[name=sort]').val();
				var limit = $this.find('select[name=limit]').val();
				var href = options.url;	
				var AJAXsend = {};
				AJAXsend['page'] = page;
				AJAXsend['sort'] = sort;
				AJAXsend['limit'] = limit;
				AJAXsend['ajax'] = 1;
				var s_i = $this.find('input,select');
				
				$(s_i).attr('class','');
				$(s_i).each(
					function(i)
					{
						if($.trim($(s_i[i]).val())!='' && $(s_i[i]).attr('type')!='hidden' && $(s_i[i]).attr('name')!='limit' && $(s_i[i]).attr('type')!='checkbox')
						{
							$(s_i[i]).attr('class','search_on');
						}
					});
				
				var post = $this.find('.search_on').serialize();
				AJAXsend['search'] = methods.encode_base64(encodeURIComponent(post));
				
				$.ajaxAG(
				{
					url: location,
					type: "POST",
					data: AJAXsend,
					success: function(d)
					{
						$this.html(d);
						methods.reloadGrid($this);
					}
				});
			},
			sendData : function ($this, checkbox_submit)
			{
				var send = true;
				
				var page = $this.find('input[name=page]').val();
				var sort = $this.find('input[name=sort]').val();
				var limit = $this.find('select[name=limit]').val();
				var href = options.url;	
				var AJAXsend = {};
				AJAXsend['page'] = page;
				AJAXsend['sort'] = sort;
				AJAXsend['limit'] = limit;
				AJAXsend['ajax'] = 1;
				var s_i = $this.find('input,select');
				
				$(s_i).attr('class','');
				$(s_i).each(
					function(i)
					{
						if($.trim($(s_i[i]).val())!='' && $(s_i[i]).attr('type')!='hidden' && $(s_i[i]).attr('name')!='limit' && $(s_i[i]).attr('type')!='checkbox')
						{
							$(s_i[i]).attr('class','search_on');
						}
					});
				
				var post = $this.find('.search_on').serialize();
				AJAXsend['search'] = methods.encode_base64(encodeURIComponent(post));
				if(checkbox_submit)
				{
					var checkbox_array = new Array();
					var checkbox = $this.find('#action_checkbox_checked');
					if(checkbox.length > 0 && $this.find('#action_submit').val() != '')
					{
						$.each(checkbox,
							function(i)
							{
								checkbox_array[i] = $(checkbox[i]).val();
								ckbname = $(checkbox[i]).attr('name');
							}
						);
						AJAXsend[ckbname] = checkbox_array;
						AJAXsend[$this.find('#action_submit').attr('name')] = $this.find('#action_submit').val();
					}
					else
					{
						alert('Элементы или действие не выбраны, запрос не может быть выполнен!');
						var send = false;
					}
				}
				if(send)
				{
					jQuery.ajaxAG(
						{
							url: href,
							type: "POST",
							data: AJAXsend,
							success: function(d)
							{
								$this.html(d);
								methods.reloadGrid($this);
							}
						}
					);
				}	
			},
			reloadGrid : function()
			{
				datepicker_load();
			},
			initFixedButtons : function($this)
			{
				var GID = $this.attr('id');
				var FIX_TOP_BLOCK = $('#fixed_top').find('#'+GID+'_hide_buttons');
				$(window).scroll(function()
				{
					methods.checkScroll($this, FIX_TOP_BLOCK);
				});
			},
			checkScroll : function($this, FIX_TOP_BLOCK)
			{
				var $YD = $this.find('#grid_buttons');
				FIX_BUT_Y = $YD.offset().top + $YD.height();
				if(check_height)
				{
					FIX_BUT_Y = FIX_BUT_Y + $(FIX_TOP_BLOCK).height();
				}
				
				if($(window).scrollTop()+$('#fixed_top').height() > FIX_BUT_Y)
				{
					methods.show_TopFixedButtons(FIX_TOP_BLOCK, 'block');
					check_height = true;
				}
				else
				{
					methods.show_TopFixedButtons(FIX_TOP_BLOCK, 'none');
					check_height = false;
				}
			},
			show_TopFixedButtons : function(BOBJ, d)
			{
				$(BOBJ).css('display',d);
				$(BOBJ).animate({opacity:'0.7'},1);
			},
			encode_base64 : function(what)
			{
				var base64_encodetable = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
				var result = "";
				var len = what.length;
				var x, y;
				var ptr = 0;

				while( len-- > 0 )
				{
					x = what.charCodeAt( ptr++ );
					result += base64_encodetable.charAt( ( x >> 2 ) & 63 );

					if( len-- <= 0 )
					{
						result += base64_encodetable.charAt( ( x << 4 ) & 63 );
						result += "==";
						break;
					}

					y = what.charCodeAt( ptr++ );
					result += base64_encodetable.charAt( ( ( x << 4 ) | ( ( y >> 4 ) & 15 ) ) & 63 );

					if ( len-- <= 0 )
					{
						result += base64_encodetable.charAt( ( y << 2 ) & 63 );
						result += "=";
						break;
					}

					x = what.charCodeAt( ptr++ );
					result += base64_encodetable.charAt( ( ( y << 2 ) | ( ( x >> 6 ) & 3 ) ) & 63 );
					result += base64_encodetable.charAt( x & 63 );
				}
				return result;
			}
		}
		
		return methods.init.apply( this );
		/*if ( methods[method] )
		{
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		}
		else if ( typeof method === 'object' || ! method )
		{
			return methods.init.apply( this, arguments );
		}
		else
		{
			$.error( 'Метод ' +  method + ' в jQuery.tooltip не существует' );
		}*/
	};
})( jQuery );