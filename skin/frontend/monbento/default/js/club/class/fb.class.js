var Club_Fb;

(function($){
Club_Fb = Klass.create({
    EVENT_SESSION_READY: 'dbm_fb_logged_in',

    init: function(){},

    initSession: function()
    {
        var self = this;

        FB.getLoginStatus(function(response){
            if(response.status === 'unknown')
            {
                self.login();
            }
            else
            {
                self.loginHandler();
            }
        });
    },

    login: function()
    {
        var self = this;

        FB.login(function(response) {
           if (response.status === 'connected') {
                self.loginHandler();
            }
        }, {scope:'email,publish_stream'});
    },

    loginHandler: function()
    {
        $(this).trigger(this.EVENT_SESSION_READY);
    }
});
})(jQuery);