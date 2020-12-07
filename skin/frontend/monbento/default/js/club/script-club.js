/* INITIALISATION BXSLIDER CLUBENTO */
jQuery(document).ready(function($) {    

    var $menu = $('ul', '#clubMenuTop');

    enquire.register("screen and (max-width:40em)", {
        match: function() {
            $menu.owlCarousel({
            itemsMobile: [320,2],
            slideSpeed: 500,
            pagination: false,
                afterInit: function(carousel) {
                    if ($('li.selected', $menu).length > 0) {
                        var $currentSlide = $('li.selected', $menu);
                        var position = $currentSlide.closest('.owl-item').index();
                        
                        carousel.trigger('owl.jumpTo', position);
                    }
                }
            });
        },
        unmatch: function() {
            $menu.data('owlCarousel').destroy();
            $menu.removeClass('owl-carousel');
        }
    });
    
    if($('#clubMenuMain').length > 0) {
        var menu = $('#clubMenuMain');
        menu.on('click', function(){
           $(this).toggleClass('open');
        });
    }
});

/* INITIALISATION DE FITVIDS POUR LA PARTIE VIDEO DU BLOG */
jQuery(function($) {
	$(window).load(function() {
		$("body.club .clubPage iframe").wrap('<div class="video-container"></div>');
		$("body.club .postContent iframe").wrap('<div class="video-container"></div>');
		$("body.club .clubPage .video-container").fitVids();
		$("body.club .postContent .video-container").fitVids();
	});
});