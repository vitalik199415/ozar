(function($) {
	$.fn.gbc_customers_add = function()
	{
		var OBJ = this;
		
		$(this).find('#same_as_billing_checkbox').live('change', function()
		{
			if($(this).is(':checked'))
			{
				$.each($(OBJ).find('#customer_address_b_fieldset').find('input[type=text], select'), function(i)
				{
					$(OBJ).find('#customer_address_s_fieldset').find('input[type=text], select').eq(i).val($(this).val());
				});
			}
		})
	}
})(jQuery);