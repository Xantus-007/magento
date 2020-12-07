/**
 * jquery.dbm.offCanvas.js
 * contain offCanvas js
 */
var offCanvas;

(function($) {
    offCanvas = {
        /**
        * Initialize variables
        */
        // icon menu
        $iconHolder: $('.c-nav__icon'),
        // offcanvas
        $menuHolder: $('.o-nav'),
        // overlay for closure
        $closeOffcanvas: $('.c-nav__close'),
        // wrapper offset
        $wrapper : $('body'),
        // scrollTop for fixed nav
        scrollTop : $(window).scrollTop(),
        scrollTopSave : undefined,

        /**
        * Initialize event
        */
        CLOSE_OFFCANVAS : 'CLOSE_OFFCANVAS',

        /**
        * [init] constructor
        */
        init: function() {
            this._eventHolder();
            this._eventTrigger();
            this._updateScrollTop();
        },

        /**
        * Catch click event
        */
        _eventHolder: function() {
            var self = this;

            // click on burger icon
            $('.c-nav__icon').on('click', function(ev) {
                ev.preventDefault();
                if($(ev.target).hasClass('is-active'))
                {
                    self._closeOffCanvas();
                }
                else
                {
                    self._openOffCanvas();
                }
            });

            // bind CLOSE_OFFCANVAS to document
            $(document).on('CLOSE_OFFCANVAS', function() {
                self._closeOffCanvas();
            });
        },

        /**
        * Trigger custom event
        */
        _eventTrigger: function() {
            $('.c-nav__close').on('click', function(ev) {
                ev.preventDefault();
                $(document).trigger('CLOSE_OFFCANVAS');
            });
        },

        /**
        * Hide / show offcanvas
        */
        _openOffCanvas: function($el) {
            // save scrollTop
            this.scrollTopSave = $(window).scrollTop();

            // swap burger icon <-> cross icon
            $('.c-nav__icon').toggleClass('is-active');

            // display / hide offcanvas
            $('.o-nav').toggleClass('is-inViewport');
            $('body').toggleClass('is-fixed');

            // display / hide $('.c-nav__close')
            $('.c-nav__close').toggleClass('is-active');

            // udpade $('body') position
            this._updateDomElmntPosition();
        },

        /**
        * listen to CLOSE_OFFCANVAS, for pragmatic closure
        */
        _closeOffCanvas: function() {
            // swap cross icon -> burger icon
            $('.c-nav__icon').removeClass('is-active');

            // hide offcanvas
            $('.o-nav').removeClass('is-inViewport');
            $('body').removeClass('is-fixed');

            // hide $('.c-nav__close')
            $('.c-nav__close').removeClass('is-active');

            // release body position
            $('body, .c-nav').css({
                top : 0
            });

            $(window).scrollTop(this.scrollTopSave);
        },

        _updateScrollTop: function() {
            var self = this;
            var resizeTimer;

            $(window).on('scroll', function(){
                // lock function during scroll handler
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    self.scrollTop = $(window).scrollTop();
                }, 250);
            });
        },

        _updateDomElmntPosition: function() {
            var self = this;

            $('body').css({
                'top' : -1*self.scrollTop
            });
        },
    };

    $(document).ready(function() {
        offCanvas.init();
    });
})(jQuery);
