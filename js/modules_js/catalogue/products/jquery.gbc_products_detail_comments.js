(function( $ )
{
	$.fn.gbc_products_detail_comments = function(method, settings)
	{
		var option = new Object(
		{
			message_overlay_id : '#site_messages_overlay',
			message_overlay_top : '35%',
			overlay_close : '.close',
			
			message_overlay_content_id : '#site_messages_overlay_content',
			message_block_id : '#site_message_message_block',
			
			overlay_id : '#product_overlay_content',
			customers_overlay_id : '#customers_overlay_content',
			
			comments_block_id : '#product_comments',
			comments_pagination_id : '#product_comments_pagination',
			form_id : '#product_comments_form_',
			
			error_html_open : '<div class="form_message_block site_message_message_block" id="site_message_message_block"><div class="error_message" id="error"><div><p>',
			error_html_close : '</p></div></div></div>',
			
			error_submit : 'Error submit form!',
			product_id : false,
			this_where_form_submit : false
		});
		
		var message_block_overlay = $(option.message_overlay_id).overlay(
		{
			api: true, 
			oneInstance : false,
			top : option.message_overlay_top,
			close : option.overlay_close,
			left: 'left',
			mask: {
				color: '#000000',
				loadSpeed: 200,
				opacity: 0.8
				}
		});
		
		var methods = new Object(
		{
			init : function(settings)
			{
				option = $.extend(option, settings);
				return this.each(function(i,el)
				{
					var $this = $(el);
					var data = $this.data('products_detail_comments'+option.product_id);
					if(!data)
					{
						$this.data('products_detail_comments'+option.product_id, true);
						
						methods.init_comments_block.apply($this, Array());
						methods.init_form_block.apply($this, Array());
					}
				});
			},
			
			init_comments_block : function()
			{
				var $this = (this);
				$this.find(option.comments_pagination_id+' a').on('click', function()
				{
					var href = $(this).attr('href');
					methods.get_comments_ajax.apply($this, Array(href));
					return false;
				});
			},
			
			get_comments_ajax :  function(href)
			{
				var $this = $(this);
				jQuery.ajaxAG(
				{
					url: href,
					type: "GET",
					dataType : 'html',
					success: function(d)
					{
						$this.find(option.comments_block_id).html(d);
						methods.init_comments_block.apply($this, Array());
					}
				});
			},
			
			init_form_block : function()
			{
				var $this = $(this);
				option.this_where_form_submit = $this;
				$form = $this.find(option.form_id+option.product_id);
				$form.validate({
					rules : {
						"name" : 	{required: true, minlength: 4},
						"email" : 	{required: true, email: true},
						"message" : {required: true, minlength: 10},
						"captcha" : {required: true, minlength: 6}
					},
					errorPlacement: function(error, element) {
						error.insertAfter(element.parent('div').find('div'));
					}
				});
				$form.find('#submit').on('click', function()
				{
					var options = { 
						beforeSubmit: function() { return methods.form_validate($form, $form) },
						success: methods.form_send_message_success,
						dataType:  'json'
					};
					$($form).ajaxSubmit(options);
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
					$error_html = option.error_html_open+option.error_submit+option.error_html_close;
					methods.show_error_block(jqForm, $error_html);
					return false;
				}
			},
			
			form_send_message_success : function(responseText, statusText, xhr, $form)
			{
				if(responseText.success == 0)
				{
					$form.find('#captcha_img').replaceWith(responseText.img);
					methods.show_error_block($form, responseText.messages);
				}
				if(responseText.success == 1)
				{
					if(responseText.reload == 1)
					{
						option.this_where_form_submit.find(option.comments_block_id).html(responseText.comments_html);
						methods.init_comments_block.apply(option.this_where_form_submit, Array());
					}
					
					methods.show_success_block($form, responseText.messages);
					$form.find('#captcha_img').replaceWith(responseText.img);
					$form.trigger( 'reset' );
				}
				loading_stop();
			},
		
			show_error_block : function($form, errors_html)
			{
				$(option.message_overlay_content_id).html(errors_html);
				$(option.message_overlay_content_id).find(option.message_block_id).find('#error').css('display', 'block');
				$(option.message_overlay_content_id).find(option.message_block_id).css('display', 'block');
				
				message_block_overlay.load();
				
				setTimeout(function($message_block_overlay)
				{
					message_block_overlay.close();
				}, 3000, message_block_overlay);
			},
			
			show_success_block : function($form, success_html)
			{
				$(option.message_overlay_content_id).html(success_html);
				$(option.message_overlay_content_id).find(option.message_block_id).find('#success').css('display', 'block');
				$(option.message_overlay_content_id).find(option.message_block_id).css('display', 'block');
				
				message_block_overlay.load();
				
				setTimeout(function($message_block_overlay)
				{
					message_block_overlay.close();
				}, 5000, message_block_overlay);
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