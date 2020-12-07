/**
 * jquery.dbm.app.js
 * contain global project js
 */
var appAccount;

(function($) {
    appAccount = {
        init : function() {
            this._equalizer();
            this._responsiveTable();
        },

        _equalizer : function() {
            $('.c-dashboard').itemEqualizer({
                item: '.box-account .box-content',
                outerHeight: true
            });

            $('.c-sponsorship, .addresses-list').itemEqualizer({
                item: '.js-itemEqualizer',
                outerHeight: true
            });

            $('.box-account, .auguria-sponsorship-points-accumulated').itemEqualizer({
                item: '.js-itemEqualizer'
            });

            // $('#form-validate').itemEqualizer({
            //     item: '.fieldset'
            // });
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
        appAccount.init();
    });

    $('.c-wishlist').imagesLoaded(function() {
        appAccount._equalizerWithImg();
    });

})(jQuery);
