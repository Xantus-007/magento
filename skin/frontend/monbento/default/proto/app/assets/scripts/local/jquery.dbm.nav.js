/**
 * jquery.dbm.nav.js
 * contain nav js
 */
var nav;

(function($) {
    nav = {
        $navItem0 : $('.c-nav__level0 > li'),
        $navItem1 : $('.c-nav__level1 > li'),
        $navSubDisplay : $('.c-nav__show'),
        $navSubHide : $('.c-nav__hide'),
        subMenu : '[class*="c-nav__level"]',
        bpMedium : '1024px',

        init: function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            // nav level 0
            $(this.$navItem0).on('mouseenter.dbm mouseleave.dbm', function(ev) {
                var $item = $(this);

                enquire.register("screen and (min-width: "+ self.bpMedium +")", {
                    match : function() {
                        if(self._hasSubMenu($item) && !$item.find('> a').hasClass('is-active'))
                        {
                            self._toggleSubMenu(ev, $item);
                        }
                    }
                });
            });

            // gt medium
            $(this.$navItem1).on('mouseenter.dbm mouseleave.dbm', function(ev) {
                var $item = $(this);

                enquire.register("screen and (min-width: "+ self.bpMedium +")", {
                    match : function() {
                        if(self._hasSubMenu($item))
                        {
                            self._toggleActive($item);
                            self._toggleSubMenu(ev, $item);
                        }
                    }
                });
            });

            // lt medium
            $('body').on('click.dbm', '.c-nav__show', function(ev) {
                self._displayNavSubLevel($(this));
            });

            $('body').on('click.dbm', '.c-nav__hide', function(ev) {
                self._hideNavSubLevel($(this));
            });

            // trigger swap thumb
            $(this.$navItem1).find('.c-nav__level2 a').on('mouseenter.dbm', function() {
                self._swapImageSublevel($(this));
            });

            $(document).on('CLOSE_OFFCANVAS', function() {
                self._closeAllSubLevel();
            });
        },

        _getSubMenu: function($el) {
            var $subMenu = $el.find(this.subMenu).first();

            return $subMenu;
        },

        _hasSubMenu: function($el) {
            var $subMenu = this._getSubMenu($el);

            if(!$subMenu.length)
            {
                return false;
            }
            else
            {
                return true;
            }
        },

        _toggleActive: function($el) {
            $el.find('a').first().toggleClass('is-active');
        },

        _toggleSubMenu: function(ev, $el) {
            var $subMenu = this._getSubMenu($el);

            $subMenu.toggleClass('is-visible');
            $subMenu.toggleClass('is-foreground');
        },

        _displayNavSubLevel: function($el) {
            var navSubLevel = $el.next(this.subMenu);

            navSubLevel.addClass('is-inViewport');
        },

        _hideNavSubLevel: function($el) {
            var navSubLevel = $el.parent(this.subMenu);

            navSubLevel.removeClass('is-inViewport');
        },

        _closeAllSubLevel: function() {
            this.$navSubHide.parent(this.subMenu).removeClass('is-inViewport');
        },

        /**
         * [_swapImageSublevel : change image inside mega menu on sublevel:hover ]
         * @param  {$objectKquery} $el
         */
        _swapImageSublevel : function($el) {
            var dataImg = $el.attr('data-img');
            if(dataImg && dataImg.length > 0) {
                $el.closest('.c-nav__level2--holder').find('.c-thumb img').attr('src', dataImg);
            }
        },
    };

    $(document).ready(function() {
        nav.init();
    });
})(jQuery);
