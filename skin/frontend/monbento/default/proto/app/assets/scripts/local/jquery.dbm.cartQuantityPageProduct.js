/**
 * jquery.dbm.cartQuantityPageProduct.js
 * contain cartQuantityPageProduct js
 */
var cartQuantityPageProduct;

(function($) {
    cartQuantityPageProduct = {
        init: function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.js-form__watchQuantity .c-input__text input').on('change.dbm', function(ev) {
                self._updateForm($(this));
            });
        },

        _updateForm: function($el) {
            var $form = $el.parents('.c-quantity');
            var $target = this._getForm($form);
            var value = this._returnVal($el);

            this._updateVal($target, value);
        },

        _updateVal: function($form, value) {
            $form.find('.c-input__text input').val(value);
        },

        _getForm: function($el) {
            var formToUpdate = $el.attr('data-dbm-form-watch');
            return $(formToUpdate);
        },

        _returnVal: function($el) {
            var value = $el.val();
            return value;
        },
    };

    $(document).ready(function() {
        cartQuantityPageProduct.init();
    });
})(jQuery);
