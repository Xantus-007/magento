/**
 * jquery.dbm.productSelect.js
 * contain productSelect js
 */
var productSelect;

(function($) {
    productSelect = {
        init: function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.js-product__select--handler').on('click.dbm', function(ev) {
                ev.preventDefault();
                self._contentToggle($(this), 'open');
                self._scrollToTop($(this));
            });

            $('.c-closed__product__select').on('click.dbm', function(ev) {
                ev.preventDefault();
                self._contentToggle($(this), 'close');
            });
        },

        _contentToggle: function($el, action) {
            switch(action) {
                case 'open' :
                    $('.c-product__select').addClass('is-open');
                    //$('body').addClass('is-product__selecting');
                    break;
                case 'close' :
                    $('.c-product__select').removeClass('is-open');
                    //$('body').removeClass('is-product__selecting');
                    break;
                default :
                    return false;
            }
        },

        _scrollToTop: function($el) {
            $el.dbmScrollto({
                scrollTarget: $('body'),
                scrollSpeed: 400
            });
        }
    };

    $(document).ready(function() {
        productSelect.init();
    });
})(jQuery);
