var cartMessage;

(function($) {

    cartMessage = {
        addCartGiftMessageDelay: function() {
            var delay = (function(){
                var timer = 0;
                return function(callback, ms){
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();

            $(document).on('keyup', '#gift-message-whole-message', function() {
                updateMessage();
            });
            
            $(document).on('click', '#allow_gift_messages_for_order', function() {
                updateMessage();
            });
            
            function updateMessage()
            {
                var data = $('.gift-messages-form').serialize();
                var url = '/dbmAjaxAddToCart/product/addGiftMessageOrder/';

                delay(function(){
                    try {
                        jQuery.ajax({
                            url: url,
                            dataType: 'json',
                            type : 'post',
                            data: data
                        });
                    } catch (e) {
                        console.log('error add custom_message order');
                    }
                }, 500);
            };
        }
    };

    $(document).ready(function() {
        cartMessage.addCartGiftMessageDelay();
    });
})(jQuery);