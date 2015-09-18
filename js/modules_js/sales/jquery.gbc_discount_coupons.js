(function( $ )
{
    var gbc_discount_coupons_options = {
        url: false,
		promocode_field_id: '#promocode',
		activate_message_block_id: '.activate_message_block',
		activate_button_id: '#activate',
		success_block_id : '.success_message',
		error_block_id : '.error_message',
		err_mess: false
    };

    $.widget('gbc.gbc_discount_coupons' , {
        options: gbc_discount_coupons_options,

        _create: function() {
			this.init_promocode();
        },

		init_promocode : function()
		{
			var $this = this;

			var submit_but = this.element.find(this.options.activate_button_id);
			var err_mess = this.options.err_mess;
			this._on(submit_but, {
				'click' : function(){
					var code = $this.element.find($this.options.promocode_field_id);

					var reg = /\d/;
					var promocode = code.val();
					if(reg.test(promocode) && promocode.length == 16) {
						console.log(promocode);
						jQuery.ajaxAG({
							url: $this.options.url,
							type: "POST",
							data: {code: promocode},
							dataType : 'json',
							success: function(d)
							{
								console.log('success');
								if(d.success == 1) {
									console.log('success');
									$('div.discount_coupons_form').append(d.html);
									var $delailLinks = $(".discount_coupons_form").find('.btn-detail');
									$delailLinks.each(function() {
										$(this).attr('target', 'blank');
									});
								} else
								if(d.success == 0)
								{
									console.log('error');
									$this.show_activate_errors(d.mess);
								}
							}
						});
					} else {
						this.show_activate_errors(err_mess);
					}
				}
			});
		},

		show_activate_errors : function(errors)
		{
			var mess_block = this.element.find(this.options.activate_message_block_id);
			var err_block = this.element.find('.activate_item_block').find(this.options.activate_message_block_id).find(this.options.error_block_id);
			err_block.find('div').html(errors);
			err_block.show();
			mess_block.show();
			setTimeout(function() {
				err_block.hide();
				mess_block.hide();
			}, 4000);
		},

		show_activate_success : function(success)
		{
			var mess_block = this.element.find(this.options.activate_message_block_id);
			var success_block = this.element.find('.activate_item_block').find(this.options.activate_message_block_id).find(this.options.success_block_id);
			success_block.find('div').html(success);
			success_block.show();
			mess_block.show();
			setTimeout(function() {
				success_block.hide();
				mess_block.hide();
			}, 4000);
		}
    });
})(jQuery);