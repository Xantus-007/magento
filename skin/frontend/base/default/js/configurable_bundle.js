document.observe(
    "dom:loaded", function () {
    bundle.setCheckoutDisabled(true);

    for (var optionId in bundle.config['options']) {
        var option = bundle.config['options'][optionId];

        if($$('.bundle-option-' + optionId)[0]) {
            var selectedId = $$('.bundle-option-' + optionId)[0].getValue();

            if(bundle.optionHasConfigurableSelected(option, selectedId)) {
                var selection = bundle.getBundleSelection(option, selectedId);

                bundle.setConfigurableAttributes(option, optionId, selectedId);

                if(selection.confProductId != undefined) {
                    var attributeSelection = selection.confProductId + '_' + selectedId;
                }

                selection.confattributes[attributeSelection].items.each(
                    function (attribute) {
                        bundle.fillAttribute(option, optionId, selectedId, attribute);
                    }
                );
            }
        }
    };

    $$('.bundle-selection-qty').each(
        function (element) {
        $(element).addEventListener(
            'keyup', function () {
            bundle.changeSelectionQty(element) }, false
        );
        }
    );

    $$('.product-custom-option').each(
        function (element) {
        $(element).addEventListener(
            'change', function () {
            bundle.reloadPrice() }, false
        );
        }
    );

    $$('.selection-change').each(
        function (element) {
        $(element).addEventListener(
            'click', function () {
                console.log($(element).getAttribute('data-val'));

                $$('.bundle-option-' + $(element).getAttribute('data-option')).first().value = $(element).getAttribute('data-val');

                $$('.bundle-option-' + $(element).getAttribute('data-option') + ' option').each(
                    function (oneElement) {
                        $(oneElement).removeClassName('selected');
                    }
                );

                $$('[data-option=' + $(element).getAttribute('data-option') + ']').each(
                    function (oneElement) {
                        $(oneElement).removeClassName('selected');
                    }
                );

                $(element).addClassName('selected');

                bundle.changeSelection($$('.bundle-option-' + $(element).getAttribute('data-option')).first());
            }, false
        );

        }
    );

    }
);

Product.Bundle.prototype.changeSelectionQty = function (element) {
    var optionId = $(element).getAttribute('data-option');
    var selectionId = $(element).getAttribute('data-selection');

    $('bundle-option-' + optionId + '-' + selectionId).setAttribute('checked', 'checked');

    bundle.changeSelection(element);
};

Product.Bundle.prototype.origChangeSelection = Product.Bundle.prototype.changeSelection;
Product.Bundle.prototype.changeSelection = function (selection) {
    this.origChangeSelection(selection);

    var parts = selection.id.split('-');
    var optionId = parts[2];
    var option = bundle.config['options'][optionId];

    if(bundle.optionHasConfigurableSelected(option, $(selection).getValue())) {
        this.removeConfigurableInputs(optionId);

        bundle.setConfigurableAttributes(option, optionId, $(selection).getValue());
        var bundleSelection = bundle.getBundleSelection(option, $(selection).getValue());

        if (bundleSelection.confProductId != undefined) {
            var attributeSelection = bundleSelection.confProductId + '_' + $(selection).getValue();
        }

        bundleSelection.confattributes[attributeSelection].items.each(
            function (attribute) {
            bundle.fillAttribute(option, optionId, $(selection).getValue(), attribute);
            }
        );

        this.updateOptionData(optionId);
    }
};

Product.Bundle.prototype.optionHasConfigurableSelected = function (option, selectedId) {
    if(option.selections[selectedId] == undefined) {
        return false;
    }

    return option.selections[selectedId].confattributes != undefined;
};

Product.Bundle.prototype.getBundleOption = function (optionId) {
    return bundle.config.options[optionId];
};

Product.Bundle.prototype.getBundleSelection = function (option, selectedId) {
    return option.selections[selectedId];
};

Product.Bundle.prototype.removeConfigurableInputs = function (optionId) {
    document.getElementById('bundle-options-' + optionId).innerHTML = "";
};

Product.Bundle.prototype.fillAttribute = function (option, optionId, selectedId, attribute) {
    var configurableAttributes = eval('configurable' + optionId + selectedId);
    var configurableCombinations = eval('configurableCombinations' + optionId + selectedId);

    $('super-option-' + optionId + '-' + attribute.attribute_id).descendants().each(Element.remove);

    $('super-option-' + optionId + '-' + attribute.attribute_id).insert(
        {
        bottom: '<option value="0">Select a value...</option>'
        }
    );

    this.setSwatches(option, optionId, selectedId, attribute, attribute.prices);
};

Product.Bundle.prototype.updateAttributes = function (option, optionId, selectedId, attribute) {
    var configurableCombinations = eval('configurableCombinations' + optionId + selectedId);

    var selection = this.getBundleSelection(option, selectedId);


    if(selection.confProductId != undefined) {
        var attributeSelection = selection.confProductId + '_' + selectedId;
    }

    selection.confattributes[attributeSelection].items.each(
        function (item, i) {
        if($('super-option-' + optionId + '-' + attribute.attribute_id).getValue() != null) {
            var nextAttribute = selection.confattributes[attributeSelection].items[i+1];

            var products = [];

            if(nextAttribute != undefined) {
                for(combinationId in configurableCombinations) {
                    if(configurableCombinations[combinationId][attribute.attribute_id] == $('super-option-' + optionId + '-' + attribute.attribute_id).getValue()) {
                        products.push(configurableCombinations[combinationId]);
                    }
                }

                bundle.toggleSwatches(option, optionId, nextAttribute, products);
            }
        }
        }
    );
}

Product.Bundle.prototype.toggleSwatches = function (option, optionId, attribute, products) {
    if($('super-option-' + optionId + '-' + attribute.attribute_id + '-swatch')) {
        $('super-option-' + optionId + '-' + attribute.attribute_id + '-swatch').childElements().invoke('removeClassName', 'not-available');
        $('super-option-' + optionId + '-' + attribute.attribute_id + '-swatch').childElements().invoke('toggleClassName', 'not-available');
    }

    for(i in products) {
        if($('option' + optionId + '-' + products[i][attribute.attribute_id])) {
            $('option' + optionId + '-' + products[i][attribute.attribute_id]).removeClassName('not-available');
        }
    }
}

Product.Bundle.prototype.getProductInformation = function (option, optionId, selectedId, attributeId) {
    this.getProductOptions(option, optionId, selectedId, attributeId);
    this.getProductGallery();

    this.updateOptionData(optionId);
};

Product.Bundle.prototype.getProductGallery = function () {
    var selectiondata = {};

    var formData = $('product_addtocart_form').serialize(true);

    $H(bundle.config.options).each(
        function (pair) {
        if($$('[name="bundle_option[' + pair.key + ']"]')) {
            if($$('[name="bundle_option[' + pair.key + ']"]')[0]) {
                var value = $$('[name="bundle_option[' + pair.key + ']"]')[0].value;

                if(bundle.config.options[pair.key]['selections'][value]) {
                    var configurableId = bundle.config.options[pair.key]['selections'][value].confProductId;

                    if(configurableId > 0) {
                        var attributes = {};

                        $H(formData).each(
                            function (attributepair) {
                            if(attributepair.key.indexOf('super_attribute[' + pair.key + ']') > -1) {
                                var matches = attributepair.key.match(/super_attribute\[(\d+)\]\[(\d+)\]/);
                                attributes[matches[2]] = attributepair.value;
                            }
                            }
                        );

                        selectiondata[configurableId] = attributes;
                    }
                }
            }
        }
        }
    );

    new Ajax.Request(
        $('baseurl').readAttribute('data-baseurl') + '/wizbundle/ajax/productgallery', {
        method:'POST',
        parameters: {'products': JSON.stringify(selectiondata)},
        onSuccess: function (transport) {
            if(transport.responseText != '') {
                var data = JSON.parse(transport.responseText);
            }

            data.each(
                function (item) {
                jQuery('.product-image-gallery').append('<img id="image-' + item.id + '" class="gallery-image" src="/media/configurablebundle/' + item.main + '" data-zoom-image="/media/configurablebundle/' + item.main + '">');
                jQuery('ul.product-image-thumbs').append('<li> <a class="thumb-link" href="#" title="" data-image-index="' + item.id + '"> <img src="/media/configurablebundle/' + item.thumbnail + '" width="75" height="75" alt=""> </a> </li>');

                ProductMediaManager.init();
                ProductMediaManager.swapImage('#image-' + item.id);
                }
            );
        }
        }
    );
};

Product.Bundle.prototype.getProductOptions = function (option, optionId, selectedId, attributeId) {
    this.setCheckoutDisabled();

    var custom_options_id = 'custom-option-'+ optionId;
    if($(custom_options_id)){
        $(custom_options_id).update('');
    }

    if($$('[name="bundle_option[' + optionId + ']"]')) {
        var selection = $$('[name="bundle_option[' + optionId + ']"]')[0].value;

        if(bundle.config.options[optionId]) {
            if(bundle.config.options[optionId]['selections'][selection]) {
                var configurableId = bundle.config.options[optionId]['selections'][selection].confProductId;

                var attributes = {};

                var formData = $('product_addtocart_form').serialize(true);

                $H(formData).each(
                    function (pair) {
                    if(pair.key.indexOf('super_attribute[' + optionId + ']') > -1) {
                        var matches = pair.key.match(/super_attribute\[(\d+)\]\[(\d+)\]/);
                        attributes[matches[2]] = pair.value;
                    }
                    }
                );

                new Ajax.Request(
                    $('baseurl').readAttribute('data-baseurl') + '/wizbundle/ajax/productoptions/id/' + configurableId, {
                    method:'POST',
                    parameters: attributes,
                    onSuccess: function (transport) {
                        if (transport.responseText != '') {
                            if (!$(custom_options_id)) {
                                $$('#bundle-options-' + option_id)[0].insert({after: '<div class="bundle-custom-options" id="' + custom_options_id + '">' + transport.responseText + '</div>'});
                            } else {
                                $(custom_options_id).replace(transport.responseText);
                            }
                        }

                        this.setCheckoutDisabled(false);
                    },
                    onFailure: function () {
                        $(custom_options_id).update('');
                        this.setCheckoutDisabled(false);
                    }
                    }
                );
            }
        }
    }
};

Product.Bundle.prototype.updateOptionData = function (option_id) {
    if($$('[name="bundle_option[' + option_id + ']"]')) {
        var selection = $$('[name="bundle_option[' + option_id + ']"]')[0].value;
        var qty = $$('[name="bundle_option_qty[' + option_id + ']"]')[0].value;
        if(bundle.config.options[option_id]) {
            if(bundle.config.options[option_id]['selections'][selection]) {
                var configurableId = bundle.config.options[option_id]['selections'][selection].confProductId;

                var attributes = {};

                var formData = $('product_addtocart_form').serialize(true);

                $H(formData).each(
                    function (pair) {
                    if(pair.key.indexOf('super_attribute[' + option_id + ']') > -1) {
                        var matches = pair.key.match(/super_attribute\[(\d+)\]\[(\d+)\]/);
                        attributes[matches[2]] = pair.value;
                    }
                    }
                );

                new Ajax.Request(
                    $('baseurl').readAttribute('data-baseurl') + '/wizbundle/ajax/productinfo/id/' + configurableId, {
                    method:'POST',
                    parameters: attributes,
                    onSuccess: function (transport) {
                        if(transport.responseText != '') {
                            var data = JSON.parse(transport.responseText);
                        }

                        if(updateStatus.name == true) {
                            jQuery('#bundle-option-name-' + option_id).html(data.name);
                        }

                        if(updateStatus.description == true) {
                            jQuery('#bundle-option-description-' + option_id).html(data.description);
                        }

                        if(updateStatus.stock == true) {
                            if(data.stock <= 0) {
                                jQuery('#bundle-option-stock-' + option_id).html(
                                    '<p class="availability out-of-stock">Availability: <span>Out of stock</span></p>'
                                );
                            } else {
                                jQuery('#bundle-option-stock-' + option_id).html(
                                    '<p class="availability in-stock">Availability: <span>In stock</span></p>'
                                );
                            }
                        }


                        if(bundle.config.isFixedPrice == false) {
                            bundle.config.options[option_id]['selections'][selection]['price'] = data.price;
                            bundle.config.options[option_id]['selections'][selection]['priceInclTax'] = data.price;
                            bundle.config.options[option_id]['selections'][selection]['priceExclTax'] = data.price;

                            if(typeof(data.tier_price) != 'undefined') {
                                bundle.config.options[option_id]['selections'][selection]['tierPrice'] = data.tier_price;
                            }

                        }

                        bundle.reloadPrice();
                    },
                    onFailure: function () {
                    }
                    }
                );
            }
        }
    }
};

Product.Bundle.prototype.setCheckoutDisabled = function (disable) {

    $$('.btn-cart').each(
        function (item,index) {
        if(disable == true) {
            $(item).setAttribute('disabled', 'disabled');
            $(item).addClassName('loading');
        } else {
            $(item).removeAttribute('disabled');
            $(item).removeClassName('loading');
        }
        }
    );
};

Product.Bundle.prototype.setSwatches = function (option, optionId, selectedId, attribute, items) {
    if($('super-option-' + optionId + '-' + attribute.attribute_id + '-swatch')) {
        $('super-option-' + optionId + '-' + attribute.attribute_id + '-swatch').innerHTML = "";
    }

    $(items).each(
        function (item) {
        if(updateStatus.enable_swatches ==  true) {
            var aWidth = 'auto';
            var labelWidth = 'auto';
            var labelExtraCss = '';

            if(typeof(swatchImages[attribute.attribute_id][item.value_index]) != 'undefined') {
                var swatchData = '<img src="'
                    + swatchImages[attribute.attribute_id][item.value_index]
                    + '" alt="' + item.store_label.toLowerCase()
                    + '" width="'+ swatchDimensions['inner_width'] + '" height="' + swatchDimensions['inner_height'] + '">';
                aWidth = swatchDimensions['outer_width'] + 'px';
                labelWidth = swatchDimensions['inner_width'] + 'px';
                labelExtraCss = 'min-width: ' + swatchDimensions['outer_width'] + 'px;';
            } else {
                labelExtraCss = 'min-width: ' + swatchDimensions['outer_width'] + 'px;';
                var swatchData = ' ' + item.store_label;
            }

            $('super-option-' + optionId + '-' + attribute.attribute_id + '-swatch').insert(
                {
                bottom: '<li class="option-' + item.store_label.toLowerCase() + ' is-media" id="option' + optionId + '-' + item.value_index + '">' +
                '<a href="javascript:void(0)" name="' + item.store_label.toLowerCase() + '" id="swatch' + optionId + '-' + item.value_index + '" data-value="' + item.value_index + '" class="swatch-link swatch-link-' + attribute.id + ' has-image" title="' + item.store_label.toLowerCase() + '" style="height: 23px; width: ' + aWidth + ';" onclick="$(\'super-option-' + optionId + '-' + attribute.attribute_id + '\').setValue(this.readAttribute(\'data-value\'));">' +
                '<span class="swatch-label" style="height: 21px; width: ' + labelWidth + '; line-height: 21px;' + labelExtraCss + '">' +
                swatchData +
                '</span>' +
                '<span class="x">X</span>' +
                '</a>' +
                '</li>'
                }
            );

            if($('super-option-' + optionId + '-' + attribute.attribute_id + '-swatch').readAttribute('data-last') == attribute.attribute_id) {
                $('swatch' + optionId + '-' + item.value_index).addEventListener(
                    'click', function () {
                    bundle.setSwatchSelected(optionId, item.value_index, 'super-option-' + optionId + '-' + attribute.attribute_id + '-swatch'); bundle.getProductInformation(option, optionId, selectedId, attribute.attribute_id) }, false
                );
            } else {
                $('swatch' + optionId + '-' + item.value_index).addEventListener(
                    'click', function () {
                    bundle.setSwatchSelected(optionId, item.value_index, 'super-option-' + optionId + '-' + attribute.attribute_id + '-swatch'); bundle.updateAttributes(option, optionId, selectedId, attribute) }, false
                );
            }
        }

        $('super-option-' + optionId + '-' + attribute.attribute_id).insert(
            {
            bottom: '<option value="' + item.value_index + '">' + item.store_label + '</option>'
            }
        );

        $('super-option-' + optionId + '-' + attribute.attribute_id).selectedIndex = 0;
        }
    );
};

Product.Bundle.prototype.setSwatchSelected = function (optionId, value, ulId) {
    $(ulId).childElements().invoke('removeClassName', 'selected');
    $('option' + optionId + '-' + value).toggleClassName('selected');
}

Product.Bundle.prototype.setConfigurableAttributes = function (option, optionId, selectedId) {
    var selection = this.getBundleSelection(option, selectedId);

    if(selection.confProductId != undefined) {
        var attributeSelection = selection.confProductId + '_' + selectedId;
    }

    var last = selection.confattributes[attributeSelection].items.last();

    selection.confattributes[attributeSelection].items.each(
        function (item) {
        var additionalCss = '';
        var swatchHtml = '';

        if(updateStatus.enable_swatches ==  true) {
            additionalCss = ' no-display swatch-select';

            swatchHtml = '<div class="option-swatch">' +
                '<ul id="super-option-' + optionId + '-' + item.attribute_id + '-swatch" class="configurable-swatch-list clearfix" data-last="' + last.attribute_id + '">' +
                '</ul>' +
                '</div>';
        }

        $('bundle-options-' + optionId).insert(
            {
            bottom:  '<div class="option-data">' +
            '<div class="option-label h3">' +
            item.label +
            '</div>'+
            swatchHtml +
            '<div class="option-select">' +
            '<select name="super_attribute[' + optionId + '][' + item.attribute_id + ']" id="super-option-' + optionId + '-' + item.attribute_id + '" class="required-entry' + additionalCss + '">' +
            '<option value="0">Select a value...</option>' +
            '</select>' +
            '</div>' +
            '</div>'
            }
        );

        // No need to get a new attribute after the last one
        if(item.attribute_id != last.attribute_id) {
            $('super-option-' + optionId + '-' + item.attribute_id).addEventListener(
                'change', function () {
                bundle.updateAttributes(option, optionId, selectedId, item) }, false
            );
        } else {
            $('super-option-' + optionId + '-' + item.attribute_id).addEventListener(
                'change', function () {
                bundle.getProductInformation(option, optionId, selectedId, item.attribute_id); } , false
            );
        }

        }
    );
};

Product.Bundle.prototype.resolveAttributeId = function (name, option_id) {
    return name.replace("super-option-" + option_id + "-", "");
};
