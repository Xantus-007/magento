// Uses CommonJS, AMD or browser globals to create a jQuery plugin.
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = function( root, jQuery ) {
            if ( jQuery === undefined ) {
                // require('jQuery') returns a factory that requires window to
                // build a jQuery instance, we normalize how we use modules
                // that require this pattern but the window provided is a noop
                // if it's defined (how jquery works)
                if ( typeof window !== 'undefined' ) {
                    jQuery = require('jquery');
                }
                else {
                    jQuery = require('jquery')(root);
                }
            }
            factory(jQuery);
            return jQuery;
        };
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
/**
 * jquery.dbm.carouselHome.js
 * contain carousel home project js
 */
var dbmCarouselHome;

    dbmCarouselHome = {
        videoID: 'js-video--yt__video',
        sliderHome : '.js-slider--home',

        init: function () {
            this._sliderHome();
            this._eventHandlers();
        },

        _eventHandlers : function() {
            var self = this;

            // Video is loaded
            $('.js-fitvids--yt').on('dbm.youtube.loaded' , function() {
                $(this).closest('.js-fitvids--yt').addClass('is-loaded');

                // Hide Slider
                $(self.sliderHome).addClass('is-hidden');

                self.videoSandbox._play();
            });

            // End of video
            var close = function($this) {
                $this.closest('.js-fitvids--yt').removeClass('is-loaded');

                // Show Slider
                $(self.sliderHome).removeClass('is-hidden');
                $(self.sliderHome).slick('slickPlay');

                self.videoSandbox._destroy();
                self.videoSandbox = undefined;
            }

            $('.js-fitvids--yt').on('dbm.youtube.ended' , function() {
                close($(this));
            });

            $('.js-video__close').on('click' , function() {
                close($(this));
            });
        },

        _youtubePlayer : function(videoUrl, title, width, height) {
            this.videoSandbox = new DbmYoutubePlayer({
                videoID : this.videoID,
                fitVidsWrapper : 'js-fitvids--yt',
                videoProperties : {
                    url : videoUrl,
                    width: width,
                    height: height,
                    title: title,
                    controls: 0,
                    showinfo : 0,
                    autoplay: 0,
                },
                videoButton : false,
            });
        },

        _initSlider : function(node, options) {
            $(node).append('<div class="o-spinner"><div class="o-spinner--bounce1"></div><div class="o-spinner--bounce2"></div><div class="o-spinner--bounce3"></div></div>');

            imagesLoaded($(node), function(el) {
                $(node).find('.o-spinner').remove();
                $(node).slick(options);
            });
        },

        _sliderHome : function() {
            var self = this;

            this._initSlider(this.sliderHome, {
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                dots: true,
                infinite: true,
                autoplay: true,
                pauseOnHover: false,
                autoplaySpeed: $(this.sliderHome).attr('autoplay-speed'),
                responsive : [
                    {
                        breakpoint: 769,
                        settings: {
                            dots: false
                        }
                    }
                ],
            });

            // Init vidéo
            $('.c-slider--home__item[data-video] [class*="c-button"]').on('click', function(e) {
                e.preventDefault();
                var $item = $(this).closest('.c-slider--home__item');

                // Init Vidéo
                if(!$('.js-fitvids--yt').hasClass('is-loaded')) {
                    $(self.sliderHome).slick('slickPause');
                    self._youtubePlayer( $item.attr('data-video'), $item.find('.c-txt--01').text(),  $item.attr('data-width'), $item.attr('data-height') );
                }
            });

            // self._youtubePlayer( $('.c-slider--home__item[data-video]').attr('data-video'), $('.c-slider--home__item[data-video]').find('.c-txt--01').text() );
        }
    };

    $(window).load(function() {
        dbmCarouselHome.init();
    });
}));
