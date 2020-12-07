/**
 * jquery.dbm.form.js
 * contain form js
 */
var form;

(function($) {
    form = {
        init: function() {
            this._updateDOM();
            this._eventHandler();
            this._initialState();
            this._file();
        },

        _initWindowLoaded: function() {
            this._updateDOMwindowLoaded();
        },

        _eventHandler: function() {
            var self = this;

            $('.js-label__animate').on('click.dbm', function() {
                self._addFocusClass($(this));
                self._addFocusState($(this));
            });

            $('.js-label__animate').find('input:not(.js-exclude), textarea').on('focus.dbm', function() {
                var parent = $(this).parents('.js-label__animate').first();

                self._addFocusClass(parent);

            });

            $('.js-label__animate').find('input:not(.js-exclude), textarea').on('blur.dbm', function() {
                if(!self._hasContent($(this)))
                {
                    if (!$(this).closest('.c-input__holder').hasClass('is-file') && !$(this).attr('placeholder')) {
                        self._removeFocus($(this));
                    }
                }
            });
        },

        _updateDOM: function() {
            // addClass to wrapper
            $('.form-list').addClass('c-fieldset');

            $.each($('form').find('.input-box:not(.customer-dob)'), function() {
                // remove .field
                if($(this).parent().hasClass('field'))
                {
                    $(this).unwrap();
                }

                // wrap label + input
                $(this).prev('label').andSelf().wrapAll('<div class="c-input__holder js-label__animate" />');

                if($(this).find('textarea').length)
                {
                    $(this).parents('.c-input__holder').addClass('is-textarea');
                }
            });

            $.each($('label.required'), function() {
                $(this).find('em').appendTo($(this));
            });
        },

        _updateDOMwindowLoaded: function() {
            var self = this;

            $.each($('.c-input__holder').find('input'), function() {
                if($(this).val().length > 0) {
                    var $el = $(this).parents('.c-input__holder').first();
                    self._addFocusClass($el);
                }
            });
        },

        _initialState: function() {
            var self =  this;

            // set focus on input
            $.each($('.js-label__animate').find('input, textarea'), function() {
                if(self._hasContent($(this)))
                {
                    self._addFocusClass($(this).parents('.c-input__holder'));
                }
            });

            // set focus on select
            $.each($('.c-input__holder').find('select'), function() {
                $(this).parents('.c-input__holder').addClass('is-focus');
            });
        },

        _addFocusClass: function($el) {
            if ($el.find('input, textarea').not('.not-focus').length > 0) {
                $el.addClass('is-focus');
            }
        },

        _addFocusState: function($el) {
            $el.find('input, textarea').focus();
        },

        _removeFocus: function($el) {
            $el.not('.not-focus').parents('.js-label__animate').removeClass('is-focus');
        },

        _hasContent: function($el) {
            var value = $el.val();

            if(value.length !== 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        },

        _file : function() {
            $('input[type="file"]').each(function(){
                $(this).on('change', function(){
                    var newLabel = $(this).val();

                    if (newLabel.length > 35) {
                        newLabel = newLabel.substr(0, 35) + '...';
                    }

                    $(this).closest('.c-input__text').find('input[type="text"]').val(newLabel);

                    if (newLabel.length > 0) {
                        $(this).closest('.c-input__holder').addClass('is-focus');
                    } else {
                        $(this).closest('.c-input__holder').removeClass('is-focus');
                    }
                });
            });
        }
    };

    $(document).ready(function() {
        form.init();
    });

    $(window).load(function() {
        form._initWindowLoaded();
    });
})(jQuery);
