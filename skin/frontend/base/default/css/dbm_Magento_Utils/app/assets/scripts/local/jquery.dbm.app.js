/**
 * jquery.dbm.app.js
 * contain global project js
 */
var app;

(function($) {
    app = {
        init : function() {
            this._equalizer();
            this._responsiveTable();
        },

        _equalizer : function() {            
            $('.c-sponsorship').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true        
            });

            $('.box-account, .auguria-sponsorship-points-accumulated').itemEqualizer({
                item: '.js-itemEqualizer'  
            });

            $('#form-validate').itemEqualizer({
                item: '.fieldset'
            });
        },

        _equalizerWithImg : function() {
            $('.c-wishlist').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });
        },

        _responsiveTable: function() {
            $('.data-table', '.c-account').wrap('<div class="c-overflow__table"/>');
        },
    };

    $(document).ready(function() {
        app.init();
    });

    $('.c-wishlist').imagesLoaded(function() {
        app._equalizerWithImg();
    });

})(jQuery);
