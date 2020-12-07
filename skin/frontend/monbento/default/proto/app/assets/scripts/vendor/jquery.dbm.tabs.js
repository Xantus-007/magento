/**
 *  DBM - Tabs
 *  @author  : Axel Viger
 *  @description : insert tabs where you need them
 *  version : 1.0.0
 */

(function($){
    var methods = {
        settings : undefined,
        $item : new Array,

        /**
         * [_init : constructor]
         */
        _init : function($el, options) {
            this.settings = $.extend({
                $tabs : $el,
                tabsControler : '.c-tabs__controler',
                tabsHolder : '.c-tabs__holder',
                tabItems : '.c-tabs__item',
            }, options);

            this._buildTabs();
            this._eventHandler();
        },

        /**
         * [_eventHandler : catch click on controler]
         */
        _eventHandler : function() {
            var self = this;

            $(this.settings.tabsControler).find('a').on('click', function(ev) {
                ev.preventDefault();
                self._updateTabs($(this));
            });

            // if(window.location.hash){
            //     self._getAnchor(window.location.hash);
            // }
        },

        /**
         * [_buildTabs : build tabs on init ]
         */
        _buildTabs : function() {
            var self = this;

            /**
             * set active link
             */
            $(this.settings.$tabs.find(this.settings.tabsControler+' a')[0]).addClass('is-active');

            /**
             * [description : store tabs inside $item, set first to visible ]
             */
            this.settings.$tabs.find(this.settings.tabItems).each(function( index ) {
                switch( index )
                {
                    case 0 :
                        $(this).addClass('is-visible');
                        break;
                    default :
                        $(this).addClass('is-hidden');
                };

                self.$item[index] = $(this);
            });
        },

        /**
         * [_updateTabs : update tabs on click ]
         */
        _updateTabs : function($el) {
            var target = $el.attr('href');

            $el.addClass('is-active').siblings().removeClass('is-active');

            for (var i = 0; i < this.$item.length; i++) {
                if(this.$item[i].is($(target)))
                {
                    this.$item[i].removeClass('is-hidden').addClass('is-visible');
                }
                else
                {
                    this.$item[i].removeClass('is-visible').addClass('is-hidden');
                }
            };
        },

        /**
        * [_resetTabsActive : reset is-active on all tabs]
        */
        _resetTabsActive : function()
        {
            $(this.settings.tabsControler).find('a').removeClass('is-active');
        },

        /**
        * [_getAnchor : update tabs with url anchor]
        */
        _getAnchor : function(anchor)
        {
            var $el = $('.c-tabs').find('a[href="' + anchor + '"]');
            if($el.length != 0){
                this._resetTabsActive();
                this._updateTabs($el);
                $('html, body').animate({
                    scrollTop: $('.c-tabs').offset().top - 20
                }, 2000);
            }
        }
    };

    // jQuery plugin interface
    $.fn.dbmTabs = function(options) {
        return methods._init($(this), options);
    };

    $(document).ready(function() {
        $('.c-tabs').dbmTabs();
    });
})(jQuery);
