/**
 * jquery.dbm.app.js
 * contain global project js
 */
var b2b;

(function($) {
    b2b = {
        init : function() {
            this._enquire();
            this._youtubeMfp();
        },

        initImagesLoaded: function() {
            var self = this;

            setTimeout(function() {
                self._itemEqualizer();
            }, 250);
        },

        _itemEqualizer: function() {
            $('.js-equalizer__wrapper').itemEqualizer({
                item : '.js-equalizer__item',
                outerHeight : true
            });

            $('.js-equalizer__post').itemEqualizer({
                item : '.js-equalizer__postItem',
                outerHeight : true
            });
        },

        _enquire : function() {
            enquire.register("screen and (max-width: 768px)", {
                match : function() {
                    $('[class*="c-button"]', '.c-b2b__registerCol').insertAfter($('[class*="c-button"]', '.c-form__login'));
                },
                unmatch : function() {
                    $('[class*="c-button--white"]', '.c-b2b__loginCol').insertAfter($('.c-b2b__list', '.c-b2b__registerCol'));
                }
            });

            //$('.c-businessCase__wrapper', '.c-businessCase');
            //$('.c-post__wrapper', '.c-post__list');
            enquire.register("screen and (max-width: 425px)", {
                match : function() {
                    $('.c-businessCase__wrapper', '.c-businessCase').each(function(i, elem) {
                        var $post = $('.c-post__wrapper:eq(' + i + ') > .c-post', '.c-post__list');
                        $post.appendTo($(elem));
                    });
                },
                unmatch : function() {
                    $('.c-businessCase__wrapper', '.c-businessCase').each(function(i, elem) {
                        var $post = $(elem).find('.c-post');
                        var $postHolder = $('.c-post__wrapper:eq(' + i + ')');

                        $post.appendTo($postHolder);

                    });
                }
            });
        },

        _youtubeMfp : function() {
            $('.youtube-mfp').magnificPopup({
                type: 'iframe',
                iframe: {
                    patterns: {
                        youtube: {
                            index: 'youtu.be/',
                            id: '/',
                            src: '//www.youtube.com/embed/%id%?autoplay=1'
                        }
                    },
                    srcAction: 'iframe_src'
                }
            });
        }
    };

    // image loaded
    $('body').imagesLoaded(function(){
        b2b.initImagesLoaded();
    });

    $(document).ready(function() {
        b2b.init();
        if($('.switch_link.c-button--transparentNoHover--arrowRightToRight').hasClass('no-lang-select')) {
            $('.switch_link.c-button--transparentNoHover--arrowRightToRight').trigger('click');
        }            
    });
})(jQuery);
