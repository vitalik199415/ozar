(function( $ )
{
var option = new Object(
{
	overlay_id : '#customers_overlay',
	overlay_cn : '#customers_overlay_content',
	overlay_top : '9%',
	overlay_close : '.close',
	
	customer_registration : '#customer_registration',
	customer_office : '#customer_office',
	customer_login : '#customer_login',
	
	message_block_id : '#customers_message_block',
	success_block_id : '.success_message',
	error_block_id : '.error_message',
	
	submit : '#submit, #submit_bot',
	
	url_registration_check_email : false,
	url_registration_form : false,
	url_login_form : false,
	url_forgot_password_form : false,
	url_change_password_form : false,
	url_write_admin_form : false,
	
	error_login_submit : false,
	error_fp_submit : false,
	error_cp_submit : false,
	
	error_submit : false,
	error_customer_exists : false
});

$.fn.gbc_customers = function(method, settngs)
{	
	var customers_block = $(option.overlay_id).overlay({
	api: true, 
	oneInstance : true,
	top : option.overlay_top,
	close: option.overlay_close,
	left: 'left',
	mask: {
		color: '#000000',
		loadSpeed: 200,
		opacity: 0.8
		}
	});
	
	var methods = 
	{
		init : function(settings)
		{
			option = $.extend(option, settings);
			return this.each(function(i,el)
			{
				var $this = $(el);
				var data = $this.data('gbc_customers');
				if(!data)
				{	
					$this.data('gbc_customers', true);
					
					methods.init_buttons.apply($this, Array());
				}
			});
		},
		
		init_buttons : function()
		{
			var $this = $(this);
			$this.find(option.customer_registration).click(function()
			{
				methods.show_registration_form();			
				return false;
			});
			$this.find(option.customer_office).click(function()
			{
				methods.show_office_form();			
				return false;
			});
			$this.find(option.customer_login).click(function()
			{
				methods.show_login_form();			
				return false;
			});
		},
		
		show_registration_form : function()
		{
			if(option.url_registration_form !== false)
			{
				jQuery.ajaxAG(
				{
					url: option.url_registration_form,
					type: "GET",
					data: {},
					success: function(d)
					{
						$(option.overlay_cn).html(d);
						customers_block.load();
					}
				});
			}
		},
		
		show_office_form : function()
		{
			if(option.url_office_form !== false)
			{
				jQuery.ajaxAG(
				{
					url: option.url_office_form,
					type: "GET",
					data: {},
					success: function(d)
					{
						$(option.overlay_cn).html(d);
						customers_block.load();
					}
				});
			}
		},
		
		show_login_form : function()
		{
			if(option.url_login_form !== false)
			{
				jQuery.ajaxAG(
				{
					url: option.url_login_form,
					type: "GET",
					data: {},
					success: function(d)
					{
						$(option.overlay_cn).html(d);
						customers_block.load();
					}
				});
			}	
		},
		
		show_forgot_password_form : function()
		{
			if(option.url_forgot_password_form !== false)
			{
				jQuery.ajaxAG(
				{
					url: option.url_forgot_password_form,
					type: "GET",
					data: {},
					success: function(d)
					{
						$(option.overlay_cn).html(d);
					}
				});
			}	
		},
		
		show_change_password_form : function()
		{
			if(option.url_change_password_form !== false)
			{
				jQuery.ajaxAG(
				{
					url: option.url_change_password_form,
					type: "GET",
					data: {},
					success: function(d)
					{
						$(option.overlay_cn).html(d);
					}
				});
			}
		},
		
		show_write_admin_form : function()
		{
			if(option.url_write_admin_form !== false)
			{
				jQuery.ajaxAG(
				{
					url: option.url_write_admin_form,
					type: "GET",
					data: {},
					success: function(d)
					{
						$(option.overlay_cn).html(d);
					}
				});
			}
		},
		
		init_customers_registration : function($r_f)
		{
			var $this = $(this);
			$this.validate({
				rules : $r_f,
				messages : {
					"customer[email]" : {remote: option.error_customer_exists}
				},
				errorPlacement: function(error, element) {
					error.insertAfter(element.parent('div').find('div'));
				}
			});
			
			$this.find(option.submit).click(function()
			{
				methods.hide_message_block.apply($this);
				var options = { 
					beforeSubmit: function() { return methods.form_validate.apply($this, Array(option.error_submit)) },
					success: methods.form_customers_registration_success,
					dataType:  'json'
				};
				$($this).ajaxSubmit(options);
				return false;
			});
		},
		
		init_customers_office : function()
		{
			$this = $(this);
			$this.validate({
				rules : {
					"customer[name]" : 				{required: true},
					"captcha" : 					{required: true, minlength: 6}
				},
				errorPlacement: function(error, element) {
					error.insertAfter(element.parent('div').find('div'));
				}
			});
			
			$this.find('#change_password').click(function()
			{
				methods.show_change_password_form();
			});
			
			$this.find('#write_admin').click(function()
			{
				methods.show_write_admin_form();
			});
			
			$this.find('#submit').click(function()
			{
				methods.hide_message_block.apply($this);
				var options = { 
					beforeSubmit: function() { return methods.form_validate.apply($this, Array(option.error_submit)) },
					success: methods.form_customers_edit_success,
					dataType:  'json'
				};
				$($this).ajaxSubmit(options);
				return false;
			});
		},
		
		init_customers_login : function()
		{
			$this = $(this);
			$this.validate({
				rules : {
					"email" : 			{required: true, email: true},
					"password" : 		{required: true, minlength: 6}
				},
				errorPlacement: function(error, element) {
					error.insertAfter(element.parent('div').find('div'));
				}
			});
			
			$this.find('#submit').click(function()
			{
				methods.hide_message_block.apply($this);
				var options = { 
					beforeSubmit: function() { return methods.form_validate.apply($this, Array(option.error_submit)) },
					success: methods.form_customers_login_success,
					dataType:  'json'
				};
				$($this).ajaxSubmit(options);
				return false;
			});
			
			$this.find('#customer_registration').click(function()
			{
				methods.show_registration_form();			
				return false;
			});
			
			$this.find('#customer_forgot_password').click(function()
			{
				methods.show_forgot_password_form();			
				return false;
			});
		},
		
		init_customers_forgot_password : function()
		{
			$this = $(this);
			$this.validate({
				rules : {
					"email" : 			{required: true, email: true},
					"captcha" : 		{required: true, minlength: 6}
				},
				errorPlacement: function(error, element) {
					error.insertAfter(element.parent('div').find('div'));
				}
			});
			
			$this.find('#submit').click(function()
			{
				methods.hide_message_block.apply($this);
				var options = { 
					beforeSubmit: function() { return methods.form_validate.apply($this, Array(option.error_submit)) },
					success: methods.form_customers_forgot_password_success,
					dataType:  'json'
				};
				$($this).ajaxSubmit(options);
				return false;
			});
		},
		
		init_change_password : function()
		{
			$this = $(this);
			$this.validate({
				rules : {
					"old_password" : 			{required: true, minlength: 6},
					"new_password" : 			{required: true, minlength: 6},
					"confirm_password" : 		{equalTo: "#new_password"},
					"captcha" : 				{required: true, minlength: 6}
				},
				errorPlacement: function(error, element) {
					error.insertAfter(element.parent('div').find('div'));
				}
			});
			
			$this.find('#submit').click(function()
			{
				methods.hide_message_block.apply($this);
				var options = { 
					beforeSubmit: function() { return methods.form_validate.apply($this, Array(option.error_submit)) },
					success: methods.form_change_password_success,
					dataType:  'json'
				};
				$($this).ajaxSubmit(options);
				return false;
			});
		},
		
		init_customers_wa : function()
		{
			$this = $(this);
			$this.validate({
				rules : {
					"message" : {required: true, minlength: 10},
					"captcha" : {required: true, minlength: 6}
				},
				errorPlacement: function(error, element) {
					error.insertAfter(element.parent('div').find('div'));
				}
			});
			
			$this.find('#submit').click(function()
			{
				methods.hide_message_block.apply($this);
				var options = { 
					beforeSubmit: function() { return methods.form_validate.apply($this, Array(option.error_submit)) },
					success: methods.form_wa_success,
					dataType:  'json'
				};
				$($this).ajaxSubmit(options);
				return false;
			});
		},
		
		form_validate : function(error)
		{
			var $this = $(this);
			if($this.valid())
			{
				loading_start();
				return true;
			}
			else
			{
				methods.show_errors.apply($this, Array(error));
				return false;
			}	
		},
		
		form_customers_registration_success : function(responseText, statusText, xhr, $form)
		{
			var $this = $($form);
			if(responseText.status == 0)
			{
				$this.find('#customer_registration_captcha_img').replaceWith(responseText.img);
				methods.show_errors.apply($this, Array(responseText.errors));
			}
			if(responseText.status == 1)
			{
				methods.show_errors.apply($this, Array(responseText.errors));
			}
			if(responseText.status == 2)
			{
				methods.show_success.apply($this, Array(responseText.success));
				$this.trigger( 'reset' );
				setTimeout(function(block_overlay, overlay_close)
				{
					$(block_overlay).find(overlay_close).trigger('click');
				}, 10000, option.overlay_id, option.overlay_close);
				$form.find('#submit').remove();
			}
			loading_stop();
		},
		
		form_customers_edit_success : function(responseText, statusText, xhr, $form)
		{
			var $this = $($form);
			if(responseText.status == 0)
			{
				$this.find('#customer_registration_captcha_img').replaceWith(responseText.img);
				methods.show_errors.apply($this, Array(responseText.errors));
			}
			if(responseText.status == 1)
			{
				methods.show_errors.apply($form, Array(responseText.errors));
			}
			if(responseText.status == 2)
			{
				methods.show_success.apply($form, Array(responseText.success));
				$this.find('#customer_registration_captcha_img').replaceWith(responseText.img);
			}
			loading_stop();
		},
		
		form_customers_login_success : function(responseText, statusText, xhr, $form)
		{
			var $this = $($form);
			if(responseText.status == 0)
			{
				methods.show_errors.apply($this, Array(responseText.errors));
			}
			if(responseText.status == 1)
			{
				methods.show_success.apply($this, Array(responseText.success));
				$this.trigger( 'reset' );
				setTimeout('location.reload()', 1000);
			}
			loading_stop();
		},
		
		form_customers_forgot_password_success : function(responseText, statusText, xhr, $form)
		{
			var $this = $($form);
			if(responseText.status == 0)
			{
				methods.show_errors.apply($this, Array(responseText.errors));
			}
			if(responseText.status == 1)
			{
				$this.find('#customer_forgot_password_captcha_img').replaceWith(responseText.img);
				methods.show_errors.apply($this, Array(responseText.errors));
			}
			if(responseText.status == 2)
			{
				methods.show_success.apply($this, Array(responseText.success));
				$this.trigger( 'reset' );
				setTimeout(function(block_overlay, overlay_close)
				{
					$(block_overlay).find(overlay_close).trigger('click');
				}, 8000, option.overlay_id, option.overlay_close);
			}
			loading_stop();
		},
		
		form_change_password_success : function(responseText, statusText, xhr, $form)
		{
			var $this = $($form);
			if(responseText.status == 0)
			{
				$this.find('#customer_change_password_captcha_img').replaceWith(responseText.img);
				methods.show_errors.apply($this, Array(responseText.errors));
				$this.trigger('reset');
			}
			if(responseText.status == 1)
			{
				$this.find('#customer_change_password_captcha_img').replaceWith(responseText.img);
				methods.show_errors.apply($this, Array(responseText.errors));
			}
			if(responseText.status == 2)
			{
				methods.show_success.apply($this, Array(responseText.success));
				$this.trigger( 'reset' );
				setTimeout(function(block_overlay, overlay_close)
				{
					$(block_overlay).find(overlay_close).trigger('click');
				}, 5000, option.overlay_id, option.overlay_close);
			}
			loading_stop();
		},
		
		form_wa_success : function(responseText, statusText, xhr, $form)
		{
			var $this = $($form);
			if(responseText.status == 0)
			{
				$this.find('#customer_wa_captcha_img').replaceWith(responseText.img);
				methods.show_errors.apply($this, Array(responseText.errors));
			}
			if(responseText.status == 1)
			{
				methods.show_success.apply($this, Array(responseText.success));
				$this.trigger( 'reset' );
			}
			loading_stop();
		},
		
		show_errors : function(errors)
		{
			var $this = $(this);
			$this.find(option.message_block_id).find(option.error_block_id).find('div').html(errors);
			$this.find(option.message_block_id).find(option.error_block_id).show();
			$this.find(option.message_block_id).show();
			$(option.overlay_cn).scrollTo(option.message_block_id, {duration : 2000});

		},
		
		show_success : function(success)
		{
			var $this = $(this);
			$this.find(option.message_block_id).find(option.success_block_id).find('div').html(success);
			$this.find(option.message_block_id).find(option.success_block_id).show();
			$this.find(option.message_block_id).show();
			$(option.overlay_cn).scrollTo(option.message_block_id, {duration : 2000});

		},
		
		hide_message_block : function()
		{
			var $this = $(this);
			$this.find(option.message_block_id).find(option.error_block_id).find('div').html('');
			$this.find(option.message_block_id).find(option.success_block_id).find('div').html('');
			$this.find(option.message_block_id).find(option.error_block_id).hide();
			$this.find(option.message_block_id).find(option.success_block_id).hide();
			$this.find(option.message_block_id).hide();
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