/**
 * jquery.dbm.header.js
 * contain header js
 */
var header;

(function($) {
    header = {
        init : function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.c-search__toggle').on('click.dbm', function(ev) {
                self._formSearchToggle(ev, $(this));
            });

            $('.c-search .c-close').on('click.dbm', function(ev) {
                self._formSearchClose(ev, $(this));
            });
        },

        _formSearchToggle: function(ev, $el) {
            ev.preventDefault();
            $('.c-search').toggleClass('is-visible');
        },

        _formSearchClose: function(ev, $el) {
            ev.preventDefault();
            $('.c-search').removeClass('is-visible');
        },
    };

    $(document).ready(function() {
        header.init();
    });
})(jQuery);
