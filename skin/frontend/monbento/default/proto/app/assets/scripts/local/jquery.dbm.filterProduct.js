/**
 * jquery.dbm.filterProduct.js
 * contain filterProduct js
 */
var filterProduct;

(function($) {
    filterProduct = {
        init: function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.c-filter__toogle').on('click.dbm', function(ev) {
                ev.preventDefault();
                self._toggleFilter($(this));
            });
        },

        _toggleFilter: function($el) {
            $el.toggleClass('is-open');
            $('.c-filter').stop().slideToggle(function() {
                $(this).removeAttr('style').toggleClass('is-open');
            });
        }
    };

    $(document).ready(function() {
        filterProduct.init();
    });
})(jQuery);
