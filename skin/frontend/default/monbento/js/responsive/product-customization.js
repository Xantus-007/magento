jQuery(document).ready(function($){
    if ($('#bundleoptions').length > 0 && (
            $('body').is('.product-bento-personnalise-2-etages') || 
            $('body').is('.product-customized-bento-box') || 
            $('body').is('.product-bausteinsystem-individuellen-gestaltung-original') || 
            $('body').is('.categorypath-shop-personnalisation-1-html') || 
            $('body').is('.product-bento-personalisado-2-pisos')
            )) {
        var $bundleOptionsWrapper   = $('#bundleoptions-wrapper'),
            $bundleOptions          = $('#bundleoptions'),
            $slidesContainer        = $('dl', '#product-options-wrapper');
            $productImage           = $('.product-img-box > .product-image');
        
        var slider = null;
        
        enquire.register("screen and (max-width:40em)", {
            match: function() {
                $('#options-6', $bundleOptions).wrap('<div class="options-grouped" id="options-group-6"></div>');
                $('#options-group-6').insertAfter('#options-group-5');
                
                $('#options-11', $bundleOptions).wrap('<div class="options-grouped" id="options-group-11"></div>');
                $('#options-group-11').insertAfter('#options-group-10');
                
                $bundleOptions.insertAfter($productImage);
                
                slider = $slidesContainer.bxSlider({
                    pager: false,
                    adaptiveHeight: true,
                    infiniteLoop: false
                });
            },
            unmatch: function() {
                $('#options-group-5', $bundleOptions).append($('#options-6'));
                $('#options-group-6').remove();
                
                $('#options-group-10', $bundleOptions).append($('#options-11'));
                $('#options-group-11').remove();                
                
                if (slider !== null) {
                    slider.destroySlider();
                }
                $bundleOptions.appendTo($bundleOptionsWrapper);                
            }
        });
    }
});