jQuery(document).ready(function($){
    // Responsive video player
    if ($('iframe, object', '.cms-page').length > 0) {
        
        $('iframe, object', '.cms-page').each(function(){
            var $iframe = $(this);
            
            $iframe.wrap('<div class="video-container"></div>');            
            $('.video-container').fitVids();
        });               
    }
});