
jQuery(function($) {
    var MYplayer;
    var Player = (function () {
        //private static
        var defaults = {
            events: {},
            playerVars: {
                modestbranding: 0,
                controls: 1, //remove controls
                showinfo: 0,
                enablejsapi: 1,
                iv_load_policy: 3
            }
        };

        var constructor = function (options) {
            this.options = _.extend(defaults, options);

            if (this.options.autoPlay) {
                this.options.events['onReady'] = function (event) {
                    event.target.playVideo()
                }
            }
            this.player = new YT.Player(this.options.id, this.options);
            MYplayer = this.player;
        }

        return constructor;
    })() //function(){
    $(document).ready(function () {
        var didScroll;
        var lastScrollTop = 0;
        var delta = 5;
        var st;
        var navbarHeight = $('#navigation').outerHeight();
        var activateSticky = $('#navigation').outerHeight() + 100;
        // $('body').css('padding-top',navbarHeight);

        $(window).scroll(function(event){
            didScroll = true;
            st = $(this).scrollTop();
        });

        setInterval(function() {
            if (didScroll) {
                hasScrolled();
                didScroll = false;
            }
        }, 250);

        function hasScrolled() {

            // scrolled passed the height of header
            setTimeout(function () {
                if (st > activateSticky) {
                    $('#navigation').addClass("sticky-active");
                } else {
                    $('#navigation').removeClass("sticky-active");
                }
            },300);

            // Make scroll more than delta
            if(Math.abs(lastScrollTop - st) <= delta)
                return;

            // If scrolled down and past the navbar, add class .nav-up.
            if (st > lastScrollTop && st > navbarHeight){
                // Scroll Down
                $('#navigation').removeClass('nav-up').addClass('nav-down');
            } else {
                // Scroll Up
                if(st + $(window).height() < $(document).height()) {
                    $('#navigation').removeClass('nav-down').addClass('nav-up');
                }
            }
            if (st==0) {
                    $('#navigation').removeClass('nav-down').removeClass('nav-up');
            }
            lastScrollTop = st;
        }

        $('#videoIframe').click(function (){
            var ytbId = $(this).find('img').fadeOut(1000).attr('data-videoId');

            myPlayer = new Player({
                id: 'videoIframeInner',
                changeVideo: '.videoGal',
                autoPlay: true,
                videoId: ytbId
            });
        });
        $('.videoGal img').click(function () {
            var ytbId = $(this).parents('.videoGal').attr('data-videoId');
            if (MYplayer) {
                MYplayer.loadVideoById(ytbId);
            }else {
                $('#videoIframe img').fadeOut(1000);
                myPlayer = new Player({
                    id: 'videoIframeInner',
                    changeVideo: '.videoGal',
                    autoPlay: true,
                    videoId: ytbId
                });
            }
        });
        var $casmess = "";
        if ($('html').attr('lang') == 'fr-FR') {

            $.extend($.validator.messages, {
                required: "Ce champ est obligatoire.",
                remote: "Veuillez corriger ce champ.",
                email: "Veuillez fournir une adresse électronique valide.",
                url: "Veuillez fournir une adresse URL valide.",
                date: "Veuillez fournir une date valide.",
                dateISO: "Veuillez fournir une date valide (ISO).",
                number: "Veuillez fournir un numéro valide.",
                digits: "Veuillez fournir seulement des chiffres.",
                creditcard: "Veuillez fournir un numéro de carte de crédit valide.",
                equalTo: "Veuillez fournir encore la même valeur.",
                notEqualTo: "Veuillez fournir une valeur différente, les valeurs ne doivent pas être identiques.",
                extension: "Veuillez fournir une valeur avec une extension valide.",
                maxlength: $.validator.format("Veuillez fournir au plus {0} caractères."),
                minlength: $.validator.format("Veuillez fournir au moins {0} caractères."),
                rangelength: $.validator.format("Veuillez fournir une valeur qui contient entre {0} et {1} caractères."),
                range: $.validator.format("Veuillez fournir une valeur entre {0} et {1}."),
                max: $.validator.format("Veuillez fournir une valeur inférieure ou égale à {0}."),
                min: $.validator.format("Veuillez fournir une valeur supérieure ou égale à {0}."),
                step: $.validator.format("Veuillez fournir une valeur multiple de {0}."),
                maxWords: $.validator.format("Veuillez fournir au plus {0} mots."),
                minWords: $.validator.format("Veuillez fournir au moins {0} mots."),
                rangeWords: $.validator.format("Veuillez fournir entre {0} et {1} mots."),
                letterswithbasicpunc: "Veuillez fournir seulement des lettres et des signes de ponctuation.",
                alphanumeric: "Veuillez fournir seulement des lettres, nombres, espaces et soulignages.",
                lettersonly: "Veuillez fournir seulement des lettres.",
                nowhitespace: "Veuillez ne pas inscrire d'espaces blancs.",
                ziprange: "Veuillez fournir un code postal entre 902xx-xxxx et 905-xx-xxxx.",
                integer: "Veuillez fournir un nombre non décimal qui est positif ou négatif.",
                vinUS: "Veuillez fournir un numéro d'identification du véhicule (VIN).",
                dateITA: "Veuillez fournir une date valide.",
                time: "Veuillez fournir une heure valide entre 00:00 et 23:59.",
                phoneUS: "Veuillez fournir un numéro de téléphone valide.",
                phoneUK: "Veuillez fournir un numéro de téléphone valide.",
                mobileUK: "Veuillez fournir un numéro de téléphone mobile valide.",
                strippedminlength: $.validator.format("Veuillez fournir au moins {0} caractères."),
                email2: "Veuillez fournir une adresse électronique valide.",
                url2: "Veuillez fournir une adresse URL valide.",
                creditcardtypes: "Veuillez fournir un numéro de carte de crédit valide.",
                ipv4: "Veuillez fournir une adresse IP v4 valide.",
                ipv6: "Veuillez fournir une adresse IP v6 valide.",
                require_from_group: "Veuillez fournir au moins {0} de ces champs.",
                nifES: "Veuillez fournir un numéro NIF valide.",
                nieES: "Veuillez fournir un numéro NIE valide.",
                cifES: "Veuillez fournir un numéro CIF valide.",
                postalCodeCA: "Veuillez fournir un code postal valide."
            });
            $casmess = "Vous devez sélectionner au moins un choix";

        }else {
            $casmess = "You must select at least one choice";
        }

        // pour chaque formulaire mettre une validation pour les champs obligatoirs
        $('.MailjetForm input[name="SubscriberEmail"]').focus(function(){
            $('label[for="rgpd"]').show();
        });
        $.validator.setDefaults({
            ignore: []
        });
        $('form#searchform').validate({
            rules: {
                's': {
                    required: true,
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        $('form#commentform').validate({
            rules: {
                'comment': {
                    required: true,
                },
                'author': {
                    required: true,
                },
                'email': {
                    required: true,
                    email: true,
                },
                'wp-comment-gdpr': {
                    required: true,
                },
            },
            submitHandler: function (form) {
                if (grecaptcha.getResponse()) {
                    form.submit();
                } else {
                    // 1) Before sending we must validate captcha
                    grecaptcha.reset();
                    grecaptcha.execute();
                }
            },
            // success: function (label) {
            //     if (label.attr('for') == 'email') {
            //         $mess = "";
            //         if ($('html').attr('lang') == 'fr-FR') {
            //             $mess = "Adresse électronique valide"
            //         } else {
            //             $mess = "Valid email address"
            //         }
            //         label.addClass("valid").text($mess);
            //     }
            // },
        });
        $('.SendA').on('click', function(){
             if( $('.MailjetForm').valid() ) {

                $.ajax({
                        type: 'POST',
                        url: "/blog/wp/wp-admin/admin-ajax.php",
                        data: {
                            action: 'SendConfirmationMail',
                            email: $('input[name="SubscriberEmail"]').val(),
                            offres_promo: ($('input[name="offres_promo"]').is(":checked")) ? $('input[name="offres_promo"]').val() : false ,
                            infos_et_news: ($('input[name="infos_et_news"]').is(":checked")) ? $('input[name="infos_et_news"]').val() : false,
                            recettes: ($('input[name="recettes"]').is(":checked")) ? $('input[name="recettes"]').val() : false,
                        },
                        success: function (data) {
                            var response = $.parseJSON(data);
                            if (response['status']=="done"){
                                $('.NewsletterMailjet .doneNewsletter').remove();
                                $('.NewsletterMailjet').append('<p class="doneNewsletter">' + response['message']+'</p>');
                                $('.NewsletterMailjet form').trigger("reset");
                                $('.NewsletterMailjet form label.valid').remove();
                            }
                            $("html, body").animate({ scrollTop: $('.doneNewsletter').offset().top-340 }, 1000);

                            // GTM Event
                            window.dataLayer = window.dataLayer || [];

                            window.dataLayer.push({
                                'event' : 'inscription-newsletter'
                            });
                        }
                    });
             }else {
                if ($('label[for="rgpd"]').css('display') == 'none') {
                    $('.error[for="rgpd"]').hide();
                }
             }
        })
        $('.close-search').click(function () {
            $('#searchform input[name="s"]').val('');
        });
        $('.MailjetForm').change(function(){
            if ($("#recettes").is(":checked") || $("#offres_promo").is(":checked") || $("#infos_et_news").is(":checked")) {
                $('.error[for="offres_promo"]').remove();
            }
        })
        $('.MailjetForm').submit(function(e) {
            e.preventDefault();
        }).validate({

            // ...your validation rules come here,
            rules: {
                'rgpd':{
                    required: true,
                },
                'SubscriberEmail': {
                    required: true,
                    email: true
                },
                'offres_promo': {
                    required: {
                        depends: function (element) {
                            if ($("#recettes").is(":checked") || $("#infos_et_news").is(":checked")) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                },
            },
            messages: {
                'offres_promo': $casmess,
            },
            // success: function (label) {
            //     if ( label.attr('for') == 'SubscriberEmail' ){
            //         $mess = "";
            //         if ($('html').attr('lang') == 'fr-FR') {
            //             $mess = "Adresse électronique valide"
            //         }else {
            //             $mess = "Valid email address"
            //         }
            //         label.addClass("valid").text($mess);
            //     }
            // },
            submitHandler: function (form) {
                if (grecaptcha.getResponse()) {

                }else{
                        // 1) Before sending we must validate captcha
                    grecaptcha.reset();
                    grecaptcha.execute();
                }
            },
            errorPlacement: function(error, element) {

                if (element.attr('type')=='checkbox')
                {
                    error.insertAfter($(element).parents('.form-group')); //So i putted it after the .form-group so it will not include to your append/prepend group.
                }
                else
                {
                    error.insertAfter(element);
                }
            },
        });
    });
});
