/*!
 * (v) hrefID jQuery extention
 * returns a valid #hash string from link href attribute in Internet Explorer
 */
(function($){$.fn.extend({hrefId:function(){return $(this).attr('href').substr($(this).attr('href').indexOf('#'));}});})(jQuery);

/*!
 * Scripts
 *
 */
jQuery(function($) {
	var Engine = {
		ui : {
			showcase : function() {
				$showcase 	= $('div.showcase-a');
				var time	= 750;		// transition time
				var cycle	= 5000;		// break time

				// mark activity
				$showcase.find('div.item').mouseenter(function() {
					$showcase.data('activity',1);
					if(!$(this).is('.active')) {
						$(this).addClass('active').siblings().removeClass('active').find('p.photo').hide();
						$(this).find('p.photo').show();
					}
				}).mouseleave(function() {
					$showcase.removeData('activity');
				});

				// link whole box
				$showcase.find('div.item').each(function() {
					var $link = $(this).find('p.photo a');
					if($link.size() > 0){
						$(this).addClass('linked');
					}
				});

				// switch
				showcaseChange = function($next) {
					if($showcase.find('div.item p.photo:animated').size() > 0) return false;
					var $current = $showcase.find('div.item.active');
					if(typeof arguments[0] == 'undefined'){
						if($showcase.data('activity') == 1) return false;
						var $next = ($current.next('div.item').size() < 1) ? $showcase.find('div.item:first') : $current.next('div.item');
					}
					$current.find('p.photo').fadeOut(time,function(){
						$current.removeClass('active');
						$next.addClass('active');
						$next.find('p.photo').fadeIn(time);
					});
				};

				// cycle
				var cycle = setInterval('showcaseChange()',(cycle+2*time));

			},
			tooltip : function(){
				xOffset = -10;
				yOffset = 10;
				tipwidth = 163;
				$("#qualities li").hover(function(e){
					this.alt = $(this).find('img').attr('title');
					$(this).find('img').attr('title', '');
					if (this.alt && this.alt!='') {
						$("body").append("<div id='tooltip'><div>"+ this.alt +"</div></div>");
						if (e.pageX+tipwidth>$(window).width()) {
							xOffsetAdapted = - xOffset - tipwidth;
							$("#tooltip").addClass('reverse');
						} else {
							$("#tooltip").removeClass('reverse');
							xOffsetAdapted = xOffset;
						}
						$("#tooltip")
							.css("top",(e.pageY + yOffset) + "px")
							.css("left",(e.pageX + xOffsetAdapted) + "px")
							.fadeIn("fast");
					}
				},
				function(){
					$(this).find('img').attr('title',this.alt);
					$("#tooltip").remove();
				});
				$("#qualities li").mousemove(function(e){
					if (e.pageX+tipwidth>$(window).width()) {
						xOffsetAdapted = - xOffset - tipwidth;
						$("#tooltip div").addClass('reverse');
					} else {
						xOffsetAdapted = xOffset;
						$("#tooltip div").removeClass('reverse');
					}
					$("#tooltip")
						.css("top",(e.pageY + yOffset) + "px")
						.css("left",(e.pageX + xOffsetAdapted) + "px");
				});
			},
			tabs : function() {
				$('div.tabs-a').each(function() {
					$(this).find('div.tabs ul li a').click(function() {
						if($(this).is('.active')) return false;
						$(this).addClass('active').parent().siblings().find('a.active').removeClass('active');
						$($(this).hrefId()).show().siblings().filter('.panel').hide();
						return false;
					});
				});
			},
			navigation : function() {
				$('.subnav .cms-block').each(function() {
					$(this).parent().css("width",$(this).parent().outerWidth()+185);
				});
				$('#nav > li').mouseover(function(e){
					var position = $(this).position();
					var subnav = $(this).find('.subnav');
					if (subnav.outerWidth()+position.left>994) {
						subnav.css("left",994-subnav.outerWidth()-position.left + "px");
					} else {
						subnav.css("left","-45px");
					}
				});
				$('#nav > li').mouseout(function(e){
					$(this).find('.subnav').css("left","-5000em");
				});
			},
			cart : function() {
				$('.cart .cart-table').each(function() {
					var $root = $(this);

					$root.find('.c4 > a').click(function() {
						var input = $(this).closest('td').find('input');
						var qty = input.val();

						if(qty != parseInt(qty)) qty = 1;

						if($(this).is('.plus')) qty++;
						if($(this).is('.minus')) qty--;

						if(qty < 1) qty = 0;

						input.val(qty);
						$(this).closest('form').submit();
						return false;
					});

					$root.find('input.qty').keyup(function() {
						var value = $(this).val();
						if(value != parseInt(value) && value != '') $(this).val(value.replace(/\D/g,''));
						$(this).closest('form').submit();
					});
				});
			}
		}
	};
	//Engine.ui.showcase();
	Engine.ui.tooltip();
	//Engine.ui.tabs();
	//Engine.ui.cart();
	//Engine.ui.navigation();
});

jQuery(document).ready(function($){
    /*if ( !(/(iPad|iPhone|iPod).*OS [5-6].*AppleWebKit.*Mobile.*Safari/.test(navigator.userAgent)) ) {
        if($.smartbanner) {
            $.smartbanner({
                title: 'monbento',
                author: 'monbento',
                price: Translator.translate('FREE'),
                inGooglePlay : Translator.translate('In Google Play'),
                button:  Translator.translate('VIEW'),
                icon : 'http://www.monbento.com/apps/android/logo.webp',
                daysHidden: 0,
                daysReminder: 0
            });
        }
    }*/
    
    //if ( !navigator.userAgent.match(/iPhone/i) && !navigator.userAgent.match(/iPad/i) ) {
    if($.smartbanner) {
        $.smartbanner({
            title: 'Monbento', // What the title of the app should be in the banner (defaults to <title>)
            author: 'monbento', // What the author of the app should be in the banner (defaults to <meta name="author"> or hostname)
            price: Translator.translate('FREE'), // Price of the app
            inAppStore: Translator.translate('On the App Store'), // Text of price for iOS
            inGooglePlay: Translator.translate('In Google Play'), // Text of price for Android
            GooglePlayParams: null, // Aditional parameters for the market
            icon: 'http://www.monbento.com/apps/android/logo.webp', // The URL of the icon (defaults to <meta name="apple-touch-icon">)
            iconGloss: null, // Force gloss effect for iOS even for precomposed
            button: Translator.translate('VIEW'), // Text for the install button
            scale: 'auto', // Scale based on viewport size (set to 1 to disable)
            speedIn: 300, // Show animation speed of the banner
            speedOut: 400, // Close animation speed of the banner
            daysHidden: 0, // Duration to hide the banner after being closed (0 = always show banner)
            daysReminder: 0, // Duration to hide the banner after "VIEW" is clicked *separate from when the close button is clicked* (0 = always show banner)
            force: null, // Choose 'ios', 'android' or 'windows'. Don't do a browser check, just always show this banner
            layer: true,
            hideOnInstall: true, // Hide the banner after "VIEW" is clicked.
            iOSUniversalApp: true, // If the iOS App is a universal app for both iPad and iPhone, display Smart Banner to iPad users, too.      
            appendToSelector: 'body' //Append the banner to a specific selector         
        });
    }
    //}        
});

jQuery.fn.infiniteCarousel = function () {

    function repeat(str, num) {
        return new Array( num + 1 ).join( str );
    }
  
    return this.each(function () {
        var $wrapper = jQuery('> div', this).css('overflow', 'hidden'),
            $slider = $wrapper.find('> ul'),
            $items = $slider.find('> li'),
            $single = $items.filter(':first'),
            
            singleWidth = $single.outerWidth(), 
            visible = Math.ceil($wrapper.innerWidth() / singleWidth), // note: doesnt include padding or border
            currentPage = 1,
            pages = Math.ceil($items.length / visible);            


        // 1. Pad so that 'visible' number will always be seen, otherwise create empty items
        if (($items.length % visible) != 0) {
            $slider.append(repeat('<li class="empty" />', visible - ($items.length % visible)));
            $items = $slider.find('> li');
        }

        // 2. Top and tail the list with 'visible' number of items, top has the last section, and tail has the first
        $items.filter(':first').before($items.slice(- visible).clone().addClass('cloned'));
        $items.filter(':last').after($items.slice(0, visible).clone().addClass('cloned'));
        $items = $slider.find('> li'); // reselect
        
        // 3. Set the left position to the first 'real' item
        $wrapper.scrollLeft(singleWidth * visible);
        
        // 4. paging function
        function gotoPage(page) {
            var dir = page < currentPage ? -1 : 1,
                n = Math.abs(currentPage - page),
                left = singleWidth * dir * visible * n;
            
            $wrapper.filter(':not(:animated)').animate({
                scrollLeft : '+=' + left
            }, 500, function () {
                if (page == 0) {
                    $wrapper.scrollLeft(singleWidth * visible * pages);
                    page = pages;
                } else if (page > pages) {
                    $wrapper.scrollLeft(singleWidth * visible);
                    // reset back to start position
                    page = 1;
                } 

                currentPage = page;
            });                
            
            return false;
        }
        
        $wrapper.after('<a class="arrow back">&lt;</a><a class="arrow forward">&gt;</a>');
        
        // 5. Bind to the forward and back buttons
        jQuery('a.back', this).click(function () {
            return gotoPage(currentPage - 1);                
        });
        
        jQuery('a.forward', this).click(function () {
            return gotoPage(currentPage + 1);
        });
        
        // create a public interface to move to a specific page
        jQuery(this).bind('goto', function (event, page) {
            gotoPage(page);
        });
    });  
};

jQuery(function($) {
    if($('.line-6').size())
    {
        $('.line-6 a').mouseenter(function(){
            var $this = $(this);
            var $image = $this.find('img:nth-child(2)');
            
            $image.stop().animate({opacity:0}, 'fast');
        });
        
        $('.line-6 a').mouseleave(function(){
            var $this = $(this);
            var $image = $this.find('img:nth-child(2)');
            
            $image.stop().animate({opacity:1}, 'fast');
        })
    }
        
    if($('#listCatShop').size())
    {
        $(".linkCat").hover(function(){
            $(this).find("img").animate({ opacity: 0 }, 'fast');
        }, function() {
            $(this).find("img").animate({ opacity: 1 }, 'fast');
        });
    }
    
    if($('#qtyMobile').size())
    {
        $("#qtyMobile").change(function() {
            var selectedIndex = $(this).val();
            $("#qty").val(selectedIndex).prop('selected', true);
        });
    }
    
    if($('.dbm_catalog_gourmet.overlay').size()){
        $('.dbm_catalog_gourmet.overlay').fadeIn();
        $('.dbm_catalog_gourmet.popup').fadeIn();
    }
    
    $('.product-gourmet').click(function(){
        $('.dbm_catalog_gourmet.overlay').fadeIn();
        $('.dbm_catalog_gourmet.popup').fadeIn();
    });
    
    $('.dbm_catalog_gourmet.popup .close a').live('click', function(){
        $('.dbm_catalog_gourmet.overlay').fadeOut('fast');
        $('.dbm_catalog_gourmet.popup').fadeOut('fast');
    });
    
    function supportsSVG() {
        return !! document.createElementNS && !! document.createElementNS('http://www.w3.org/2000/svg','svg').createSVGRect;  
    }

    if (supportsSVG()) {
        document.documentElement.className += ' svg';
    } else {
        document.documentElement.className += ' no-svg';  
    }
});