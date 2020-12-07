/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if(typeof Product=='undefined') {
    var Product = {};
}
/**************************** BUNDLE PRODUCT **************************/
Product.Bundle = Class.create();
Product.Bundle.prototype = {
    initialize: function(config){
        this.config = config;
        this.reloadPrice();
    },
    changeSelection: function(selection){
        var parts = selection.id.split('-');
        if (this.config['options'][parts[2]].isMulti) {
            selected = new Array();
            if (selection.tagName == 'SELECT') {
                for (var i = 0; i < selection.options.length; i++) {
                    if (selection.options[i].selected && selection.options[i].value != '') {
                        selected.push(selection.options[i].value);
                    }
                }
            } else if (selection.tagName == 'INPUT') {
                selector = parts[0]+'-'+parts[1]+'-'+parts[2];
                selections = $$('.'+selector);
                for (var i = 0; i < selections.length; i++) {
                    if (selections[i].checked && selections[i].value != '') {
                        selected.push(selections[i].value);
                    }
                }
            }
            this.config.selected[parts[2]] = selected;
        } else {
            if (selection.value != '') {
                this.config.selected[parts[2]] = new Array(selection.value);
            } else {
                this.config.selected[parts[2]] = new Array();
            }
            this.populateQty(parts[2], selection.value);
        }
        this.reloadPrice();

    },

    reloadPrice: function() {
        var calculatedPrice = 0;
        var dispositionPrice = 0;
        for (var option in this.config.selected) {
            if (this.config.options[option]) {
                for (var i=0; i < this.config.selected[option].length; i++) {
                    var prices = this.selectionPrice(option, this.config.selected[option][i]);
                    calculatedPrice += Number(prices[0]);
                    dispositionPrice += Number(prices[1]);
                }
            }
        }

        optionsPrice.changePrice('bundle', calculatedPrice);
        optionsPrice.changePrice('nontaxable', dispositionPrice);
        optionsPrice.reload();

        return calculatedPrice;
    },

    selectionPrice: function(optionId, selectionId) {
        if (selectionId == '' || selectionId == 'none') {
            return 0;
        }
        var qty = null;
        if (this.config.options[optionId].selections[selectionId].customQty == 1 && !this.config['options'][optionId].isMulti) {
            if ($('bundle-option-' + optionId + '-qty-input')) {
                qty = $('bundle-option-' + optionId + '-qty-input').value;
            } else {
                qty = 1;
            }
        } else {
            qty = this.config.options[optionId].selections[selectionId].qty;
        }

        if (this.config.priceType == '0') {
            price = this.config.options[optionId].selections[selectionId].price;
            tierPrice = this.config.options[optionId].selections[selectionId].tierPrice;

            for (var i=0; i < tierPrice.length; i++) {
                if (Number(tierPrice[i].price_qty) <= qty && Number(tierPrice[i].price) <= price) {
                    price = tierPrice[i].price;
                }
            }
        } else {
            selection = this.config.options[optionId].selections[selectionId];
            if (selection.priceType == '0') {
                price = selection.priceValue;
            } else {
                price = (this.config.basePrice*selection.priceValue)/100;
            }
        }
        //price += this.config.options[optionId].selections[selectionId].plusDisposition;
        //price -= this.config.options[optionId].selections[selectionId].minusDisposition;
        //return price*qty;
        var disposition = this.config.options[optionId].selections[selectionId].plusDisposition +
            this.config.options[optionId].selections[selectionId].minusDisposition;

        if (this.config.specialPrice) {
            newPrice = (price*this.config.specialPrice)/100;
            newPrice = (Math.round(newPrice*100)/100).toString();
            price = Math.min(newPrice, price);
        }
        var result = new Array(price*qty, disposition*qty);
        return result;
    },

    populateQty: function(optionId, selectionId){
        if (selectionId == '' || selectionId == 'none') {
            this.showQtyInput(optionId, '0', false);
            return;
        }
        if (this.config.options[optionId].selections[selectionId].customQty == 1) {
            this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, true);
        } else {
            this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, false);
        }
    },

    showQtyInput: function(optionId, value, canEdit) {
        elem = $('bundle-option-' + optionId + '-qty-input');
        elem.value = value;
        elem.disabled = !canEdit;
        if (canEdit) {
            elem.removeClassName('qty-disabled');
        } else {
            elem.addClassName('qty-disabled');
        }
    },

    changeOptionQty: function (element, event) {
        var checkQty = true;
        if (typeof(event) != 'undefined') {
            if (event.keyCode == 8 || event.keyCode == 46) {
                checkQty = false;
            }
        }
        if (checkQty && (Number(element.value) == 0 || isNaN(Number(element.value)))) {
            element.value = 1;
        }
        parts = element.id.split('-');
        optionId = parts[2];
        if (!this.config['options'][optionId].isMulti) {
            selectionId = this.config.selected[optionId][0];
            this.config.options[optionId].selections[selectionId].qty = element.value*1;
            this.reloadPrice();
        }
    },

    validationCallback: function (elmId, result){
        if (elmId == undefined || $(elmId) == undefined) {
            return;
        }
        var container = $(elmId).up('ul.options-list');
        if (typeof container != 'undefined') {
            if (result == 'failed') {
                container.removeClassName('validation-passed');
                container.addClassName('validation-failed');
            } else {
                container.removeClassName('validation-failed');
                container.addClassName('validation-passed');
            }
        }
    }
}

function changeOptions(optionsArray) {
	jQuery('#custombento').addClass('loading');
	jQuery('#custombento').children().removeAttr('style');;
	var images = [];
	optionsArray.each(
		function( intIndex ){
			images.push(jQuery('.option-wrapper span#'+intIndex).children('span:not(.hover)').attr('rel'));	
		}
	);		
			
	jQuery.preLoadImages(images,function(){
		jQuery('#custombento').removeClass('loading');
		optionsArray.each(
			function( intIndex ){	
				changeOption(jQuery('.option-wrapper span#'+intIndex),true);	
			}
		);
		reloadFB();
		changeDownloadLinkImage();
	});
	
	return false;
}

function reloadFB() {
	changeUrl();
	changeFBUrl();
	changeFBImage();
	jQuery('.fb-like').remove();
	jQuery('.product-img-box .share .partage').after('<div class="fb-like" data-href="'+jQuery('meta[property="og:url"]').attr('content')+'" data-send="false" data-layout="button_count" data-width="420" data-show-faces="false" data-font="tahoma"></div>');
	FB.XFBML.parse();
}

function changeOption(el,delayreload) {
	el.parent().children('span.bundle-color').removeClass('active');
	el.addClass('active');
	var exploded = el.attr('rel').split(',');
	el.parent().children('input').val(exploded[0]);
	if (el.children('span:not(.hover)').attr('rel')) changeCustomImage(exploded[1],el.children('span:not(.hover)').attr('rel'),delayreload);
}

function changeUrl() {
	query = '?';
	jQuery('#bundleoptions .input-box input').each(
		function(){	
			query = query=='?'?query:query+'&';
			query = query + jQuery(this).attr('id')+'='+jQuery(this).val();				
		}
	);
	if (jQuery.browser.msie) {
		if (jQuery("div.historyIE").length === 0) { 
		jQuery('body').append('<div class="historyIE"></div>');
		}
		jQuery('div.historyIE').text(location.protocol+'//'+location.host+location.pathname+query);
	} else {
	    history.replaceState( {}, this.title || '', query );
	}
}
					
function changeCustomImage(el,url,noreload) {
	jQuery('#element'+el).fadeTo(100, 0, function()
	{
		jQuery(this).css('background-image', 'url(' + url + ')');
	}).fadeTo(400, 1);
	if (!noreload) {
		changeUrl();
		reloadFB();
		changeDownloadLinkImage();
	}
}

function preloadCustomImages (){
	jQuery('#custombento').addClass('loading');
	var images = [];
	jQuery('#bundleoptions .bundle-color').children('span:not(.hover)').each(
		function( intIndex ){
			if (jQuery(this).attr('rel')) {
				images.push(jQuery(this).attr('rel'));
			}
		}
	);
	
	jQuery.preLoadImages(images,function(){
		jQuery('#custombento').removeClass('loading');
		jQuery('#bundleoptions .bundle-color').each(
			function( intIndex ){	
				if (jQuery(this).hasClass("active") && jQuery(this).children('span:not(.hover)').attr('rel')) {
					var exploded = jQuery(this).attr('rel').split(',');
					changeCustomImage(exploded[1],jQuery(this).children('span:not(.hover)').attr('rel'),true);
				}
			}
		);
		changeUrl();
		reloadFB();
		changeDownloadLinkImage();
	});
}

function changeFBUrl() {
	if (jQuery.browser.msie) {
		jQuery('meta[property="og:url"]').attr('content',jQuery('div.historyIE').text());
	} else {
		jQuery('meta[property="og:url"]').attr('content',window.location);
	}
}

function changeFBImage() {
	var query = [location.protocol, '//', location.host].join('')+ '/bundle/image/generate';
	datas = {};
	jQuery('#bundleoptions .bundle-color.active').each(
		function( intIndex ){
			var exploded = jQuery(this).attr('rel').split(',');
            datas[exploded[1]] = exploded[2];
		}
	);
	jQuery.ajax({
	  type: "POST",
	  url: query,
	  data: datas
	}).done(function( msg ) {
		jQuery('meta[property="og:image"]').attr('content',msg);
	});
}

function changeDownloadLinkImage (){
	var query = [location.protocol, '//', location.host].join('')+ '/bundle/image/download';
	datas = '';
	jQuery('#bundleoptions .bundle-color.active').each(
		function( intIndex ){
			var exploded = jQuery(this).attr('rel').split(',');
            datas=datas+exploded[1]+'/'+exploded[2]+'/';
		}
	);
	jQuery('.product-bundle #downloadlink').attr('href',query+'/'+datas);
}


/*
jQuery(function($) {
  jQuery('body').bind('keypress', function(e){
  	if (e.which == 83) {
    arrayOptions = new Array();
	jQuery('.option-wrapper span.active').each(
		function(){
			arrayOptions.push(jQuery(this).attr('id'));
		}
	);
	alertMsg = "onclick=\"changeOptions([";
	arrayOptions.each(
		function( intIndex ){
			if (alertMsg != "onclick=\"changeOptions([") alertMsg= alertMsg+',';
			alertMsg = alertMsg + "'" + intIndex + "'";
		}
	);
	
	alertMsg = alertMsg + "]);return false;\"";
			alert(alertMsg);
		}
  });
});*/



(function ($) {
    $.preLoadImages = function(imageList,callback) {
        var pic = [], i, total, loaded = 0;
        if (typeof imageList != 'undefined') {
            if ($.isArray(imageList)) {
                total = imageList.length; // used later
                    for (i=0; i < total; i++) {
                        pic[i] = new Image();
                        pic[i].onload = function() {
                            loaded++; // should never hit a race condition due to JS's non-threaded nature
                            if (loaded == total) {
                                if ($.isFunction(callback)) {
                                    callback();
                                }
                            }
                        };
                        pic[i].src = imageList[i];
                    }
            } else {
                pic[0] = new Image();
                if ($.isFunction(callback)) {
                    pic[0].onload = callback;
                }
                pic[0].src = imageList;
            }
        } else if ($.isFunction(callback)) {
            //nothing passed but we have a callback.. so run this now
            //thanks to Evgeni Nobokov
            callback();
        }
        pic = undefined;
    };
})(jQuery);


(function($) {
	$(document).ready(function(){
		$('#bundleoptions .bundle-color').bind('click', function() {
			changeOption($(this));
		});
	});
})(jQuery);

