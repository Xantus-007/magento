/**
 * jquery.dbm.about.js
 * contain about page js
 */
var about;

(function($) {
    about = {
        $sidebar    : $('.js-sidebar'),
        $nav        : $('.o-nav'),
        $aboutLink  : $('li:first > a', this.$nav),

        init : function() {
            if ($('.js-sidebar').length > 0) {
                this._enquire();
                this._eventHandler();
            }
        },

        _eventHandler : function() {

        },

        _enquire : function() {
            var self = this;

            enquire.register("screen and (max-width: 40em)", {
                match : function() {
                    self._moveIntoNav();
                },
                unmatch : function() {
                    self._moveOutsideNav();
                }
            });
        },

        _moveIntoNav : function() {
            var self = this;


            self.$aboutLink.addClass('has-subMenu');

            // Add nav show icon
            self.$aboutLink.after(self._getNavIcon('.c-nav__show'));

            // Insert sidebar menu into navigation
            self.$aboutLink.closest('li').find('.c-nav__show').after($('ul', self.$sidebar).addClass('c-nav__level1'));

            // Add nav hide icon
            self.$aboutLink.closest('li').find('ul').prepend(self._getNavIcon('.c-nav__hide'));
        },

        _moveOutsideNav : function() {
            var self = this;

            // Remove nav icons
            self.$aboutLink.closest('li').find('.c-nav__show').remove();
            self.$aboutLink.closest('li').find('.c-nav__hide').remove();

            // Add ul into initial sidebar
            self.$sidebar.append(self.$aboutLink.next('ul'));

            // Remove useless class
            $('ul', self.$sidebar).removeClass('c-nav__level1');
        },

        _getNavIcon : function(icon) {
            var self = this;

            var $icon = $(icon + ':first', self.$nav);

            return $icon.clone();
        }
    };

    $(document).ready(function() {
        about.init();
    });
})(jQuery);
