/**
 * jquery.dbm.cartQuantity.js
 * contain cartQuantity js
 */
var cartQuantity;

(function($) {
    cartQuantity = {
        $quantityForm   : null,
        $quantityInput  : null,
        limit           : 1,
        $shoppingCart   : false,

        init: function() {
            this.$quantityForm = $('.js-form__quantity');
            this.$quantityInput = $('.js-input__quantity');
            this.$shoppingCartForm = $('.shopping-cart-form');
            if(this.$shoppingCartForm.size() == 1) this.$shoppingCart = true;

            this._clickHandler();
        },

        _clickHandler: function() {
            var self = this;

            $.each(self.$quantityForm, function() {
                var $form = $(this);

                $('.js-quantity__decrease', $(this)).on('click.dbm', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    self._updateQuantity($form, 'decrease');
                });

                $('.js-quantity__increase', $(this)).on('click.dbm', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    self._updateQuantity($form, 'increase');
                });
            });
        },

        _updateQuantity: function($form, action) {
            var self = this;

            switch(action) {
                case 'decrease' :
                    if (self._getQuantity($form) > self.limit) {
                        self._setQuantity($form, self._getQuantity($form) - 1);
                        if(this.$shoppingCart) self._updateCart();
                    }
                    break;
                case 'increase' :
                    self._setQuantity($form, self._getQuantity($form) + 1);
                    if(this.$shoppingCart) self._updateCart();
                    break;
                default :
                    return false;
            }
        },

        _updateCart: function() {
            $('.btn-update').click();
        },

        _setQuantity: function($form, quantity) {
            $('.c-input__text input', $form).val(quantity).trigger('change.dbm');
        },

        _getQuantity: function($form) {
            var quantity = parseInt($('.c-input__text input', $form).val());
            return quantity;
        },

    };

    $(document).ready(function() {
        cartQuantity.init();
    });
})(jQuery);
