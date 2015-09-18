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
	$.ajaxAG = function(options){
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
		
		jQuery.ajax(options);
	}
})(jQuery);