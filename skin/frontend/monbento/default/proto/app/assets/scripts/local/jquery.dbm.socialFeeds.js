/**
 * jquery.dbm.app.js
 * contain global project js
 */
var socialFeeds;

(function($) {
    socialFeeds = {
        init : function() {
            var base_url = (typeof(mobileUrl) != "undefined") ? mobileUrl : window.location.origin;
            
            try {
                jQuery.ajax({
                    url: base_url + '/monbento-site/index/getInstaBlock/',
                    dataType: 'json',
                    success: function(data){
                        if(data.status == "SUCCESS")
                        {
                            var $section = $(data.block);
                            if($('.c-wrapper__offcanvas').size() > 0) {
                                $section.insertBefore('footer');
                            }
                        }
                    }
                });
            } catch (e) {
                
            }
        },
    };

    $(document).ready(function() {
        socialFeeds.init();
    });

})(jQuery);
