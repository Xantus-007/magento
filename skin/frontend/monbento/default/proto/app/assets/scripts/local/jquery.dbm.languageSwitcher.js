/**
 * jquery.dbm.languageSwitcher.js
 * contain languageSwitcher js
 */
var languageSwitcher;

(function($) {
    languageSwitcher = {
        init: function() {
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.js-close__languageSwitcher').on('click.dbm', function(ev) {
                ev.preventDefault();
                self._toggleContent('close');
            });

            $('.js-open__languageSwitcher').on('click.dbm', function(ev) {
                ev.preventDefault();

                if($(this).hasClass('is-active')) {
                    $('.dbm_country_switcher.overlay').fadeIn();
                    $('.dbm_country_switcher.popup').fadeIn();
                }

                self._toggleContent('open');
            });

            $('.c-languageSwitcher a.switch_link').on('click.dbm', function(ev) {
                ev.preventDefault();
                var disabledClassName = 'disabled';
                var $link = $(this);

                if (!$link.hasClass(disabledClassName))
                {
                    $('.dbm_country_switcher.overlay').fadeIn();
                    $('.dbm_country_switcher.popup').fadeIn();
                }
            });
        },

        _toggleContent: function(action) {
            switch(action) {
                case 'close':
                    $('.c-languageSwitcher').removeClass('is-open');
                    $('.c-stickyNav').removeClass('has-languageSwitcherOpen');
                    $('.js-open__languageSwitcher').removeClass('is-active');
                    $('.c-wrapper__offcanvas').removeClass('is-expand');
                    break;

                case 'open':
                    $('.c-languageSwitcher').addClass('is-open');
                    $('.c-stickyNav').addClass('has-languageSwitcherOpen');
                    $('.js-open__languageSwitcher').addClass('is-active');
                    $('.c-wrapper__offcanvas').addClass('is-expand');
                    break;

                default:
                    return false;
            }
        }
    };

    $(document).ready(function() {
        languageSwitcher.init();
    });
})(jQuery);
