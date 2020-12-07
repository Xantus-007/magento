/**
 * jquery.dbm.app.js
 * contain global project js
 */
var addToCart;

(function($) {
    addToCart = {
        init : function() {
            $('body').on('click touchend', '.js-openmfp', function(ev) {
                var self = $(this);
                ev.preventDefault();
                
                if(self.attr('data-cart-url'))
                {
                    var url = self.attr('data-cart-url');
                    var data = 'qte=1&isAjax=1';
                }
                else
                {
                    var productAddToCartForm = new VarienForm('product_addtocart_form');
                    var form = productAddToCartForm.form;
                    var url = form.action;
                    var data = $('#product_addtocart_form').serialize();
                    data += '&isAjax=1';
                }
           
                url = url.replace("checkout/cart","dbmAjaxAddToCart/product");
                
                addToCart.createMfp();
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
                                        item.src = '<div id="c-magnificpopup__addtobasket" class="c-mfp__comparator zoom-anim-dialog text-center"><div class="row"><p>' + data.message + '</p></div></div>';
                                    }
                                });
                                currentMfp.updateItemHTML();
                            }
                        }
                    });
                } catch (e) {
                    currentMfp.close();
                }
            });
            
            $('body').on('click', '.force-close-mfp, .js-closeMfp', function(ev) {
                ev.preventDefault();
                var currentMfp = $.magnificPopup.instance;
                currentMfp.close();
            });
        },
        
        createMfp : function() {
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

    $(document).ready(function() {
        addToCart.init();
    });

})(jQuery);
