/**
 *  DBM - Item Equalizer
 *  @author  : Axel Viger
 *  @description : get the higher height in a item list , in order to linearise all other items's height
 */

(function($){
    $.fn.itemEqualizer = function(options){
        return this.each(function() {
            /**
             * [settings holder]
             * @$list {[type]} jquery object - List holder // default value $(this)
             * @$item {[type]} jquery object - Item holder
             * @enableResize {[type]} boolean - Enable window resize handler
             */
            var settings = $.extend({
                $list : $(this),
                $item : undefined,
                enableResize: false,
            }, options);

            /**
             * [_windowLoad : excute _setHeight once at init]
             */
            var _windowLoad = function() {
                _setHeight();
            };

            /**
             * [_windowResize : execute _setHeight after resize]
             */
            var _windowResize = function() {
                var resizeTimer;

                $(window).on('resize', function(){
                    // lock function during resize handler
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        _resetHeight();
                        _setHeight();
                    }, 250);
                });
            };

            /**
             * [_setHeight : set $item height]
             */
            var _setHeight = function() {
                var itemsHeight = [];

                // store ul height inside itemsHeight
                settings.$list.find(settings.$item).each(function( index ) {
                    itemsHeight[index] = $(this).height();
                });

                // retrieve max height
                var maxHeight = _getMaxHeight(itemsHeight);

                // set maxHeight to all other $item
                settings.$list.find(settings.$item).css({
                    height: maxHeight + 'px',
                });
            };

            /**
             * [_resetHeight : clean style attribute]
             */
            var _resetHeight = function() {
                settings.$list.find(settings.$item).removeAttr('style');
            };

            /**
             * [_getMaxHeight : get max height among all items]
             * @param  {[type]} array [items's height]
             * @return {[type]} integer 
             */
            var _getMaxHeight = function(array) {
                return parseInt( Math.max.apply( Math, array ) );
            };

            /**
             * [Constructor]
             * @options {[type]} object - hold init setting objects
             */
            _windowLoad();
            if(settings.enableResize) _windowResize();
        });
    };
})(jQuery);