/**
 * jquery.dbm.app.js
 * contain global project js
 */
var app;

(function ($) {
    app = {
        init: function () {
            this._foundationJs();
            this._slick();
            this._waypointJs();
            this._enquireJs();
            this._requiredWrapper();
            this._magnificPopup();
            this._responsiveTable();
            this._file();
            this._scrollTo();
        },

        initImagesLoaded: function () {
            this._bxSlider();
            this._itemEqualizer();
        },

        _foundationJs: function () {
            $(document).foundation();
        },

        _formValidator: function () {
            $.validate();
        },

        _bxSlider: function () {
            // home
            $('.c-slider--pageHeader .js-bxslider').bxSlider({
                pagerCustom: $('.c-bxslider--pager'),
                auto: true,
                pause: 7000,
                autoHover: true,
                speed: 600,
                controls: false,
                adaptiveHeight: true,
                easing: 'cubic-bezier(.7, 0, .175, 1)',
                onSliderLoad: function () {
                    $(".c-slider--pageHeader").find('.js-bxslider > div:not(.bx-clone)').show();
                    $('.c-bxslider--pager').show();
                    $(".c-slider--pageHeader").hide().fadeIn(2000);
                }
            });

            // stop pager
            // $('.c-slider--pageHeader .bx-wrapper').on('mouseenter mouseleave', function(ev) {
            //     $('.c-slider--pageHeader .c-bxslider--pager').toggleClass('is-paused');
            // });

            // page product - product thumb
            if($(window).width() < 768) {
                var p = false;
            }else {
                var p = true;
            }
            $('.c-slider--product .js-bxslider').bxSlider({
                pagerCustom: $('.js-bxslider__controler'),
                pager: p,
                minSlides: 1,
                maxSlides: 1,
                mode: 'fade',
                auto: false,
                pause: 7000,
                controls: false,
                infiniteLoop: true,
                easing: 'cubic-bezier(.7, 0, .175, 1)',
            });

            // page product - product presentation
            $('.c-slider--productPresentation .js-bxslider').bxSlider({
                mode: 'fade',
                pagerCustom: $('.c-bxslider--pager'),
                auto: true,
                pause: 7000,
                speed: 600,
                controls: false,
                easing: 'cubic-bezier(.7, 0, .175, 1)',
            });

            // page product - decli selection
            $('.c-slider__productSelect').bxSlider({
                auto: false,
                speed: 600,
                pager: false,
                infiniteLoop: false,
                hideControlOnEnd: true,
                touchEnabled: false,
                easing: 'cubic-bezier(.7, 0, .175, 1)',
            });

            // page custom
            $('.c-page__custom__slider .js-bxslider').bxSlider({
                auto: true,
                pause: 4000,
                speed: 600,
                pager: false,
                infiniteLoop: true,
                hideControlOnEnd: true,
                touchEnabled: false,
                controls: false,
                easing: 'cubic-bezier(.7, 0, .175, 1)',
            });
        },

        _slick: function () {
            $('.c-carousel--product .js-slick').slick({
                centerMode: true,
                infinite: true,
                slidesToShow: 3,
                initialSlide: 3,
                variableWidth: true
            });

            $('.c-gallery__nav .js-slick').slick({
                slidesToShow: 4,
                infinite: false,
                responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3
                    }
                }]
            });

            // slick product custom
            enquire.register("screen and (max-width: 580px)", {
                match: function () {
                    $('.product-image.js-slick').slick({
                        slidesToShow: 1,
                        dots: true,
                        arrows: false
                    });
                },
                unmatch: function () {
                    $('.product-image.js-slick').slick('unslick');
                }
            });

        },

        _itemEqualizer: function () {
            // testimonial
            $('.c-testimonial--home').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // testimonial
            $('.c-testimonial--about').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // news
            $('.c-news__list').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // footer
            $('.c-footer--sub').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // Product presentation
            $('.c-product__presentation').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // Product personalisation
            $('.c-product__personalize').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            $('.c-row__personnalize').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // Grid product
            $('.js-grid__product').itemEqualizer({
                item: '.js-itemEqualizer--holder',
                outerHeight: true
            });

            $('.c-grid__product').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            $('.c-row__product').itemEqualizer({
                item: '.c-priceBox',
                outerHeight: true
            });

            // Business case
            $('.c-businessCase').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // Gift card
            $('.c-product__giftCard').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // Login
            $('[class*="c-container__white"]').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            // Checkout
            $('.js-list__radio').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            $('.js-products__about').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            $('.c-list__awards').itemEqualizer({
                item: '.js-awards__equalize',
                outerHeight: true
            });

            $('.c-section__about--customisation').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            $('.c-section__about--history').itemEqualizer({
                item: '.js-history__equalize',
                outerHeight: true
            });

            $('.c-press__table').itemEqualizer({
                item: '.js-press__equalize',
                outerHeight: true
            });

            $('.c-item__team').itemEqualizer({
                item: '.js-team__equalizer',
                outerHeight: true
            });

            $('.js-recruitment').itemEqualizer({
                item: '.js-recruitment__equalizer',
                outerHeight: true
            });

            $('.js-progress__list').itemEqualizer({
                item: '.js-progress__equalizer',
                outerHeight: true
            });

            // Stat blocks
            $('.js-stat__number--holder').itemEqualizer({
                item: '.js-stat__number',
                outerHeight: true
            });

            $('.js-stat__description--holder').itemEqualizer({
                item: '.js-stat__description',
                outerHeight: true
            });

            $('.js-stat__smallText--holder').itemEqualizer({
                item: '.js-stat__smallText',
                outerHeight: true
            });

            $('.js-stat__block--holder').itemEqualizer({
                item: '.js-stat__block',
                outerHeight: true
            });

            // Environment
            $('.js-environment').itemEqualizer({
                item: '.js-environment__equalizer',
                outerHeight: true
            });

            // FAQ
            $('.c-faq__contact__blocks').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });
        },

        _waypointJs: function () {
            // sticky nav
            if ($('.c-stickyNav')[0]) {
                var stickyTopline = new Waypoint.Sticky({
                    element: $('.c-stickyNav')[0],
                    offset: 55
                });
            }

            // waypoint nav -> is-small
            if (!$('body').hasClass('is-kiosk')) {
                $('body').waypoint({
                    handler: function () {
                        $('.c-stickyNav').toggleClass('is-small');
                    },
                    offset: -20
                });
            }

            // waypoint sticky product -> is-visible
            // waypoint sticky nav -> !is-visible
            $('.c-product__controls').waypoint({
                handler: function () {
                    $('.c-sticky__product').toggleClass('is-visible');
                    $('.c-stickyNav').toggleClass('is-hide');
                },
                offset: '140px'
            });
        },

        _enquireJs: function () {
            // change priceBox position
            var $html_src = $('.c-product__controls'),
                $html_desc = $('.c-slider--product'),
                $html = $('.c-gallery__nav');

            enquire.register("screen and (max-width: 768px)", {
                match: function () {
                    $html.appendTo($html_desc);
                },
                unmatch: function () {
                    $html.prependTo($html_src);
                }
            });

            // change colorSelector position
            var $html2_src = $('.c-row__personnalize .c-product__action'),
                $html2 = $('.c-row__personnalize .c-colorSelector');

            enquire.register("screen and (max-width: 580px)", {
                match: function () {
                    $html2.insertBefore($html2_src);
                },
                unmatch: function () {
                    $html2.insertAfter($html2_src);
                }
            });

            // change colorSwitcher position
            var $colorSwitcher = $('.c-color__switcher__toggle'),
                $colorDest = $('.c-slider--product'),
                $colorSrc = $('.c-product__controls');

            enquire.register("screen and (max-width: 640px)", {
                match: function () {
                    $colorSwitcher.appendTo($colorDest);
                },
                unmatch: function () {
                    $colorSwitcher.prependTo($colorSrc);
                }
            });

            enquire.register("screen and (max-width: 425px)", {
                match: function () {
                    $('.c-giftCard__preview').removeClass('js-itemEqualizer');
                    $('.c-giftCard__preview').removeAttr('style');
                },
                unmatch: function () {
                    $('.c-giftCard__preview').addClass('js-itemEqualizer');
                }
            });

            enquire.register("screen and (max-width: 1024px)", {
                match: function () {
                    $('.c-mozaic').imagesLoaded(function () {
                        $('.c-mozaic').itemEqualizer({
                            item: '.js-mozaic__equalizer',
                            outerHeight: true
                        });
                    });
                },
                unmatch: function () {

                }
            });
        },

        _requiredWrapper: function () {
            $.each($('.buttons-set .required'), function () {
                var $html = $(this).html(),
                    newHtml = '<em>*</em>' + $html.slice(1);

                $(this).html(newHtml);
            });
        },

        _magnificPopup: function () {
            $('body').on('click', '.popup-modal', function () {
                var src = $(this).attr('href');
                $.magnificPopup.open({
                    items: {
                        src: $(src),
                        type: 'inline'
                    }
                });
            });
        },

        _responsiveTable: function () {
            $('table', '.c-wysiwyg').wrap('<div class="c-table__wrapper"></div>');
        },

        _file: function () {
            // print file value inside input text
            $('input[type="file"]').each(function () {
                $(this).on('change', function () {
                    var label = $(this).val();
                    var result = label.replace('C:\\fakepath\\', '');

                    if (result.length > 35) {
                        result = result.substr(0, 35) + '...';
                    }

                    $(this).parents('.c-input').find('.c-input--wrapper input').val(result);

                    if (result != '') {
                        $(this).not('.not-focus').parents('.c-input__holder').addClass('is-focus');
                    } else {
                        $(this).not('.not-focus').parents('.c-input__holder').removeClass('is-focus');
                    }
                });
            });

            // simulate click on input text
            $('.c-input--wrapper input').on('click.dbm', function (ev) {
                $(this).parents('.c-input').find('input[type="file"]').click();
            });

            // $("#contactForm .is-file input[type='file']").uniform({
            //     fileDefaultText: 'example'
            // });

        },

        _scrollTo: function () {
            $('.js-scrollTo').on('click.dbm', function (ev) {
                ev.preventDefault();

                var target = $(this).attr('href');

                $(this).dbmScrollto({
                    scrollTarget: $(target),
                    scrollSpeed: 400
                });
            });
        }
    };

    //dbm_country_switch_country_fr
    //dbm_country_switch_language_fr

    // image loaded
    $('body').imagesLoaded(function () {
        app.initImagesLoaded();
    });

    $(document).ready(function () {
        app.init();
    });
})(jQuery);
