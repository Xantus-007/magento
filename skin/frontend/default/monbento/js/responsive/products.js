jQuery(document).ready(function($){
	// Equalizer usage
	if ($('.products-grid').length > 0) {		
		var grid = document.querySelectorAll('.products-grid');
		imagesLoaded(grid, function() {
			$('.products-grid').itemEqualizer({
		        $item : $('.item'),
		        enableResize: true
		    });
		});		
	}

	// Change product name & description position
	if ($('.product-name', '.product-shop').length > 0) {
		var $productName 		= $('.product-name', '.product-shop'),
			$productDescription	= $('.short-description', '.product-shop'),
			$productStock		= $('#availability_product', '.product-shop'),
			$productQty			= $('.add-to-box', '.product-shop'),
			$imgBox				= $('.product-img-box'),
			$placeholder		= $('form > .no-display');

		enquire.register("screen and (max-width:40em)", {
	        match: function() {
	        	$productName.insertBefore($imgBox);
	        	$productDescription.insertAfter($productName);
	        	$productStock.insertBefore($productQty);
	        },
	        unmatch: function() {
	        	$productName.insertAfter($placeholder);
	        	$productDescription.insertAfter($productName);
	        	$productStock.insertAfter($productQty);
	        }
	    });
	}

});