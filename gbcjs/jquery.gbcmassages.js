jQuery.fn.initSEmassages = function(option)
{
	/*var option = jQuery.extend(
	{
		DMblocks : jQuery('.succes_error_massages_id'),
		DM_FIXblocks : jQuery('#fixed_top').find('.succes_error_massages')
	},option);*/
	
	var OBJ = this;
	
	/*jQuery(option.DMblocks).each(function(i)
	{
		if(i < (option.DMblocks.length-1))
		{
			jQuery(option.DMblocks[i]).hide('blind',{}, 500, function()
			{
				jQuery(this).remove();
			});
			jQuery(option.DM_FIXblocks[i]).hide('blind',{}, 500, function()
			{
				jQuery(this).remove();
			});
		}
	});*/
	
	//jQuery('#fixed_top').prepend(jQuery(OBJ).html());
	//var FIX_OBJ = jQuery('#fixed_top').find('.succes_error_massages');
	//jQuery(FIX_OBJ).css('display','none');
	if(OBJ)
	{
		setTimeout(function()
		{
			jQuery(OBJ).hide('blind',{}, 1000,function()
			{
				jQuery(this).remove();
			});
			/*if($(FIX_OBJ).css('display') == 'block')
			{
				jQuery(FIX_OBJ).hide('blind',{}, 1000,function()
				{
					jQuery(this).remove();
				});
			}
			else
			{
				jQuery(this).remove();
				FIX_OBJ = false;
			}*/
		},8000)
	}
	else
	{
		return false;
	}
	

	/*$(window).scroll(function()
	{
		checkScroll();
	});
	
	function checkScroll()
	{
		if(FIX_OBJ)
		{
			FIX_MS_Y = jQuery(OBJ).offset().top + jQuery(OBJ).height();
			if(jQuery(this).scrollTop() > FIX_MS_Y)
			{
				show_TopFixedMassages('block');
			}
			else
			{
				show_TopFixedMassages('none');
			}
		}	
	}
	function show_TopFixedMassages(d)
	{
		$(FIX_OBJ).css('display',d);
		$(FIX_OBJ).animate({opacity:'0.7'},1);
	}*/
}