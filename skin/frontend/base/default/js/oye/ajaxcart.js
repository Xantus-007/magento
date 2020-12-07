/**
 * Let's register Ajax loader
 */
function startLoading() {
    if (jQuery("#ajax-loader-image").length == 0) {
        var body = jQuery('body');
//    body.insert("<p>HTML to append</p>");
        var img = new Element('img', {src: loaderImg});
        img.id = "ajax-loader-image";
        body.prepend('<div id="overlay"> </div>');
        body.prepend(img);
    }
}
function stopLoading() {
    if ((jQuery('#ajax-loader-image').length != 0) ) {
        jQuery("#ajax-loader-image").remove();
        jQuery("#overlay").remove();
    }
}
jQuery( document ).ajaxStart(function() {
    startLoading();
});
jQuery( document ).ajaxStop(function() {
    stopLoading();
});

var oyeAjaxcart = Class.create();
oyeAjaxcart.prototype = {
    initialize: function () {
        this.timeOut;
        this.effect;
        this.findCartButtons();
        this.loadQuickCart(false);
    },
    loadQuickCartLabel: function (autoOpen) {
        jQuery.ajax({
            url: quickCartLabelUrl,
            type: "get",
            dataType: "html",
            success: function(returnData){
                jQuery(topCartLinkClass).replaceWith(returnData);
                oyeAjaxcart.observeTopLinks();
                if (autoOpen) {
                    oyeAjaxcart.openCart();
                    oyeAjaxcart.timeOut = setTimeout('oyeAjaxcart.closeCart()', 3000);
                }
            },
            error: function(e){
                throw new Error(e);
            }
        });
    },
    loadQuickCart: function (autoOpen) {
        clearTimeout(oyeAjaxcart.timeOut);
        if ((jQuery('#ajax-top-cart').length != 0) ) {
            jQuery('#ajax-top-cart').remove();
        }
        jQuery.ajax({
            url: quickCartUrl,
            type: "get",
            dataType: "html",
            success: function(returnData){
                jQuery(topCartLinkClass).after(returnData);
                if (autoOpen) {
                    oyeAjaxcart.openCart();
                    oyeAjaxcart.timeOut = setTimeout('oyeAjaxcart.closeCart()', 3000);
                }
                oyeAjaxcart.observeMouse();
                oyeAjaxcart.observeRemoveButtons();
            },
            error: function(e){
                throw new Error(e);
            }
        });
    },
    observeRemoveButtons: function () {
        var buttons = jQuery('#ajax-top-cart a.ajax-btn-remove');
        if (buttons && buttons.length > 0) {

            buttons.each(function () {
                var url = jQuery(this).attr('href');
                jQuery(this).attr('href','#');
                jQuery(this).attr('onclick','#');

                jQuery(this).click(function() {
                    jQuery('#ajax-top-cart').slideUp(500);
                    jQuery.ajax({
                        url: url,
                        type: "get",
                        dataType: "html",
                        success: function(returnData){
                            oyeAjaxcart.loadQuickCart(true);
                            oyeAjaxcart.loadQuickCartLabel();
                        },
                        error: function(e){
                            throw new Error(e);
                        }
                    });
                });
            });
        }
    },
    openCart: function () {
        if(jQuery('#ajax-top-cart').length != 0 && !jQuery('#ajax-top-cart').is(':visible')) {
            jQuery('body,html').animate({scrollTop: 0}, 700);
            jQuery('#ajax-top-cart').slideDown(700);
        }
    },
    closeCart: function () {
        if(jQuery('#ajax-top-cart').length != 0 && jQuery('#ajax-top-cart').is(':visible')) {
            oyeAjaxcart.effect = jQuery('#ajax-top-cart').slideUp(700);
        }
    },
    observeMouse: function () {
        if(jQuery('#ajax-top-cart').length != 0) {
            jQuery('#ajax-top-cart').mouseover(function(el){
                clearTimeout(oyeAjaxcart.timeOut);
            });
            jQuery('#ajax-top-cart').mouseout(function(el){
                oyeAjaxcart.timeOut = setTimeout('oyeAjaxcart.closeCart()', 1000);
            });
        }
    },
    observeTopLinks: function() {
        if(jQuery(topCartLinkClass).length != 0) {
            jQuery(topCartLinkClass).mouseover(function(el){
                clearTimeout(oyeAjaxcart.timeOut);
                oyeAjaxcart.openCart();
            });
            jQuery(topCartLinkClass).mouseout(function(el){
                oyeAjaxcart.timeOut = setTimeout('oyeAjaxcart.closeCart()', 1000);
            });
        };
    },
    findCartButtons: function () {

        if(jQuery('.category-products button.btn-cart').length != 0) {
            var buttons = jQuery('.category-products button.btn-cart');
            buttons.each(function (el) {
                var url = jQuery(this).attr('onclick');
                if(url.search("cart") != -1) {
                    jQuery(this).attr('onclick','');
                    url = url.replace("setLocation('", "");
                    url = url.replace("')", "");

                    jQuery(this).click(function(el){
                        el.preventDefault();
                        jQuery.ajax({
                            url: url,
                            type: "get",
                            dataType: "html",
                            data: {isAjax:1},
                            success: function(returnData){
                                oyeAjaxcart.loadQuickCart(true);
                                oyeAjaxcart.loadQuickCartLabel();
                            },
                            error: function(e){
                                throw new Error(e.toArray());
                            }
                        });
                    });
                }

            });
        }
    }
}
document.observe('dom:loaded', function () {
    if (typeof productAddToCartForm != 'undefined' && productAddToCartForm.submit) {
        productAddToCartForm.submit = productAddToCartForm.submit.wrap(function (originalSubmit, button, url) {
            productAddToCartForm.originalSubmit = originalSubmit;
            if (false) {
                originalSubmit();
            } else {
                if (this.validator.validate()) {
                    var form = this.form;
                    var oldUrl = form.action;
                    var formId = form.id;

                    if (url) {
                        form.action = url;
                    }
                    var e = null;
                    try {
                        if (button && button != 'undefined') {
                            button.disabled = true;
                        }

                        if(form.action.search("updateItemOptions") >= 0) {
                            form.submit();
                        } else { 

                            var ajaxUrl = form.action.replace('/checkout/', '/oyecheckout/');
                            var formData = jQuery('#' + formId).serializeArray();
                            formData.push({name: "isAjax", value: 1});
                            jQuery.ajax({
                                url: ajaxUrl,
                                data: formData,
                                type: "POST",
                                success: function (returnData) {
                                    var response = JSON.parse(returnData);
                                    if (response.success) {
                                        oyeAjaxcart.loadQuickCart(true);
                                        oyeAjaxcart.loadQuickCartLabel();
                                        form.reset();
                                    } else {
                                        var errors = response.errors;
                                        var message = '';
                                        for (var i = 0; i < errors.length; i++) {
                                            message += errors[i] + "\n";
                                        }
                                        throw new Error(message);
                                    }
                                },
                                error: function (e) {
                                    throw new Error(e);
                                }
                            });
                        }
                    } catch (e) {
                    }
                    this.form.action = oldUrl;
                    if (e) {
                        throw e;
                    }

                    button.disabled = false;
                }
            }
        });
    }
});
