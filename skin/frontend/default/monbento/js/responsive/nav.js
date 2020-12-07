jQuery(document).ready(function($){
    
    
    // Main navigation
    var $nav             = $('#navigation'),
        $collapseNavBtn  = $('#collapse-menu');
 
    $collapseNavBtn.on('click', function(e) {       
        toggleNav();
        e.stopPropagation();
    });
    
    $nav.on('click, tap', function(e){
       e.stopPropagation();
    });
                
    $(document).on('click, tap', function(e){
        if ($nav.is('.open')) {
            e.preventDefault();
            toggleNav();
        }
    });
    
    // Shop submenu
    var $toggleLinks   = $('.shopLink, .about-link', $nav);       
    
    $(window).on('resize', function(){
        if (!window.matchMedia("(max-width: 40em)").matches) {  
            $nav.removeClass('open');
            $('body').removeClass('open');
            $collapseNavBtn.removeClass('active');
            $('.nav-container-newMenu', $nav).hide();
        }
    });
    
    $toggleLinks.on('click', function(){
        if ($('body').is('.open')) {
            var goToLink = false;

            if ($(this).is('.about-link') && !$('body').is('.contenu')) {
                goToLink = true;
            }

            if (goToLink == false) {
                $(this).toggleClass('active');
                
                $('.nav-container-newMenu:visible').each(function(){
                    $(this).slideUp();
                });

                var $subMenu = $(this).next('.nav-container-newMenu:hidden');

                if (window.matchMedia("(max-width: 40em)").matches) {
                    $subMenu.slideDown();            
                }

                return false;
            }
        }
    });
    
    function toggleNav() {
        $nav.toggleClass('open');
        $('body').toggleClass('open');
        $collapseNavBtn.toggleClass('active');
    }
    
    function openNav() {
        $nav.addClass('open');
        $('body').addClass('open');
        $collapseNavBtn.addClass('active');
    }
    
    function closeNav() {
        $nav.removeClass('open');
        $('body').removeClass('open');
        $collapseNavBtn.removeClass('active');
    }
});