/**
 * jquery.dbm.app.js
 * contain configurableDbm project js
 */
var configurableDbm;

(function($) {
    configurableDbm = {
        init : function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.super-attribute-select').on('change.dbm', function(ev) {
                self._updateCarousel($(this));
            });

            $('.c-gallery__nav .c-item').on('click.dbm', function(ev) {
                self._updateSelect($(this));
            });
        },

        _getOptionIndex: function($el) {
            var index = $el[0].selectedIndex;
            return index;
        },

        _getOptionValue: function($el) {
            var value = $el[0].value;
            return value;
        },

        _getCarouselItemIndex: function($el) {
            var index = $el.index();
            return index;
        },

        _updateCarousel: function($el) {
            var option = this._getOptionValue($el);
            $('.c-gallery__nav').find('.c-item[data-configurable-option="' + option + '"]').click();
        },

        _updateSelect: function($el) {
            var option = $el.attr('data-configurable-option');
            $('.super-attribute-select option[value="' + option + '"]').prop('selected', true);
        },
    };

    $(document).ready(function() {
        configurableDbm.init();
    });
})(jQuery);
