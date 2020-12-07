/**
 *  DBM - Item Equalizer
 *  @author  : Axel Viger
 *  @description : get the higher height in a item list , in order to linearise all other items's height
 *  version : 1.2.0
 */

(function($){
    function itemEqualizer(item, options) {
        /**
         * Initialize plugin
         */
        this._setOptions(item, options);
        this._init();
    }

    itemEqualizer.prototype = {
        /**
         * [Initialize variables]
         */
        settings : undefined,

        /**
         * [Constructor]
         * @options {[type]} object - hold init setting objects
         */
        _init : function() {
            this._windowLoad();

            // enable watch on window resize default {true}
            if(this.settings.enableResize) this._windowResize();
        },

        /**
         * [_setOptions : define plugin options]
         */
        _setOptions : function(item, options) {
            /**
             * [settings holder]
             * @$list {[type]} jquery object - List holder // default value $(this)
             * @$item {[type]} jquery object - Item holder
             * @enableResize {[type]} boolean - Enable window resize handler
             * @outerHeight {[type]} boolean - Get outerHeight in place of height in case of box-sizing:border-box with padding
             */

            this.settings = $.extend({
                $list : $(item),
                item : undefined,
                enableResize: true,
                outerHeight: false,
            }, options);
        },

        /**
         * [_windowLoad : excute _setHeight once at init]
         */
        _windowLoad : function() {
            this._setHeight();
        },

        /**
         * [_windowResize : execute _setHeight after resize]
         */
        _windowResize : function() {
            var resizeTimer;
            var self = this;

            $(window).on('resize', function(){
                // lock function during resize handler
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    self._resetHeight();
                    self._setHeight();
                }, 250);
            });
        },

        /**
         * [_setHeight : set $item height]
         */
        _setHeight : function() {
            var itemsHeight = [];
            var self = this;

            // store ul height inside itemsHeight
            this.settings.$list.find($(this.settings.item)).each(function( index ) {
                if(self.settings.outerHeight)
                {
                    itemsHeight[index] = $(this).outerHeight();
                }
                else
                {
                    itemsHeight[index] = $(this).height();
                }
            });

            // retrieve max height
            var maxHeight = this._getMaxHeight(itemsHeight);

            // set maxHeight to all other $item
            this.settings.$list.find($(this.settings.item)).css({
                height: maxHeight + 'px',
            });
        },

        /**
         * [_resetHeight : clean style attribute]
         */
        _resetHeight : function() {
            this.settings.$list.find($(this.settings.item)).removeAttr('style');
        },

        /**
         * [_getMaxHeight : get max height among all items]
         * @param  {[type]} array [items's height]
         * @return {[type]} integer
         */
        _getMaxHeight : function(array) {
            return parseInt( Math.max.apply( Math, array ) );
        },

        /**
         * [Public methods]
         */
        refreshAll : function() {
            this._setHeight();
        },
    }

    // jQuery plugin interface
    $.fn.itemEqualizer = function(opt){
        var args = Array.prototype.slice.call(arguments, 1);
        return this.each(function() {
            var item = $(this), instance = item.data('itemEqualizer');
            if(!instance) {
                // create plugin instance if not created
                item.data('itemEqualizer', new itemEqualizer(this, opt));
            } else {
                // otherwise check arguments for method call
                if(typeof opt === 'string') {
                    instance[opt].apply(instance, args);
                }
            }
        });
    };
})(jQuery);
