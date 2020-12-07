var cookieExpiryDate = new Date();
cookieExpiryDate.setDate(cookieExpiryDate.getDate() + 60);
var mbtpagecookie = 'mbtpageviews_' + currentStoreId;
var mbtpageviews = parseInt(Mage.Cookies.get(mbtpagecookie)) || 1;
Mage.Cookies.set(mbtpagecookie, mbtpageviews+1, cookieExpiryDate);
(function($){
    $(document).ready(function(){
    	//console.log(mbtpagecookie + ':', mbtpageviews, incentiveMessagePage);
    	if(mbtpageviews == incentiveMessagePage && popin == '0'){
	    	$('#mbt-newsletter').show();
	        $.magnificPopup.open({
	            items: {
	                src: $('#mbt-newsletter'),
	                type: 'inline'
	            }
	        });
    	}
    })
})(jQuery);
