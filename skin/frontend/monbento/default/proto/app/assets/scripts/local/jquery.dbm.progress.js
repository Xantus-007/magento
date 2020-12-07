/**
 * jquery.dbm.progress.js
 * contain progress js
 */
var progress;

(function($) {
    progress = {
        redrawGlobal : false,
        redrawSingle : false,

        init : function() {
            this._initProgress();
            this._waypointJs();
        },

        _initProgress : function() {
            $('.js-progress').circleProgress({
                emptyFill: '#f4849a',
                size: 180,
                startAngle: -Math.PI / 2
            });

            $('.js-progress--alone').circleProgress({
                emptyFill: '#fff',
                size: 150,
                startAngle: -Math.PI / 2
            });
        },

        _waypointJs : function() {
            var self = this;

            new Waypoint.Inview({
                element : $('.js-progress')[0],
                enter: function() {                                    
                    if (!self.redrawGlobal) {
                        $('.js-progress').circleProgress('redraw');
                        self.redrawGlobal = true;
                    }
                }
            });

            new Waypoint.Inview({
                element : $('.js-progress--alone')[0],
                enter: function() {
                    if (!self.redrawSingle) {
                        $('.js-progress--alone').circleProgress('redraw');
                        self.redrawSingle = true;
                    }
                }
            });
        }
    }

    $(document).ready(function() {
        progress.init();
    });
})(jQuery);
