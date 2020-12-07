/**
 * jquery.dbm.gallery.js
 * Dependencies :
 * ImagesLoaded : https://github.com/desandro/imagesloaded
 * Magnific Popup : http://dimsemenov.com/plugins/magnific-popup
 */
var gallery;

(function($){
    gallery = {
        $gallery : $('.js-gallery'),

        _setMagnificPopup: function() {
            this.$gallery.magnificPopup({
                delegate: 'a',
                type: 'image',
                closeOnContentClick: false,
                showCloseBtn: false,
                gallery: {
                    enabled: true
                },
                zoom: {
                    enabled: true,
                    duration: 300,
                    opener: function(element) {
                        return element.find('img');
                    }
                },
                titleSrc: function(item) {
                    return item.el.attr('title');
                }
            });
        }
    };

    gallery.$gallery.imagesLoaded(function(){
        gallery._setMagnificPopup();
    });
})(jQuery);
