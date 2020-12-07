(function($) {
    $.fn.dbmScrollto = function(options){
        this.each(function(){
            settings = $.extend({
                scrollOrigin : $(this),
                scrollTarget : undefined,
                scrollOffset : 0,
                scrollSpeed  : 1000,
            }, options);

            settings.scrollOrigin.slideDown('200', function(){
                $('html, body').stop().animate({
                    scrollTop: (settings.scrollTarget.offset().top + settings.scrollOffset)
                }, settings.scrollSpeed, 'linear');
            });
        });
    }
})(jQuery);
