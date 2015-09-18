function encode_base64( what )
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

jQuery.fn.create_grid = function(option)
{
	var option = jQuery.extend(
	   {
			url : '',
			init_fixed_buttons : 1
	   },option);
	var OBJ = this;
	
	var GID;
	var YD;
	
	var FIX_TOP_BLOCK;
	var check_height = false;
	
	
	jQuery(OBJ).find('.field_data').live('mouseover', function()
	{
		$(this).addClass('field_data_hover');
	});
	jQuery(OBJ).find('.field_data').live('mouseout', function()
	{
		$(this).removeClass('field_data_hover');
	});
	
	
	jQuery('#fixed_top').append(jQuery(OBJ).find('#top_fixed_buttons').html());
		
	jQuery(OBJ).find('.search_button').live('click',
		function()
		{
			if(jQuery(this).attr('rel')=='search')
			{
				sendData();
				return false;
			}
			else
			{
				jQuery(OBJ).find('form').find('input,select').val('');
				sendData();
				return false;
			}
		}
	);	
	jQuery(OBJ).find('.up , .down').live('click',
		function()
		{
			var sortInput = jQuery(OBJ).find('input[name=sort]');
			jQuery(sortInput).val(jQuery(this).attr('rel'));
			sendData();
			return false;
		}
	);
	jQuery(OBJ).find('.pages_block .page').live('click',
		function()
		{
			var pageInput = jQuery(OBJ).find('input[name=page]');
			jQuery(pageInput).val(jQuery(this).attr('rel'));
			sendData();
			return false;
		}
	);
	jQuery(OBJ).find('.pages_block select[name=limit]').live('change',
		function()
		{
			sendData();
			return false;
		}
	);
	jQuery(OBJ).find('#action_checkbox').live('change',
		function()
		{
			if(jQuery(this).attr('checked'))
			{
				jQuery(this).parents('tr').addClass('field_data_checked');
			}
			else
			{
				jQuery(this).parents('tr').removeClass('field_data_checked');
			}
		}
	);
	jQuery(OBJ).find('.select_all a').live('click',
		function()
		{
			var checkbox = jQuery(OBJ).find('#action_checkbox');
			if(jQuery(this).attr('rel')=='check')
			{
				jQuery(checkbox).attr("checked","checked");
				jQuery(checkbox).parents('tr').addClass('field_data_checked');
			}
			else if(jQuery(this).attr('rel')=='uncheck')
			{
				jQuery(checkbox).removeAttr("checked");
				jQuery(checkbox).parents('tr').removeClass('field_data_checked');
			}
			return false;
		}
	);
	jQuery(OBJ).find('#submit').live('click',
		function()
		{
			var checkbox = jQuery(OBJ).find('#action_checkbox');
			jQuery.each(checkbox,
				function(i)
				{
					if(jQuery(checkbox[i]).attr('checked'))
					{
						jQuery(checkbox[i]).attr('id','action_checkbox_checked');
					}
				}
			)
			sendData(true);
			return false;
		}
	);
	function sendData(checkbox_submit)
	{
		var send = true;
		var page = jQuery(OBJ).find('input[name=page]').val();
		var sort = jQuery(OBJ).find('input[name=sort]').val();
		var limit = jQuery(OBJ).find('select[name=limit]').val();
		var href = option.url+'?page='+page+'&sort='+sort+'&limit='+limit+'&ajax='+1;	
		var s_i = jQuery(OBJ).find('form').find('input,select');
		jQuery(s_i).attr('class','');
		jQuery(s_i).each(
			function(i)
			{
				if(jQuery.trim(jQuery(s_i[i]).val())!='' && jQuery(s_i[i]).attr('type')!='hidden' && jQuery(s_i[i]).attr('type')!='checkbox')
				{
					jQuery(s_i[i]).attr('class','search_on');
				}
			});
		var AJAXsend = {};
		var post = jQuery(OBJ).find('.search_on').serialize();
		AJAXsend['search'] = encode_base64(encodeURIComponent(post));
		
		if(checkbox_submit)
		{
			var checkbox_array = new Array();
			var checkbox = jQuery(OBJ).find('#action_checkbox_checked');
			if(checkbox.length > 0 && jQuery(OBJ).find('#action_submit').val() != '')
			{
				jQuery.each(checkbox,
					function(i)
					{
						checkbox_array[i] = jQuery(checkbox[i]).val();
						ckbname = jQuery(checkbox[i]).attr('name');
					}
				);
				AJAXsend[ckbname] = checkbox_array;
				AJAXsend[jQuery(OBJ).find('#action_submit').attr('name')] = jQuery(OBJ).find('#action_submit').val();
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
						jQuery(OBJ).html(d);
						reloadGrid();
					}
				}
			);
		}	
	}
	
	jQuery(OBJ).find('.arrow_down').live('click',function()
		{
			alert('123');
			//var data = {cid:$(this).attr('href'), type:'down'}
			//categories_grid_change_position(data);
			return false;
		}
	);
	jQuery(OBJ).find('.arrow_up').live('click',function()
		{
			//var data = {cid:$(this).attr('href'), type:'up'}
			//categories_grid_change_position(data);
			return false;
		}
	);
	function categories_grid_change_position(data)
	{
		var location = window.location.href;
		location = location+'?ajax=1';

		$.ajaxAG(
		{
			url: location,
			type: "POST",
			data: data,
			success: function(d){$(OBJ).html(d);}
		});
	}
	
	function initFixedButtons()
	{
		GID = jQuery(OBJ).attr('id');
		FIX_TOP_BLOCK = jQuery('#fixed_top').find('#'+GID+'_hide_buttons');
		$(window).scroll(function()
		{
			checkScroll();
		});
	}
	function checkScroll()
	{
		YD = jQuery(OBJ).find('#grid_buttons');
		FIX_BUT_Y = jQuery(YD).offset().top + jQuery(YD).height();
		if(check_height)
		{
			FIX_BUT_Y = FIX_BUT_Y + jQuery(FIX_TOP_BLOCK).height();
		}
		
		if(jQuery(this).scrollTop()+jQuery('#fixed_top').height() > FIX_BUT_Y)
		{
			show_TopFixedButtons(FIX_TOP_BLOCK, 'block');
			check_height = true;
		}
		else
		{
			show_TopFixedButtons(FIX_TOP_BLOCK, 'none');
			check_height = false;
		}
	}
	function show_TopFixedButtons(BOBJ, d)
	{
		$(BOBJ).css('display',d);
		$(BOBJ).animate({opacity:'0.7'},1);
	}
	if(option.init_fixed_buttons)
	{
		initFixedButtons();
	}	
}