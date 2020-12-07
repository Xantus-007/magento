/**
 * jquery.dbm.custom.app.js
 * contain customApp js
 */
var customApp;

(function($) {
    customApp = {
        $customerSelection: {},
        basePrice: basePrice,
        currency: currency,
        currentPriceOpts: [],
        bundleOptions: bundleOptions,
        slider: {},

        init: function() {
            this._eventHandler();
            this._bxSlider();
            //this._buildPrice(this.basePrice);
        },

        _eventHandler: function() {
            var self = this;

            // display options
            $('.js-custom__option').on('click.dbm', function(ev) {
                ev.preventDefault();
                self._displayOptions($(this));
            });

            // click on option
            $('.c-personnalize').on('click.dbm', '.c-personnalize__controler__option__content__list .js-bundle__option', function (ev) {
                ev.preventDefault();
                self._updatePrice($(this));
                self._updateActiveStepOption($(this));
                self._changeCustomImage($(this).attr('data-optionId'), jQuery.parseJSON(decodeURIComponent($(this).attr('data-images'))));
                self._updateLinks($(this).attr('data-optionId'), $(this).attr('data-productId'));
            });

            // cancel current option
            $('.c-personnalize').on('click.dbm', '.js-personnalize__cancel', function(ev) {
                ev.preventDefault();
                self._hideOptions(1);
            });

            // get random bento
            $('.c-personnalize').on('click.dbm', '.js-controlsPersonnalize__shuffle', function(ev) {
                ev.preventDefault();
                self._updateBento('random');
            });

            // reset bento
            $('.c-personnalize').on('click.dbm', '.js-controlsPersonnalize__reset', function(ev) {
                ev.preventDefault();
                self._updateBento('reset');
            });

            // todo
            $('.js-personnalize__submit').on('click.dbm', function(ev) {
                self._checkSelection(ev);
            });

            $('body').on('custombento:viewloaded', function(ev) {
                self._preloadCustomImagesAndLinks();
            });
        },

        _bxSlider: function() {
            var self = this;
            self.slider = $('.c-personnalize__product .js-bxSlider').bxSlider({
                auto: false,
                speed: 600,
                pager: true,
                infiniteLoop: true,
                hideControlOnEnd: true,
                touchEnabled: true,
                easing: 'cubic-bezier(.7, 0, .175, 1)',
                onSliderLoad: function() {
                    $('.c-personnalize__product .js-bxSlider>div').eq(1).addClass('active-slide');
                    self._preloadOptions();
                },
                onSlideBefore: function($slideElement, oldIndex, newIndex) {
                    jQuery('.c-personnalize__product__item').removeClass('active-slide');
                    $slideElement.addClass('active-slide');
                },
                onSlideAfter: function() {
                    self._updateLinks(false, false);
                }
            });
        },

        _displayOptions: function($el) {
            var optionId = $el.attr('data-optionId'),
                options = this._getOptions(optionId),
                imgHeader = $el.find('img').attr('src'),
                html = this._generateOptions(optionId, options, imgHeader);

            $('.c-personnalize__controler__list').addClass('is-hidden');
            $(html).insertBefore('.c-personnalize__controler__action');

            this._updateAction(2);
        },

        _hideOptions: function(step) {
            this._updateAction(step);
            $('.c-personnalize__controler__list').removeClass('is-hidden');
            $('.c-personnalize__controler__option').remove();
        },

        _getOptions: function(id) {
            var options = this.bundleOptions[id];
            return options;
        },

        _generateOptions: function(id, data, imgHeader) {
            var self = this;

            var html  = '<div class="c-personnalize__controler__option" data-optionId="'+id+'">';
                html += '<div class="c-personnalize__controler__option__header js-personnalize__cancel">';
                html += '<img src="'+imgHeader+'" title="">';
                html += '</div>';
                html += '<div class="c-personnalize__controler__option__content">';

            $.each(data.configurableOptions, function(index) {
                html += '<div class="c-personnalize__controler__option__group">';

                if($(this)[0].products.length > 0) {
                    label = typeof($(this)[0].label) == 'undefined' ? defaultLabelColor : $(this)[0].label;
                    html += '<div class="c-personnalize__controler__option__content__title">'+label+'</div>';
                }

                html += '<ul class="c-personnalize__controler__option__content__list">';

                $.each($(this)[0].products, function() {
                    if($(this)[0].isActive) {
                        html += '<li class="is-active">';
                    } else {
                        html += '<li>';
                    }

                    html += '<a href="" class="js-bundle__option bundle-option-'+id+'-'+$(this)[0].productId+'" data-optionId="'+id+'" data-productId="'+$(this)[0].productId+'" data-priceModifier="'+$(this)[0].priceOption+'" data-images="'+encodeURIComponent(JSON.stringify($(this)[0].images))+'">';
                    html += '<span class="c-color" style="background: ';
                    if($(this)[0].imageMotif.charAt(0) == '#')
                    {
                        html += $(this)[0].imageMotif;
                    }
                    else
                    {
                        html += 'url('+$(this)[0].imageMotif+')';
                    }
                    html += ';"></span>';
                    html += '<span class="c-label">';
                    html += $(this)[0].color;

                    if($(this)[0].priceOption !== 0) {
                        html += '<small class="c-price">';
                        html += '+'+ $(this)[0].priceOption + self.currency;
                        html += '</small>';
                    }

                    html += '</span>';
                    html += '</a>';
                    html += '</li>';
                });

                html += '</ul>';
                html += '</div>';
            });

            html += '</div>';
            html += '</div>';

            return html;
        },

        _updateActiveStepOption: function($el) {
            $el.parents('.c-personnalize__controler__option').find('li').removeClass('is-active');
            $el.parents('li').addClass('is-active');

            this._updateBundleOptions($el);
        },

        _updateBundleOptions: function($el) {
            var optionId = $el.attr('data-optionId'),
                productId = $el.attr('data-productId');

            _.forEach(this.bundleOptions[optionId].configurableOptions, function(type) {
                _.forEach(type.products, function(product) {
                    if(parseInt(product.productId) === parseInt(productId)) {
                        product.isActive = true;
                    } else {
                        product.isActive = false;
                    }
                });
            });
        },

        _updateAction: function(step) {
            switch (step) {
                case 1:
                default:
                    $('.c-step--01').removeClass('is-hidden');
                    $('.c-step--02').addClass('is-hidden');
                    break;
                case 2:
                    $('.c-step--01').addClass('is-hidden');
                    $('.c-step--02').removeClass('is-hidden');
                    break;
            }
        },

        _preloadOptions: function(init) {
            for(var x=1; x<=nbViews; x++)
            {
                $('.c-personnalize__product__item:not(.bx-clone):eq(' + (x-1) + ') .c-img__holder').append('<div id="custombento-view' + x + '"></div');
            }

            _.forEach(this.bundleOptions, function(options, optionId) {
                for(var x=1; x<=nbViews; x++)
                {
                    $('#custombento-view' + x).append('<div class="element" id="element-view' + x + '-optionid-' + optionId + '"></div>');
                    if(x == nbViews) $('body').trigger('custombento:viewloaded');
                }
            });
        },

        _preloadCustomImagesAndLinks: function() {
            var self = this,
                priceUpdate = 0;

            _.forEach(this.bundleOptions, function(options, optionId) {
                _.forEach(options.configurableOptions, function(matieres, matiereId) {
                    $.each(matieres.products, function(index, product) {
                        if(product.isActive)
                        {
                            self._changeCustomImage(optionId, product.images);
                            self.$customerSelection[optionId] = product.productId;
                            if(product.priceOption != 0)
                            {
                                self.currentPriceOpts[optionId] = parseFloat(product.priceOption);
                                var _value = product.priceOption == undefined ? 0 : product.priceOption;
                                priceUpdate =  priceUpdate + _value;
                            }
                        }
                    });
                    if(typeof self.$customerSelection[optionId] === "undefined")
                    {
                        self.$customerSelection[optionId] = "undefined";
                    }
                });
            });
            self._updateLinks(false, false);

            var newPrice = self.basePrice + priceUpdate;
            setTimeout(self._buildPrice(newPrice), 100);

            $('.c-personnalize__product').removeClass('is-loading');
        },

        _changeCustomImage: function(optionId, images) {
            if(false == Array.isArray(images))
            {
                images = images.split(',');
            }

            for(var x=1; x<=nbViews; x++)
            {
                var image = images[(x-1)];
                $('#element-view' + x + '-optionid-' + optionId)
                        .attr('data-value-id', image.value_id)
                        .css('background-image', 'url(' + image.url + ')');
            }
        },

        _updateLinks: function(optionId, productId) {
            if(optionId !== false && productId !== false) {
                this.$customerSelection[optionId] = productId;
            }
            
            if (jQuery.isEmptyObject(this.slider)) {
                var bundleSlide = 0;
            } else {
                var bundleSlide = this.slider.getCurrentSlide();
            }
            urlParams = '';
            urlParamsBuy = '';

            _.forEach(this.$customerSelection, function(productId, optionId) {
                urlParams = urlParams + 'bundle-option-' + optionId + '/' + productId + '/';
                urlParamsBuy = urlParamsBuy + 'bundle-option-' + optionId + '=' + productId + '&';
            });
            
            var imageIds = [];
            var $imageHolders = jQuery('.c-personnalize__product .js-bxSlider .active-slide .c-img__holder div.element');
            $imageHolders.each(function(){
                if(jQuery(this).attr('data-value-id')) {
                    imageIds.push(jQuery(this).attr('data-value-id'));
                }
            });
            
            var baseImg = jQuery('.c-personnalize__product .js-bxSlider .active-slide .c-img__holder>img').attr('data-value-id');

            $('.js-personnalize__submit').attr('href', baseBuyLink + '?' + urlParamsBuy);
            $('.js-controlsPersonnalize__download').attr('href', baseDownloadLink + 'base-img/' + baseImg + '/image-ids/' + imageIds.toString());

            history.replaceState({}, this.title || '', currentCustomizeUrl + urlParams);

            $.ajax({
                type: "POST",
                url: baseShareImageLink + urlParams
            }).done(function (msg) {
                $('meta[property="og:image"]').attr('content', msg);
                var customHistoryName = new Date();
                window.history.pushState('updateBentoPerso-'+customHistoryName.getTime(), 'addthis', window.location);
            });
        },

        _updatePrice: function($el) {
            var optionId = $el.attr('data-optionId'),
                priceModifier = $el.attr('data-priceModifier'),
                priceUpdate = 0;

            this.currentPriceOpts[optionId] = parseFloat(priceModifier);

            _.forEach(this.currentPriceOpts, function(value) {
                var _value = value == undefined ? 0 : value;
                priceUpdate =  priceUpdate + _value;
            });

            var newPrice = this.basePrice + priceUpdate;
            this._buildPrice(newPrice);
        },

        _buildPrice: function(price) {
            var priceHTML = this._formatPriceFloat(price) + ' ' + this.currency;
            $('.c-personnalize__price').html(priceHTML);
        },

        _formatPriceFloat: function(price) {
            return price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
        },

        _updateBento: function(action) {
            var self = this;

            switch (action) {
                case 'reset':
                    var _getBundle = baseResetUrl;
                    break;
                case 'random':
                    var _getBundle = baseRandomUrl;
                    break;
                default:
                    return false;
            }

            $.ajax({
                url: _getBundle,
                crossDomain: true,
                method: 'ajax',
                beforeSend: function() {
                    $('.c-personnalize__product').addClass('is-loading');
                },
                success: function(data) {
                    self.bundleOptions = $.parseJSON(data);
                    for(var x=1; x<=nbViews; x++)
                    {
                        $('#custombento-view' + x).empty();
                    }
                    _.forEach(self.bundleOptions, function(options, optionId) {
                        for(var x=1; x<=nbViews; x++)
                        {
                            $('#custombento-view' + x).append('<div class="element" id="element-view' + x + '-optionid-' + optionId + '"></div>');
                        }
                    });
                    self._preloadCustomImagesAndLinks();
                    self._updateCurrentActive();
                    $('.c-personnalize__product').removeClass('is-loading');
                },
                error: function() {
                    return false;
                }
            });
        },

        _updateCurrentActive: function() {
            if($('.c-personnalize__controler__option').length) {
                var optionId = $('.c-personnalize__controler__option').attr('data-optionId'),
                    option = this.bundleOptions[optionId],
                    dataProduct,
                    flatOptions = [],
                    $item;

                _.forEach(option.configurableOptions, function(collection) {
                    flatOptions.push(collection.products);
                });

                dataProduct = _.find(_.flatMapDeep(flatOptions), {'isActive' : true});
                $item = $('.c-personnalize__controler__option').find('[data-productId="'+dataProduct.productId+'"]');

                this._updateActiveStepOption($item);
            }
        },

        _checkSelection: function(ev) {
            var self = this;

            var validNextStep = true;
            _.forEach(this.$customerSelection, function(productId, optionId) {
                if(typeof(productId) === "undefined" || productId === "undefined")
                {
                    if(optionId != 0) validNextStep = false;
                }
            });

            if(!validNextStep)
            {
                ev.preventDefault();
                $.magnificPopup.open({
                    closeBtnInside: true,
                    closeOnContentClick: false,
                    closeOnBgClick: true,
                    items: {
                        src: '<div id="c-magnificpopup__addtobasket" class="c-mfp__comparator zoom-anim-dialog text-center"><div class="row c-empty__modal"><div class="o-table"><div class="o-table__cell--valignMiddle"><span>' + missingOptionText + '</span></div></div></div></div>',
                        type: 'inline'
                    }
                });
            }
        }
    };

    $(document).ready(function() {
        customApp.init();
    });
})(jQuery);
