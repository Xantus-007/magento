var storeLocation;

(function($){
    storeLocation = {
        $icon: $('.c-storeLocator__toggle'),
        $aside: $('.c-storeLocator__list'),
        $storeLocator: $('.c-storeLocator--gmap'),
        $addressFormContainer: $('.c-storeLocator__search'),
        $addressFormField: $('.address-form'),
        $addressAsideField: $('.address-aside'),

        init: function()
        {
            this._eventHandler();
        },

        _eventHandler: function()
        {
            var self = this;

            $('body').on('click.dbm', self.$icon, function(ev)
            {
                if($(ev.target).hasClass('c-storeLocator__toggle'))
                {
                    self._toggleAside();
                }
            });

            $('#address-search').keypress(function(ev)
            {
                var key = ev.which;
                if(key == 13)  // the enter key code
                {
                    $('.submit-search').click();
                    return false;
                }
            });
/*
            $('#location-submit-from').click(function(ev) {
                ev.preventDefault;
                $('#search-location').submit();
            });*/
        },

        _toggleAside: function()
        {
            this.$storeLocator.toggleClass('is-asideOpen');
            this.$icon.find('i').toggleClass('c-fonticon__icon--cross c-fonticon__icon--shevronRight');
        },

        _searchForm: function()
        {
            if(this.$icon.hasClass('hide'))
            {
                this.$icon.removeClass('hide');
            }

            if (!this.$storeLocator.hasClass('is-asideOpen'))
            {
                this.$addressAsideField.val(this.$addressFormField.val());
                this.$addressFormField.attr('id', 'address-search_off');
                this.$addressAsideField.attr('id', 'address-search');
                this.$storeLocator.addClass('is-asideOpen');
                this.$addressFormContainer.hide();
            }
            else
            {
                this.$addressFormField.val(this.$addressAsideField.val());
            }
        },

    };

    $(document).ready(function() {
        storeLocation.init();
    });
})(jQuery);
