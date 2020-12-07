/**
 * jquery.dbm.goToTop.js
 * contain goToTop js
 */
var goToTop;

(function($){
    goToTop = {
        $goToTop: $('.c-action__gototop'),

        init: function()
        {
            this._eventHandler();
            this._waypoints();
        },

        _eventHandler: function()
        {
            var self = this;

            this.$goToTop.on('click', function() {
                self._goToTop();
            });
        },

        _waypoints: function()
        {
            var self = this;

            $('body').waypoint({
                handler: function () {
                    self.$goToTop.toggleClass('is-visible');
                },
                offset: -85
            });
        },

        _goToTop: function()
        {
            this.$goToTop.dbmScrollto({
                scrollTarget: $('body'),
                scrollSpeed: 400
            });
        }
    };

    $(document).ready(function() {
        goToTop.init();
    });
})(jQuery);
