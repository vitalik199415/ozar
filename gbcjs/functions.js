function datepicker_load()
{
	$(".datepicker").datepick({
		dateFormat: "yyyy-mm-dd"
	});
}
function delete_question()
{
	$('.delete_question').live('click', function()
		{
			if(confirm('Вы уверены что ходите удалить элемент?'))
			{
				return true;
			}
			return false;
		}
	);
}
function action_question()
{
	$('.action_question').live('click', function()
		{
			if(confirm('Вы уверены что ходите совершить это действие?'))
			{
				return true;
			}
			return false;
		}
	);
}

function change_TopFixedBlockOpacity()
{
	$('#fixed_top').live('mouseover, mouseout', function()
	{
		$(this).animate({opacity:'0.7'},1);
		$(this).mouseover(function(){
			$(this).stop().animate({opacity:'1.0'},500);
		});
		$(this).mouseout(function(){
			$(this).stop().animate({opacity:'0.7'},500);
		});
	});
}

function start_js_func()
{
	datepicker_load();
	delete_question();
	action_question();
	change_TopFixedBlockOpacity();
	//$('#multi-ddm').dropDownMenu({timer: 500, parentMO: 'parent-hover', childMO: 'child-hover1'});
}

function hide_massages()
{
	jQuery('.succes_error_massages').hide('blind',{}, 1000, function()
		{
			jQuery(this).remove();
		}
	);
}

function loading_start()
{
	var loading = $('.loading');
		
		jQuery(loading).fadeTo(10,0.0);
		jQuery(loading).css('display','block');
		jQuery(loading).fadeTo(400,0.7);		
}
function loading_stop()
{
	var loading = $('.loading');
		jQuery(loading).fadeTo(400,0.0,function()
			{
				jQuery(loading).css('display','none');	
			}
		);
}

(function($) {
	return $.ajaxAG = function(options){
		var options = $.extend({
		ulr : "",
		type: "POST",
		data: {},
		beforeSend: function(){loading_start();},
		success : function(){},
		statusCode:
			{
				303: function()
				{
					document.location = '';
				}
			},
		complete: function(){loading_stop();}	
		},options);
		return jQuery.ajax(options);
	}
})(jQuery);