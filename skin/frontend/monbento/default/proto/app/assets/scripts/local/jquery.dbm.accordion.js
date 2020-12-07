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
     * jquery.dbm.accordion.js
     */
    var dbmAccordion;

    dbmAccordion = {
        init : function() {
            this._eventsHandler();
        },

        _eventsHandler : function() {
            var self = this;


            $('.js-accordion__item').on('click', function(e){
                e.preventDefault();

                self._toggleAccordion($(this));
            });
        },

        _toggleAccordion : function($toggle) {
            $toggle.toggleClass('is-open');
            $toggle.siblings().removeClass('is-open');
            // Slide up others items
            $toggle.parent().parent().siblings().find('.js-accordion__item').removeClass('is-open');

            // if($link.parent().hasClass('is-open')) {
            //     $link.dbmScrollto({
            //         scrollTarget: $link,
            //         scrollSpeed: 500,
            //         scrollOffset : - $('.c-header').height()
            //     });
            // }
        }
    };

    $(document).ready(function() {
        dbmAccordion.init();
    });
}));
