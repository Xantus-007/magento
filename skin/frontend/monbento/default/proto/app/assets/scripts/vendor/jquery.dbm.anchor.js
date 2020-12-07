/**
 * jquery.dbm.anchor.js
 * contain anchor js
 */
var anchor;

(function($) {
    anchor = {
        scrollOffset : false,

        init: function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.js-anchor').on('click.dbm', function(ev) {
                ev.preventDefault();
                self._scrollTo($(this));
            });
            $('.js-anchor--offset').on('click.dbm', function(ev) {
                ev.preventDefault();
                self.scrollOffset = $('.c-languageSwitcher').height() + $('.sticky-wrapper').height();
                self._scrollTo($(this));
            });
        },

        _scrollTo: function($el) {
            var target = this._getTarget($el);

            $el.dbmScrollto({
                scrollTarget: $(target),
                scrollSpeed: 400,
                scrollOffset: this.scrollOffset == false ? 0 : - this.scrollOffset
            });
        },

        _getTarget: function($el) {
            var target = $el.attr('href');

            return target;
        }
    };

    $(document).ready(function() {
        anchor.init();
    });
})(jQuery);
