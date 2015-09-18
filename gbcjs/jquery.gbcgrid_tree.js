jQuery.fn.create_grid_tree = function(option)
{
	var option = jQuery.extend(
	   {
			
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

		var href = '?ajax=1';
		var AJAXsend = {};
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
						//reloadGrid();
					}
				}
			);
		}	
	}
	
	jQuery(OBJ).find('.load').live('click', function()
	{
		loadCategories(this, jQuery(this).attr('id'));
		return false;
	});
	jQuery(OBJ).find('.unload').live('click', function()
	{
		unloadCategories(this, jQuery(this).attr('id'));
		return false;
	});
	
	function loadCategories(ob, id)
	{
		var id = Number(id);
		if(id>0)
		{
			var href = '?ajax=1';
			var AJAXsend = {};
			AJAXsend['id'] = id;
			jQuery.ajaxAG(
				{
					url: href,
					type: "POST",
					data: AJAXsend,
					success: function(d)
					{
						jQuery(OBJ).html(d);
					}
				}
			);
		}
	}
	
	function unloadCategories(ob, id)
	{
		var id = Number(id);
		if(id>0)
		{
			var href = '?ajax=1';
			var AJAXsend = {};
			AJAXsend['id_unload'] = id;
			jQuery.ajaxAG(
				{
					url: href,
					type: "POST",
					data: AJAXsend,
					success: function(d)
					{
						jQuery(OBJ).html(d);
					}
				}
			);
		}
	}
	
	
	jQuery(OBJ).find('.arrow_down').live('click',function()
		{
			var data = {cid:$(this).attr('href'), type:'down'}
			categories_grid_change_position(data);
			return false;
		}
	);
	jQuery(OBJ).find('.arrow_up').live('click',function()
		{
			var data = {cid:$(this).attr('href'), type:'up'}
			categories_grid_change_position(data);
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
	initFixedButtons();
}