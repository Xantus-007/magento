/**
 * jquery.dbm.gridProduct.js
 * contain gridProduct js
 */
var gridProduct;

(function($) {
    gridProduct = {
        init: function() {
            this._itemParser();
        },

        _itemParser: function() {
            var self = this;

            $.each($('.c-row__product'), function() {
                if(self._isMovable($(this)))
                {
                    self._enquireJs($(this), 640);
                }
            });
        },

        _isMovable: function($el) {
            if($el.hasClass('is-odd'))
            {
                return false;
            }
            else if($el.hasClass('is-even'))
            {
                return true;
            }
        },

        _enquireJs: function($el, breakpoint) {
            var $container = $el.find('.row'),
                $item = $el.find('.columns').first(),
                _breakpoint = breakpoint + 1;

            enquire.register("screen and (min-width: "+_breakpoint+"px)", {
                match : function() {
                    $item.appendTo($container);
                },
                unmatch : function() {
                    $item.prependTo($container);
                }
            });
        },
        
        _reinitGrid: function() {
            // Item equalizer Grid product
            $('.js-grid__product').imagesLoaded( function() {
                $('.c-product__item').removeAttr('style');
                $('.js-grid__product').itemEqualizer({
                    item: '.js-itemEqualizer--holder',
                    outerHeight: false
                });
                $('.js-grid__product').itemEqualizer('refreshAll');
                
                $('body').dbmScrollto({
                    scrollTarget: $('#catalog-listing'),
                    scrollOffset: -70
                });
            });
        }
    };

    $(document).ready(function() {
        gridProduct.init();
    });
})(jQuery);
