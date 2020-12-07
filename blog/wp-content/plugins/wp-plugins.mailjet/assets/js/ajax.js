jQuery(document).ready(function($)
{
	$('.subscribe-form').submit(function(e)
	{
		e.preventDefault();
		var $el  = $(this);
		var loading = '<p><img src="'+WPMailjet.loadingImg+'" alt="Please wait..."></p>';// Loading state
		var $res = $el.parents(".WP_Mailjet_Subscribe_Widget").find(".response").html(loading);// Clear previous messages
		console.log($res);
		$.post(WPMailjet.ajaxurl, $el.serialize(), function(data)
		{
			$res.html(data);
		});
	})
	
	$('.widget-control-close').click(function(e)
	{
		var $res = $(this).closest('form').find(".mailjet_subscribe_response");
		if(jQuery.type($res.html()) !== undefined)
		{
			$res.hide();
		}
	})
});