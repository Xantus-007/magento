/**
 * jquery.dbm.personnalize.js
 * contain personnalize js
 */
var personnalize;

(function($) {
    personnalize = {
        init : function() {
            this._updateDOM();
            this._slider();
            this._eventHandler();
        },

        _eventHandler: function() {
            var self = this;

            $('.js-controlsPersonnalize__shuffle').on('click', function(ev) {
                ev.preventDefault();
                self._random();
            });

            $('.js-controlsPersonnalize__reset').on('click', function(ev) {
                ev.preventDefault();
                self._reset();
            });
        },

        _slider: function() {
            var self = this;

            $('.c-slider--personnalize .js-slider').bxSlider({
                infiniteLoop: false,
                pager: false,
                mode: 'fade',
                hideControlOnEnd: 'true',
                speed: 200,
                slideSelector: '.c-item',
                touchEnabled: false,

                onSlideNext: function($slideElement, oldIndex, newIndex) {
                    self._updateColorSelector(newIndex);
                },
                onSlidePrev: function($slideElement, oldIndex, newIndex) {
                    self._updateColorSelector(newIndex);
                }
            });
        },

        _updateDOM: function() {
            // move colorSelector
            $.each($('.c-colorSelector__item'), function() {
                $(this).appendTo('.c-colorSelector');
            });

            // hide all except first
            $('.c-colorSelector__item:not(:first-child)').addClass('is-hidden');
        },

        _updateColorSelector: function(index) {
            $('.c-colorSelector__item').eq(index).removeClass('is-hidden').siblings().addClass('is-hidden');
        },

        _random: function() {
            $('.c-colorSelector').children().each(function(id, section) {
                var $section = $(section); _.sample($section.children()).click();
            });
        },

        _reset: function() {
            $('#custombento, #sticky_custombento').find('.element').removeAttr('style');
            $('.c-colorSelector').find('.bundle-color').removeClass('active');
            $('.js-controlsPersonnalize__download').attr('href', '');
        }
    };

    $(document).ready(function() {
        personnalize.init();
    });
})(jQuery);
