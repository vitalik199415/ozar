(function( $ )
{
$.fn.gbc_contacts = function(method, option)
{
	var option = $.extend(
	{
		error_submit : false
	},option);
	
	var methods = 
	{	
		init : function()
		{
			return this.each(function()
			{
				var $this = $(this);
				//data = $this.data('products_full');
				methods.init_contacts_form($this);
				/*if(!data)
				{
					
					methods.init_contacts_form($this);
				}*/
			});
		},
		
		init_contacts_form : function($this)
		{
			$this.validate({
				rules : {
					"name" : 	{required: true, minlength: 6},
					"email" : 	{required: true, email: true},
					"text" : 	{required: true, minlength: 10},
					"captcha" : {required: true, minlength: 6}
				},
				errorPlacement: function(error, element) {
					error.insertAfter(element.parent('div').find('div'));
				}
			});
			
			$this.find('#submit').click(function()
			{
				methods.hide_message_block($this);
				var options = { 
					beforeSubmit: function() { return methods.form_validate($this, $this) },
					success: methods.form_send_message_success,
					dataType:  'json'
				};
				$($this).ajaxSubmit(options);
				return false;
			});
		},
		
		form_validate : function(formData, jqForm, options)
		{
			if(jqForm.valid())
			{
				loading_start();
				return true;
			}
			else
			{
				methods.show_errors(jqForm, option.error_submit);
				return false;
			}	
		},
		
		form_send_message_success : function(responseText, statusText, xhr, $form)
		{
			if(responseText.status == 0)
			{
				$form.find('#contacts_form_captcha_img_'+responseText.id).replaceWith(responseText.img);
				methods.show_errors($form, responseText.errors);
			}
			if(responseText.status == 1)
			{
				methods.show_success($form, responseText.success);
				$form.find('#contacts_form_captcha_img_'+responseText.id).replaceWith(responseText.img);
				$form.trigger( 'reset' );
			}
			loading_stop();
		},
		
		show_errors : function($form, errors)
		{
			$form.find('#form_massage_block').find('#error').find('div').html(errors);
			$form.find('#form_massage_block').find('#error').css('display', 'block');
			$form.find('#form_massage_block').css('display', 'block');
			$(option.overlay_id).scrollTo('#form_massage_block', {duration : 2000});

		},
		
		show_success : function($form, success)
		{
			$form.find('#form_massage_block').find('#success').find('div').html(success);
			$form.find('#form_massage_block').find('#success').css('display', 'block');
			$form.find('#form_massage_block').css('display', 'block');
			$(option.overlay_id).scrollTo('#form_massage_block', {duration : 2000});

		},
		
		hide_message_block : function($form)
		{
			$form.find('#form_massage_block').find('#error').find('div').html('');
			$form.find('#form_massage_block').find('#success').find('div').html('');
			$form.find('#form_massage_block').find('#success').css('display', 'none');
			$form.find('#form_massage_block').find('#error').css('display', 'none');
			$form.find('#form_massage_block').css('display', 'none');
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