jQuery.noConflict();

var monbentoKids;
var currentDownloadURL;

(function($) {
    monbentoKids = {
        /**
         * bxSlider
         * -----------------------------------------
         */
        bxSlider: {
            init: function() {
                'use strict';

                $('.rainbow_bxlider_startr').bxSlider({
                    mode: 'fade',
                    controls: false
                });
            }
        },
        /**
         * fitVids
         * -----------------------------------------
         */
        fitVids: {
            init: function() {
                'use strict';

                $('.embed_video').fitVids();
            }
        },
        /**
         * Image comparaison
         * -----------------------------------------
         */
        imgComp: {
            init: function() {
                'use strict';

                //check if the .cd-image-container is in the viewport 
                //if yes, animate it
                checkPosition($('.cd-image-container'));
                $(window).on('scroll', function(){
                    checkPosition($('.cd-image-container'));
                });
                
                //make the .cd-handle element draggable and modify .cd-resize-img width according to its position
                $('.cd-image-container').each(function(){
                    var actual = $(this);
                    drags(actual.find('.cd-handle'), actual.find('.cd-resize-img'), actual, actual.find('.cd-image-label[data-type="original"]'), actual.find('.cd-image-label[data-type="modified"]'));
                });

                //upadate images label visibility
                $(window).on('resize', function(){
                    $('.cd-image-container').each(function(){
                        var actual = $(this);
                        updateLabel(actual.find('.cd-image-label[data-type="modified"]'), actual.find('.cd-resize-img'), 'left');
                        updateLabel(actual.find('.cd-image-label[data-type="original"]'), actual.find('.cd-resize-img'), 'right');
                    });
                });
            }
        },
        
        /**
         * Get Email address
         * -----------------------------------------
         */
        getEmail: {
            init: function() {
                'use strict';
                $('.loading').hide();
                $('.error').hide();
                $('.succes').hide();
                
                if(getCookie('email')) {
                    $('.dl').attr('data-reveal-id', '');
                }
                
                $('.dl').on('click', function(){
                    currentDownloadURL = $(this).attr('data-href');
                    if($(this).hasClass('all')) currentDownloadURL = 'all';
                    _gaq.push(['_trackEvent', 'Downloads', 'PDF', '/kids/'+currentDownloadURL]);
                    if(getCookie('email')) {
                        if(currentDownloadURL == 'all') {
                            var file_list = $(this).attr("data-docs").split(";");
                            for (var i=0; i < file_list.length; i++) {
                                var file = file_list[i];
                                launchFile(file, i*500);
                            }
                        } else {
                            window.open(currentDownloadURL, "_blank");
                        }
                    }
                });
                
                $('.btn-submit').on('click', function(){
                    $('.loading').show();
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: "ajax/index.php?lang="+$('#lang').val(),
                        data: "email="+$('#email').val(),
                        success: function(msg){
                            if(msg == "success") {
                                $('.error').hide();
                                $('.succes').show();
                                document.cookie="email=ok; expires=Thu, 19 Dec 2030 12:00:00 UTC";
                                $('.reveal-modal').trigger('reveal:close');
                                $('.dl').attr('data-reveal-id', '');
                                if(currentDownloadURL == 'all') {
                                    var file_list = $(this).attr("data-docs").split(";");
                                    for (var i=0; i < file_list.length; i++) {
                                        var file = file_list[i];
                                        launchFile(file, i*500);
                                    }
                                } else {
                                    window.open(currentDownloadURL,"_blank");
                                }
                            } else {
                                $('.error').html(msg);
                                $('.error').show();
                            }
                            $('.loading').hide();
                        },
                        error: function() {
                            $('.error').show();
                            $('.loading').hide();
                        }
                    });
                });
            }
        }
    };

    $(document).ready(function() {
        'use strict';
        monbentoKids.bxSlider.init();
        monbentoKids.fitVids.init();
        monbentoKids.imgComp.init();
        monbentoKids.getEmail.init();
    });

    //Image Comp Plugin
    function checkPosition(container) {
        container.each(function(){
            var actualContainer = $(this);
            if( $(window).scrollTop() + $(window).height()*0.5 > actualContainer.offset().top) {
                actualContainer.addClass('is-visible');
            }
        });
    }

    //draggable funtionality - credits to http://css-tricks.com/snippets/jquery/draggable-without-jquery-ui/
    function drags(dragElement, resizeElement, container, labelContainer, labelResizeElement) {
        dragElement.on("mousedown vmousedown", function(e) {
            dragElement.addClass('draggable');
            resizeElement.addClass('resizable');

            var dragWidth = dragElement.outerWidth(),
                xPosition = dragElement.offset().left + dragWidth - e.pageX,
                containerOffset = container.offset().left,
                containerWidth = container.outerWidth(),
                minLeft = containerOffset + 10,
                maxLeft = containerOffset + containerWidth - dragWidth - 10;
            
            dragElement.parents().on("mousemove vmousemove", function(e) {
                leftValue = e.pageX + xPosition - dragWidth;
                
                //constrain the draggable element to move inside his container
                if(leftValue < minLeft ) {
                    leftValue = minLeft;
                } else if ( leftValue > maxLeft) {
                    leftValue = maxLeft;
                }

                widthValue = (leftValue + dragWidth/2 - containerOffset)*100/containerWidth+'%';
                
                $('.draggable').css('left', widthValue).on("mouseup vmouseup", function() {
                    $(this).removeClass('draggable');
                    resizeElement.removeClass('resizable');
                });

                $('.resizable').css('width', widthValue); 

                updateLabel(labelResizeElement, resizeElement, 'left');
                updateLabel(labelContainer, resizeElement, 'right');
                
            }).on("mouseup vmouseup", function(e){
                dragElement.removeClass('draggable');
                resizeElement.removeClass('resizable');
            });
            e.preventDefault();
        }).on("mouseup vmouseup", function(e) {
            dragElement.removeClass('draggable');
            resizeElement.removeClass('resizable');
        });
    }

    function updateLabel(label, resizeElement, position) {
        if(position == 'left') {
            ( label.offset().left + label.outerWidth() < resizeElement.offset().left + resizeElement.outerWidth() ) ? label.removeClass('is-hidden') : label.addClass('is-hidden') ;
        } else {
            ( label.offset().left > resizeElement.offset().left + resizeElement.outerWidth() ) ? label.removeClass('is-hidden') : label.addClass('is-hidden') ;
        }
    }
    
    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
        }
        return "";
    }
    
    function launchFile(file,time) {
        //alert(file);
        setTimeout(function() {
            document.location = '/kids/download.php?file='+file;
        }, time);
    }
})(jQuery);