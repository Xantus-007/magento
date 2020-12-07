/**
 * jquery.dbm.giftCard.js
 * contain giftCard js
 */
var giftCard;

(function($) {
    giftCard = {
        init: function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.c-radio__giftCard input[type="radio"]').on('click.dbm', function(ev) {
                self._updateBackground($(this));
            });

            $('.need-myself label, .need-myself input').on('click.dbm', function(ev) {
                self._updateFields($(this).closest('.need-myself').find('input'));
            });
        },

        _updateBackground: function($el) {
            console.log('updatebg');
            var background = this._getImgSrc($el);

            $('.c-giftCard__preview').css({
                'background-image': 'url("'+background+'")',
            });
        },

        _updateFields: function($el) {
            console.log('updatefield');
            if($el.is(":checked")) {
               $("#recipient_message").closest('.form-list').show();
            } else {
               $("#recipient_message").closest('.form-list').hide();
           }
        },

        _getImgSrc: function($el) {
            console.log('getimgsrc');
            var imgSrc = $el.attr('data-img-src');
            return imgSrc;
        }
    };

    $(document).ready(function() {
        giftCard.init();
    });
})(jQuery);
