var account;

(function($) {
    account = {       
        menuPlacement: function() {        	
			var $sidebar 	= $('.sidebar', '.customer-account'),
				$main		= $('.col-main');		
			
			enquire.register("screen and (max-width:40em)", {
				match: function() {
					$sidebar.insertBefore($main);
				},
				unmatch: function() {
					$sidebar.insertAfter($main);
				}
			});
        }
    };

    $(document).ready(function() {
    	if ($('.sidebar', '.customer-account').length > 0) {
        	account.menuPlacement();
    	}
    });
})(jQuery);