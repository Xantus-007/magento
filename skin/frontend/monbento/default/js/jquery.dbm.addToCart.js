/**
 * jquery.dbm.app.js
 * contain global project js
 */
var addToCart;

(function($) {
    addToCart = {
        init : function() {
            this._eventHandler();
        },

        _eventHandler : function() {
            var self = this;

            $('body').on('click', '.js-openmfp', function(ev) {
                ev.preventDefault();
                self._openMfp($(this));
            });

            $('body').on('click touchend', '.js-openmfp__product', function(ev) {
                ev.preventDefault();
                self._openMfp($(this));
            });

            $('body').on('click', '.force-close-mfp, .js-closeMfp', function(ev) {
                ev.preventDefault();
                self._closeMfp();
            });
        },

        _openMfp : function($el) {
            var self = this;

            if($el.attr('data-cart-url'))
            {
                var url = $el.attr('data-cart-url'),
                    data = 'qte=1&isAjax=1';
            }
            else
            {
                var productAddToCartForm = new VarienForm('product_addtocart_form'),
                    form = productAddToCartForm.form,
                    url = form.action
                    data = $('#product_addtocart_form').serialize();
                
                data += '&isAjax=1';
            }
       
            url = url.replace("checkout/cart","dbmAjaxAddToCart/product");
            
            self._createMfp();
            var currentMfp = $.magnificPopup.instance;

            currentMfp.st.closeOnBgClick = false;
            currentMfp.st.closeBtnInside = true;
            $('.mfp-wrap').addClass('mfp-close-btn-in');
            
            try {
                jQuery.ajax({
                    url: url,
                    dataType: 'json',
                    type : 'post',
                    data: data,
                    success: function(data){
                        if(data.status == "SUCCESS")
                        {
                            $('.c-cart').replaceWith(data.minicart);
                            currentMfp.items.each( function(item) {
                                if($.isPlainObject(item))
                                {
                                    item.src = data.modal;
                                }
                            });
                            currentMfp.updateItemHTML();
                        } 
                        else 
                        {
                            currentMfp.items.each( function(item) {
                                if($.isPlainObject(item))
                                {
                                    item.src = '<div id="c-magnificpopup__addtobasket" class="c-mfp__comparator zoom-anim-dialog text-center"><div class="row c-empty__modal"><div class="o-table"><div class="o-table__cell--valignMiddle"><span>' + data.message + '</span></div></div></div></div>';
                                }
                            });
                            currentMfp.updateItemHTML();
                        }
                    }
                });
            } catch (e) {
                currentMfp.close();
            }
        },

        _closeMfp : function() {
            var currentMfp = $.magnificPopup.instance;
            currentMfp.close();
        },
        
        _createMfp : function() {
            $.magnificPopup.open({
                closeBtnInside: true,
                closeOnContentClick: false,
                closeOnBgClick: true,
                items: {
                    src: '#c-magnificpopup__addtobasket',
                    type: 'inline'
                }
            });
        }
    };

    checkoutOnepage = {
        init : function() {
            if($('#billing_fiscal_id').length == 0)
                return;
            this._changeValue();
            $('#billing_country_id select').change(this._changeValue);
        },
        _changeValue : function() {
            var country = $('#billing_country_id select').val();
            if(country == 'IT')
                $('#billing_fiscal_id').show();
            else
                $('#billing_fiscal_id').hide();
        }
    };

    editAddress = {
        init : function() {
            if($('body.customer-address-form #fiscal_id').length == 0)
                return;
            this._changeValue();
            this._checkBilling();
            $('#country').change(this._checkBilling);
            $('#primary_billing').change(this._checkBilling);
        },
        _changeValue : function() {
            var country = $('#country').val();
            if(country == 'IT')
                $('#fiscal_id').parents('li').show();
            else
                $('#fiscal_id').parents('li').hide();
        },
        _checkBilling : function() {
            var country = $('#country').val();
            var defaultBilling = $('#fiscal_id').data('default-billing');
            if(($('#primary_billing').is(":checked") && country == 'IT') || (defaultBilling && country == 'IT'))
                $('#fiscal_id').parents('li').show();
            else
                $('#fiscal_id').parents('li').hide();
        }
    };

    $(document).ready(function() {
        addToCart.init();
        checkoutOnepage.init();
        editAddress.init();
    });

})(jQuery);
