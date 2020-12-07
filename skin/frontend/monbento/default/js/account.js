/**
 * account.js
 * contain account js
 */
var accountApp;

(function ($) {
    accountApp = {
        init: function () {
            this._edit();
        },
        _edit: function(){
            if($('.form-account-edit #change_password').length){
                $('#change_password').change(function(){
                    if(this.checked)
                        $('#hidden-password').removeClass('hide');
                    else
                        $('#hidden-password').addClass('hide');
                });
            }
        }
    };
    
    $(document).ready(function () {
        accountApp.init();
    });
})(jQuery);
