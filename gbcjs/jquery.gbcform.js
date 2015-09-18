jQuery.fn.create_form = function(id,option)
{
	var id = id;
	var option = jQuery.extend(
		{
			
		},option);
	var OBJ = this;
	var check_height = false;
	var FIX_TOP = jQuery("#fixed_top");
	
	jQuery('#fixed_top').append(jQuery(OBJ).find('#top_fixed_buttons').html());
	$("#"+id+" .tabs_block ul").tabs("#"+id+" .block .block_padding div.field_block", {history: true});
	
	jQuery('#submit').live('click',
		function()
		{
			jQuery(OBJ).find('#form_'+id).submit();
			return false;
		}
	);	
	
	jQuery('#submit_back').live('click',
		function()
		{
			F = jQuery(OBJ).find('#form_'+id);
			var LOCATION = new String(window.location);
			var POS = LOCATION.lastIndexOf('#');
			var RET = '';
			if(POS>0)
			{
				RET = '&tab='+LOCATION.substring(POS);
			}
			jQuery(F).attr('action',jQuery(F).attr('action')+'?return=1'+RET)
			jQuery(F).submit();
			return false;
		}	
	);
	
	function initFixedButtons()
	{
		GID = jQuery(OBJ).attr('id');
		FIX_TOP_BLOCK = jQuery('#fixed_top').find('#hide_buttons');
		$(window).scroll(function()
		{
			checkScroll();
		});
	}
	function checkScroll()
	{
		YD = jQuery(OBJ).find('#form_buttons');
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