$('.JQ_products_view').live('click', function()
{
var JQ_products_overlay = $('#JQ_products_overlay').overlay({api: true, top: '0', oneInstance : false});
jQuery.ajaxAG(
	{
		url: $(this).attr('href'),
		type: "GET",
		data: {},
		success: function(d)
		{
			$('#JQ_products_overlay #content').html(d);
			JQ_products_overlay.load();
		}
	}
);
return false;
});