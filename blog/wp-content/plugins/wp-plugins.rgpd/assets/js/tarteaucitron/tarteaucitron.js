/*jslint browser: true, evil: true */

// define correct path for files inclusion
var scripts = document.getElementsByTagName('script'),
    path = scripts[scripts.length - 1].src.split('?')[0],
    cdn = path.split('/').slice(0, -1).join('/') + '/',
    alreadyLaunch = (alreadyLaunch === undefined) ? 0 : alreadyLaunch,
    tarteaucitronForceLanguage = (tarteaucitronForceLanguage === undefined) ? '' : tarteaucitronForceLanguage,
    tarteaucitronProLoadServices,
    tarteaucitronNoAdBlocker = false;

var tarteaucitron = {
    "version": 323,
    "cdn": cdn,
    "user": {},
    "lang": {},
    "services": {},
    "added": [],
    "idprocessed": [],
    "state": [],
    "launch": [],
    "parameters": {},
    "isAjax": false,
    "reloadThePage": false,
    "init": function (params) {
        "use strict";
        var origOpen;

        tarteaucitron.parameters = params;
        if (alreadyLaunch === 0) {
            alreadyLaunch = 1;
            tarteaucitron.load();
            if (window.addEventListener) {
                window.addEventListener("load", function () {
                    tarteaucitron.fallback(['tarteaucitronOpenPanel'], function (elem) {
                        elem.addEventListener("click", function () {
                            tarteaucitron.userInterface.openPanel();
                        }, false);
                    }, true);
                }, false);
                window.addEventListener("scroll", function () {
                    var scrollPos = window.pageYOffset || document.documentElement.scrollTop,
                        heightPosition;
                    if (document.getElementById('tarteaucitronAlertBig') !== null && !tarteaucitron.highPrivacy) {
                        if (document.getElementById('tarteaucitronAlertBig').style.display === 'block') {
                            heightPosition = document.getElementById('tarteaucitronAlertBig').offsetHeight + 'px';

                            if (scrollPos > (screen.height * 2)) {
                                //tarteaucitron.userInterface.respondAll(true);
                            } else if (scrollPos > (screen.height / 2)) {
                                //document.getElementById('tarteaucitronDisclaimerAlert').innerHTML = '<b>' + tarteaucitron.lang.alertBigScroll + '</b> ' + tarteaucitron.lang.alertBig;
                            }

                            if (tarteaucitron.orientation === 'top') {
                                document.getElementById('tarteaucitronPercentage').style.top = heightPosition;
                            } else {
                                document.getElementById('tarteaucitronPercentage').style.bottom = heightPosition;
                            }
                            document.getElementById('tarteaucitronPercentage').style.width = ((100 / (screen.height * 2)) * scrollPos) + '%';
                        }
                    }
                }, false);
                window.addEventListener("keydown", function (evt) {
                    if (evt.keyCode === 27) {
                        tarteaucitron.userInterface.closePanel();
                    }
                }, false);
                window.addEventListener("hashchange", function () {
                    if (document.location.hash === tarteaucitron.hashtag && tarteaucitron.hashtag !== '') {
                        tarteaucitron.userInterface.openPanel();
                    }
                }, false);
                window.addEventListener("resize", function () {
                    if (document.getElementById('tarteaucitron') !== null) {
                        if (document.getElementById('tarteaucitron').style.display === 'block') {
                            tarteaucitron.userInterface.jsSizing('main');
                        }
                    }

                    if (document.getElementById('tarteaucitronCookiesListContainer') !== null) {
                        if (document.getElementById('tarteaucitronCookiesListContainer').style.display === 'block') {
                            tarteaucitron.userInterface.jsSizing('cookie');
                        }
                    }
                }, false);
            } else {
                window.attachEvent("onload", function () {
                    tarteaucitron.fallback(['tarteaucitronOpenPanel'], function (elem) {
                        elem.attachEvent("onclick", function () {
                            tarteaucitron.userInterface.openPanel();
                        });
                    }, true);
                });
                window.attachEvent("onscroll", function () {
                    var scrollPos = window.pageYOffset || document.documentElement.scrollTop,
                        heightPosition;
                    if (document.getElementById('tarteaucitronAlertBig') !== null && !tarteaucitron.highPrivacy) {
                        if (document.getElementById('tarteaucitronAlertBig').style.display === 'block') {
                            heightPosition = document.getElementById('tarteaucitronAlertBig').offsetHeight + 'px';

                            if (scrollPos > (screen.height * 2)) {
                                tarteaucitron.userInterface.respondAll(true);
                            } else if (scrollPos > (screen.height / 2)) {
                                //document.getElementById('tarteaucitronDisclaimerAlert').innerHTML = '<b>' + tarteaucitron.lang.alertBigScroll + '</b> ' + tarteaucitron.lang.alertBig;
                            }
                            if (tarteaucitron.orientation === 'top') {
                                document.getElementById('tarteaucitronPercentage').style.top = heightPosition;
                            } else {
                                document.getElementById('tarteaucitronPercentage').style.bottom = heightPosition;
                            }
                            document.getElementById('tarteaucitronPercentage').style.width = ((100 / (screen.height * 2)) * scrollPos) + '%';
                        }
                    }
                });
                window.attachEvent("onkeydown", function (evt) {
                    if (evt.keyCode === 27) {
                        tarteaucitron.userInterface.closePanel();
                    }
                });
                window.attachEvent("onhashchange", function () {
                    if (document.location.hash === tarteaucitron.hashtag && tarteaucitron.hashtag !== '') {
                        tarteaucitron.userInterface.openPanel();
                    }
                });
                window.attachEvent("onresize", function () {
                    if (document.getElementById('tarteaucitron') !== null) {
                        if (document.getElementById('tarteaucitron').style.display === 'block') {
                            tarteaucitron.userInterface.jsSizing('main');
                        }
                    }

                    if (document.getElementById('tarteaucitronCookiesListContainer') !== null) {
                        if (document.getElementById('tarteaucitronCookiesListContainer').style.display === 'block') {
                            tarteaucitron.userInterface.jsSizing('cookie');
                        }
                    }
                });
            }

            if (typeof XMLHttpRequest !== 'undefined') {
                origOpen = XMLHttpRequest.prototype.open;
                XMLHttpRequest.prototype.open = function () {

                    if (window.addEventListener) {
                        this.addEventListener("load", function () {
                            if (typeof tarteaucitronProLoadServices === 'function') {
                                tarteaucitronProLoadServices();
                            }
                        }, false);
                    } else if (typeof this.attachEvent !== 'undefined') {
                        this.attachEvent("onload", function () {
                            if (typeof tarteaucitronProLoadServices === 'function') {
                                tarteaucitronProLoadServices();
                            }
                        });
                    } else {
                        if (typeof tarteaucitronProLoadServices === 'function') {
                            setTimeout(tarteaucitronProLoadServices, 1000);
                        }
                    }

                    try {
                        origOpen.apply(this, arguments);
                    } catch (err) {}
                };
            }
        }
    },
    "load": function () {
        "use strict";
        var cdn = tarteaucitron.cdn,
            language = tarteaucitron.getLanguage(),
            pathToLang = cdn + 'lang/tarteaucitron.' + language + '.js?v=' + tarteaucitron.version,
            pathToServices = cdn + 'tarteaucitron.services.js?v=' + tarteaucitron.version,
            defaults = {
                "adblocker": false,
                "hashtag": '#tarteaucitron',
                "highPrivacy": false,
                "orientation": "top",
                "removeCredit": false,
                "showAlertSmall": true,
                "cookieslist": true,
                "startJsOnWait": false,
                "overlayOnSpecificConsent": false,
                "allowServicesOnPageNav": false,
                "privacyUrl": ''
            },
            params = tarteaucitron.parameters;

        // Step 0: get params
        if (params !== undefined) {
            tarteaucitron.extend(defaults, params);
        }

        // global
        tarteaucitron.orientation = defaults.orientation;
        tarteaucitron.hashtag = defaults.hashtag;
        tarteaucitron.highPrivacy = defaults.highPrivacy;
        tarteaucitron.startJsOnWait = defaults.startJsOnWait;
        tarteaucitron.overlayOnSpecificConsent = defaults.overlayOnSpecificConsent;
        tarteaucitron.allowServicesOnPageNav = defaults.allowServicesOnPageNav;
        tarteaucitron.privacyUrl = defaults.privacyUrl;
        tarteaucitron.currentUrl = location.protocol + '//' + location.host + location.pathname;

        // Step 1: load css
        // Loaded via wp plugin
        
        // Step 2: load language and services
        if(language == 'fr') {
            tarteaucitron.lang = {
                "adblock": "Bonjour! Ce site joue la transparence et vous donne le choix des services tiers à activer.",
                "adblock_call": "Merci de désactiver votre adblocker pour commencer la personnalisation.",
                "reload": "Recharger la page",
                
                "presentationTitle": "Gestion de vos préférences sur les cookies",
                "presentationContent": "<p>Certaines fonctionnalités de ce site (partage de contenus sur les réseaux sociaux, statistiques) s’appuient sur des services proposés par des sites tiers.</p>"
                                       + "<p>Ces fonctionnalités déposent des cookies permettant notamment à ces sites de tracer votre navigation. Ces cookies ne sont déposés que si vous donnez votre accord.</p>"
                                       + "<p>Vous pouvez vous informer sur la nature des cookies déposés, les accepter ou les refuser soit globalement pour l’ensemble du site et l’ensemble des services, soit service par service.</p>"
                                       + "<p><b>Le refus des cookies désactive les fonctionnalités qui les utilisent et les rendent par conséquent inutilisables.</b></p>",
                "isDisabled": "est désactivé.",
                "authorizeService": "Autorisez le dépôt de cookies pour accéder à cette fonctionnalité.",
                "alertTitle": "Vos choix en matière de cookies sur ce site",
                "alertFull": "Les cookies sont importants pour le bon fonctionnement d'un site. Afin d'améliorer votre expérience, nous utilisons des cookies pour collecter les statistiques, vous proposer des vidéos et des boutons de partage. En cliquant sur \"J'accepte\", vous acceptez tous les cookies. Vous pourrez ensuite poursuivre votre navigation sur le site. Vous pouvez aussi cliquer sur \"Je personnalise\", pour consulter en détail les descriptions des types de cookies et choisir ceux que vous voulez accepter lorsque vous visitez le site.",
                "alertBigScroll": "En continuant de défiler,",
                "alertBigClick": "En poursuivant votre navigation,",
                "alertBig": "vous acceptez l'utilisation de services tiers pouvant installer des cookies",

                "alertBigPrivacy": "Ce site utilise des cookies et vous donne le contrôle sur ce que vous souhaitez activer",
                "alertSmall": "Gestion des services",
                "acceptAll": "J'accepte",
                "personalize": "En savoir plus",
                "close": "X",

                "all": "Préférence pour tous les services",

                "info": "Protection de votre vie privée",
                "disclaimer": "En autorisant ces services tiers, vous acceptez le dépôt et la lecture de cookies et l'utilisation de technologies de suivi nécessaires à leur bon fonctionnement.",
                "allow": "Autoriser",
                "deny": "Interdire",
                "noCookie": "Ce service ne dépose aucun cookie.",
                "useCookie": "Ce service peut déposer",
                "useCookieCurrent": "Ce service a déposé",
                "useNoCookie": "Ce service n'a déposé aucun cookie.",
                "more": "En savoir plus",
                "source": "Voir le site officiel",
                "credit": "Gestion des cookies par tarteaucitron.js",
                
                "fallback": "est désactivé.",

                "ads": {
                    "title": "Régies publicitaires",
                    "details": "Les régies publicitaires permettent de générer des revenus en commercialisant les espaces publicitaires du site."
                },
                "analytic": {
                    "title": "Mesure d'audience",
                    "details": "Les services de mesure d'audience permettent de générer des statistiques de fréquentation utiles à l'amélioration du site."
                },
                "social": {
                    "title": "Réseaux sociaux",
                    "details": "Les réseaux sociaux permettent d'améliorer la convivialité du site et aident à sa promotion via les partages."
                },
                "video": {
                    "title": "Vidéos",
                    "details": "Les services de partage de vidéo permettent d'enrichir le site de contenu multimédia et augmentent sa visibilité."
                },
                "comment": {
                    "title": "Commentaires",
                    "details": "Les gestionnaires de commentaires facilitent le dépôt de vos commentaires et luttent contre le spam."
                },
                "support": {
                    "title": "Support",
                    "details": "Les services de support vous permettent d'entrer en contact avec l'équipe du site et d'aider à son amélioration."
                },
                "api": {
                    "title": "APIs",
                    "details": "Les APIs permettent de charger des scripts : géolocalisation, moteurs de recherche, traductions, ..."
                }
            };
        } else {
            tarteaucitron.lang = {
                "adblock": "Hello! This site is transparent and lets you chose the 3rd party services you want to allow.",
                "adblock_call": "Please disable your adblocker to start customizing.",
                "reload": "Refresh the page",

                "presentationTitle": "Managing your preferences on cookies",
                "presentationContent": "<p>Some features of this website (content sharing on social networks, statistics) rely on services offered by third-party websites.</p>"
                        + "<p>These features deposit cookies allowing these websites to trace your navigation. These cookies are only deposited if you give your consent.</p>"
                        + "<p>You can get informations about the nature of the cookies deposited, accept or reject them either globally for the entire website and all services, or service by service.</p>",

                "alertTitle": "Your choice of cookies on this website",
                "alertFull": "Cookies are important for the proper functioning of a website. In order to improve your experience, we use cookies to collect statistics, offer you videos and share buttons. Click on OK, accept all to accept cookies and continue directly on the website or click on Personalize to view in detail the descriptions of the types of cookies and choose the ones you want to accept when you visit the website.",
                "alertFullAutomatic": "Our site uses cookies to personalize and improve your comfort of use. See our <a class=\"c-cookies__alert__text__privacyurl\" href=\"" + tarteaucitron.privacyUrl + "\">Privacy Policy</a> to learn more. To manage your personal preferences, <span class=\"c-cookies__alert__text__openpanel\" onclick=\"tarteaucitron.userInterface.openPanel();\">use our cookie acceptance tool</a>. By browsing our site, you accept the use of cookies.",
                "alertBigScroll": "By continuing to scroll,",
                "alertBigClick": "If you continue to browse this website,",
                "alertBig": "you are allowing all third-party services",
                
                "alertBigPrivacy": "This site uses cookies and gives you control over what you want to activate",
                "alertSmall": "Manage services",
                "personalize": "Personalize",
                "acceptAll": "OK, accept all",
                "close": "X",
                
                "all": "Preference for all services",

                "info": "Protecting your privacy",
                "disclaimer": "By allowing these third party services, you accept their cookies and the use of tracking technologies necessary for their proper functioning.",
                "allow": "Allow",
                "deny": "Deny",
                "noCookie": "This service does not use cookie.",
                "useCookie": "This service can install",
                "useCookieCurrent": "This service has installed",
                "useNoCookie": "This service has not installed any cookie.",
                "more": "Read more",
                "source": "View the official website",
                "credit": "Cookies manager by tarteaucitron.js",
                
                "fallback": "is disabled.",

                "ads": {
                    "title": "Advertising network",
                    "details": "Ad networks can generate revenue by selling advertising space on the site."
                },
                "analytic": {
                    "title": "Audience measurement",
                    "details": "The audience measurement services used to generate useful statistics attendance to improve the site."
                },
                "social": {
                    "title": "Social networks",
                    "details": "Social networks can improve the usability of the site and help to promote it via the shares."
                },
                "video": {
                    "title": "Videos",
                    "details": "Video sharing services help to add rich media on the site and increase its visibility."
                },
                "comment": {
                    "title": "Comments",
                    "details": "Comments managers facilitate the filing of comments and fight against spam."
                },
                "support": {
                    "title": "Support",
                    "details": "Support services allow you to get in touch with the site team and help to improve it."
                },
                "api": {
                    "title": "APIs",
                    "details": "APIs are used to load scripts: geolocation, search engines, translations, ..."
                }
            };
        }

        tarteaucitron.addScript(pathToServices, '', function () {

            var body = document.body,
                div = document.createElement('div'),
                html = '',
                index,
                orientation = 'Top',
                cat = ['ads', 'analytic', 'api', 'comment', 'social', 'support', 'video'],
                i;

            cat = cat.sort(function (a, b) {
                if (tarteaucitron.lang[a].title > tarteaucitron.lang[b].title) { return 1; }
                if (tarteaucitron.lang[a].title < tarteaucitron.lang[b].title) { return -1; }
                return 0;
            });

            // Step 3: prepare the html
            html += '<div id="tarteaucitronPremium"></div>';
            html += '<div id="tarteaucitronBack" onclick="tarteaucitron.userInterface.closePanel();"></div>';
            html += '<div class="c-cookies__choose" id="tarteaucitron">';
            html += '   <div class="c-cookies__choose__close" id="tarteaucitronClosePanel" onclick="tarteaucitron.userInterface.closePanel();">';
            html += '       ' + tarteaucitron.lang.close;
            html += '   </div>';
            html += '   <div id="tarteaucitronServices">';
            html += '       <div class="c-cookies__choose__intro" id="tarteaucitronPresentation">';
            html += '           <div class="c-cookies__choose__intro__title tarteaucitronPresentationTitle">';
            html +=                 tarteaucitron.lang.presentationTitle;
            html += '           </div>';
            html += '           <div class="c-cookies__choose__intro__text tarteaucitronDetails">';
            html +=                 tarteaucitron.lang.presentationContent;
            html += '           </div>';
            html += '       </div>';
            html += '      <div class="c-cookies__category is-main tarteaucitronLine tarteaucitronMainLine" id="tarteaucitronMainLineOffset">';
            html += '         <div class="tarteaucitronName">';
            html += '            <b>' + tarteaucitron.lang.all + '</b>';
            html += '         </div>';
            html += '         <div class="tarteaucitronAsk" id="tarteaucitronScrollbarAdjust" style="display:none;">';
            html += '            <div id="tarteaucitronAllAllowed" class="tarteaucitronAllow c-button--alt" onclick="tarteaucitron.userInterface.respondAll(true);">';
            html +=                 tarteaucitron.lang.allow;
            html += '            </div> ';
            html += '            <div id="tarteaucitronAllDenied" class="tarteaucitronDeny c-button--main" onclick="tarteaucitron.userInterface.respondAll(false);">';
            html +=                 tarteaucitron.lang.deny;
            html += '            </div>';
            html += '         </div>';
            html += '      </div>';
            html += '      <div id="tarteaucitronScrollbarParent">';
            html += '         <div class="clear"></div>';
            for (i = 0; i < cat.length; i += 1) {
                html += '         <div id="tarteaucitronServicesTitle_' + cat[i] + '" class="c-cookies__category tarteaucitronHidden tarteaucitronLine">';
                html += '            <div class="tarteaucitronTitle c-cookies__category__title">';
                html +=                 tarteaucitron.lang[cat[i]].title;
                html += '            </div>';
                html += '            <div id="tarteaucitronDetails' + cat[i] + '" class="c-cookies__category__thumbs tarteaucitronDetails">';
                html += '               ' + tarteaucitron.lang[cat[i]].details;
                html += '            </div>';
                html += '         </div>';
                html += '         <div id="tarteaucitronServices_' + cat[i] + '"></div>';
            }
            html += '           <div class="tarteaucitronHidden" id="tarteaucitronScrollbarChild" style="height:20px;display:block"></div>';
            html += '           </div>';
            if (defaults.removeCredit === false) {
                html += '        <br/><br/>';
                html += '        <a href="https://opt-out.ferank.eu/" rel="nofollow" target="_blank">' + tarteaucitron.lang.credit + '</a>';
            }
            html += '   </div>';
            html += '</div>';
                
            if (defaults.overlayOnSpecificConsent) {
                html += '<div id="tarteaucitronBack-specific" onclick="tarteaucitron.userInterface.closeAuthorizeServiceModal();"></div>';
            }

            html += '<div class="c-cookies__choose" id="tarteaucitronAuthorizeService">';
            html += '   <div class="c-cookies__choose__close" onclick="tarteaucitron.userInterface.closeAuthorizeServiceModal();">';
            html += '       ' + tarteaucitron.lang.close;
            html += '   </div>';
            html += '   <div id="tarteaucitronAuthorizeServiceContent">';
            html += '   </div>';
            html += '</div>';

            if (defaults.orientation === 'bottom') {
                orientation = 'Bottom';
            }

            if (defaults.highPrivacy) {
                html += '<div id="tarteaucitronAlertBig" class="tarteaucitronAlertBig' + orientation + '">';
                html += '   <span id="tarteaucitronDisclaimerAlert">';
                html += '       ' + tarteaucitron.lang.alertBigPrivacy;
                html += '   </span>';
                html += '   <span id="tarteaucitronPersonalize" onclick="tarteaucitron.userInterface.openPanel();">';
                html += '       ' + tarteaucitron.lang.personalize;
                html += '   </span>';
                html += '</div>';
            } else {
                html += '<div id="tarteaucitronAlertBig" class="c-cookies__alert tarteaucitronAlertBig' + orientation + '">';
                html += '   <div id="tarteaucitronDisclaimerAlert">';
                html += '      <div class="c-cookies__alert__title" id="tarteaucitronDisclaimerAlertTitle">';
                html += '       ' + tarteaucitron.lang.alertTitle;
                html += '       </div>';
                html += '   </div>';
                html += '   <div class="c-cookies__alert__text" id="tarteaucitronDisclaimerAlertFull">';
                if (defaults.allowServicesOnPageNav && 
                    typeof tarteaucitron.lang.alertFullAutomatic !== 'undefined') {
                    html += '       ' + tarteaucitron.lang.alertFullAutomatic;
                } else {
                    html += '       ' + tarteaucitron.lang.alertFull; 
                }
                html += '   </div>';
                html += '   <div class="c-cookies__alert__buttons tartaucitronAlertButtons">';
                if (!defaults.allowServicesOnPageNav) {
                    html += '       <button class="c-button--alt" id="tarteaucitronPersonalize" onclick="tarteaucitron.userInterface.openPanel();">';
                    html += '           ' + tarteaucitron.lang.personalize;
                    html += '       </button>';
                }
                html += '       <button class="c-button--main" id="tarteaucitronCloseAlert" onclick="tarteaucitron.userInterface.respondAll(true);">';
                html += '           ' + tarteaucitron.lang.acceptAll;
                html += '       </button>';
                html += '   </div>';
                html += '</div>';
                html += '<div id="tarteaucitronPercentage"></div>';
            }

            if (defaults.showAlertSmall === true) {
                html += '<div id="tarteaucitronAlertSmall">';
                html += '   <div id="tarteaucitronManager" onclick="tarteaucitron.userInterface.openPanel();">';
                html += '       ' + tarteaucitron.lang.alertSmall;
                html += '       <div id="tarteaucitronDot">';
                html += '           <span id="tarteaucitronDotGreen"></span>';
                html += '           <span id="tarteaucitronDotYellow"></span>';
                html += '           <span id="tarteaucitronDotRed"></span>';
                html += '       </div>';
                if (defaults.cookieslist === true) {
                    html += '   </div><!-- @whitespace';
                    html += '   --><div id="tarteaucitronCookiesNumber" onclick="tarteaucitron.userInterface.toggleCookiesList();">0</div>';
                    html += '   <div id="tarteaucitronCookiesListContainer">';
                    html += '       <div id="tarteaucitronClosePanelCookie" onclick="tarteaucitron.userInterface.closePanel();">';
                    html += '           ' + tarteaucitron.lang.close;
                    html += '       </div>';
                    html += '       <div class="tarteaucitronCookiesListMain" id="tarteaucitronCookiesTitle">';
                    html += '            <b id="tarteaucitronCookiesNumberBis">0 cookie</b>';
                    html += '       </div>';
                    html += '       <div id="tarteaucitronCookiesList"></div>';
                    html += '    </div>';
                } else {
                    html += '   </div>';
                }
                html += '</div>';
            }

            tarteaucitron.addScript(tarteaucitron.cdn + 'advertising.js?v=' + tarteaucitron.version, '', function () {
                if (tarteaucitronNoAdBlocker === true || defaults.adblocker === false) {
                    div.id = 'tarteaucitronRoot';
                    div.className = 'c-cookies__root';
                    body.appendChild(div, body);
                    div.innerHTML = html;

                    if (tarteaucitron.job !== undefined) {
                        tarteaucitron.job = tarteaucitron.cleanArray(tarteaucitron.job);
                        for (index = 0; index < tarteaucitron.job.length; index += 1) {
                            tarteaucitron.addService(tarteaucitron.job[index]);
                        }
                    }

                    tarteaucitron.isAjax = true;
                    tarteaucitron.job.push = function (id) {

                        // ie <9 hack
                        if (typeof tarteaucitron.job.indexOf === 'undefined') {
                            tarteaucitron.job.indexOf = function (obj, start) {
                                var i,
                                    j = this.length;
                                for (i = (start || 0); i < j; i += 1) {
                                    if (this[i] === obj) { return i; }
                                }
                                return -1;
                            };
                        }

                        if (tarteaucitron.job.indexOf(id) === -1) {
                            Array.prototype.push.call(this, id);
                        }
                        tarteaucitron.launch[id] = false;
                        tarteaucitron.addService(id);
                    };

                    if (document.location.hash === tarteaucitron.hashtag && tarteaucitron.hashtag !== '') {
                        tarteaucitron.userInterface.openPanel();
                    }

                    tarteaucitron.cookie.number();
                    setInterval(tarteaucitron.cookie.number, 60000);
                }
            }, defaults.adblocker);

            if (defaults.adblocker === true) {
                setTimeout(function () {
                    if (tarteaucitronNoAdBlocker === false) {
                        html = '<div id="tarteaucitronAlertBig" class="tarteaucitronAlertBig' + orientation + '" style="display:block">';
                        html += '   <span id="tarteaucitronDisclaimerAlert">';
                        html += '       ' + tarteaucitron.lang.adblock + '<br/>';
                        html += '       <b>' + tarteaucitron.lang.adblock_call + '</b>';
                        html += '   </span>';
                        html += '   <span id="tarteaucitronPersonalize" onclick="location.reload();">';
                        html += '       ' + tarteaucitron.lang.reload;
                        html += '   </span>';
                        html += '</div>';
                        html += '<div id="tarteaucitronPremium"></div>';
                        div.id = 'tarteaucitronRoot';
                        body.appendChild(div, body);
                        div.innerHTML = html;
                        tarteaucitron.pro('!adblocker=true');
                    } else {
                        tarteaucitron.pro('!adblocker=false');
                    }
                }, 1500);
            }
        });
    },
    "addService": function (serviceId) {
        "use strict";
        var html = '',
            s = tarteaucitron.services,
            service = s[serviceId],
            cookie = tarteaucitron.cookie.read(),
            hostname = document.location.hostname,
            hostRef = document.referrer.split('/')[2],
            isNavigating = (hostRef === hostname) ? true : false,
            isAutostart = (!service.needConsent) ? true : false,
            isWaiting = (cookie.indexOf(service.key + '=wait') >= 0) ? true : false,
            isDenied = (cookie.indexOf(service.key + '=false') >= 0) ? true : false,
            isAllowed = (cookie.indexOf(service.key + '=true') >= 0) ? true : false,
            isResponded = (cookie.indexOf(service.key + '=false') >= 0 || cookie.indexOf(service.key + '=true') >= 0) ? true : false;

        if (tarteaucitron.added[service.key] !== true) {
            tarteaucitron.added[service.key] = true;

            html += '<div id="' + service.key + 'Line" class="c-cookies__category__service tarteaucitronLine">';
            html += '   <div class="tarteaucitronName">';
            html += '       <b>' + service.name + '</b><br/>';
            html += '       <span id="tacCL' + service.key + '" class="tarteaucitronListCookies"></span><br/>';
            html += '       <a href="https://opt-out.ferank.eu/service/' + service.key + '/" target="_blank">';
            html += '           ' + tarteaucitron.lang.more;
            html += '       </a>';
            html += '        - ';
            html += '       <a href="' + service.uri + '" target="_blank">';
            html += '           ' + tarteaucitron.lang.source;
            html += '       </a>';
            html += '   </div>';
            html += '   <div class="tarteaucitronAsk">';
            html += '       <div id="' + service.key + 'Allowed" class="tarteaucitronAllow c-button--alt" onclick="tarteaucitron.userInterface.respond(this, true);">';
            html +=             tarteaucitron.lang.allow;
            html += '       </div> ';
            html += '       <div id="' + service.key + 'Denied" class="tarteaucitronDeny c-button--main" onclick="tarteaucitron.userInterface.respond(this, false);">';
            html +=             tarteaucitron.lang.deny;
            html += '       </div>';
            html += '   </div>';
            html += '</div>';

            tarteaucitron.userInterface.css('tarteaucitronServicesTitle_' + service.type, 'display', 'block');

            if (document.getElementById('tarteaucitronServices_' + service.type) !== null) {
                document.getElementById('tarteaucitronServices_' + service.type).innerHTML += html;
            }

            tarteaucitron.userInterface.order(service.type);
        }

        // allow by default for non EU
        if (isResponded === false && tarteaucitron.user.bypass === true) {
            isAllowed = true;
            tarteaucitron.cookie.create(service.key, true);
        }
        
        if ((!isResponded && isAutostart && !tarteaucitron.highPrivacy) || 
            isAllowed || 
            (tarteaucitron.allowServicesOnPageNav && tarteaucitron.currentUrl !== tarteaucitron.privacyUrl && isWaiting)) {
            if (!isAllowed) {
                tarteaucitron.cookie.create(service.key, true);
            }
            if (tarteaucitron.launch[service.key] !== true) {
                tarteaucitron.launch[service.key] = true;
                service.js();
            }
            tarteaucitron.state[service.key] = true;
            tarteaucitron.userInterface.color(service.key, true);
        } else if (isDenied) {
            if (typeof service.fallback === 'function') {
                service.fallback();
            }
            tarteaucitron.state[service.key] = false;
            tarteaucitron.userInterface.color(service.key, false);
        } else if (!isResponded) {
            tarteaucitron.cookie.create(service.key, 'wait');
            if(tarteaucitron.startJsOnWait) {
                service.js();
            }
            if (typeof service.fallback === 'function') {
                service.fallback();
            }
            tarteaucitron.userInterface.color(service.key, 'wait');
            tarteaucitron.userInterface.openAlert();
        }

        tarteaucitron.cookie.checkCount(service.key);
    },
    "cleanArray": function cleanArray(arr) {
        "use strict";
        var i,
            len = arr.length,
            out = [],
            obj = {},
            s = tarteaucitron.services;

        for (i = 0; i < len; i += 1) {
            if (!obj[arr[i]]) {
                obj[arr[i]] = {};
                if (tarteaucitron.services[arr[i]] !== undefined) {
                    out.push(arr[i]);
                }
            }
        }

        out = out.sort(function (a, b) {
            if (s[a].type + s[a].key > s[b].type + s[b].key) { return 1; }
            if (s[a].type + s[a].key < s[b].type + s[b].key) { return -1; }
            return 0;
        });

        return out;
    },
    "userInterface": {
        "css": function (id, property, value) {
            "use strict";
            if (document.getElementById(id) !== null) {
                document.getElementById(id).style[property] = value;
            }
        },
        "cssClass": function(id, name, add) {
            "use strict";
            var elt = document.getElementById(id);
            if (elt === null) {
                return;
            }
            if (add) {
                elt.classList.add(name);
            } else {
                elt.classList.remove(name);
            }
        },
        "respondAll": function (status) {
            "use strict";
            var s = tarteaucitron.services,
                service,
                key,
                index = 0;

            for (index = 0; index < tarteaucitron.job.length; index += 1) {
                service = s[tarteaucitron.job[index]];
                key = service.key;
                if (tarteaucitron.state[key] !== status) {
                    if (status === false && tarteaucitron.launch[key] === true) {
                        tarteaucitron.reloadThePage = true;
                    }
                    if (tarteaucitron.launch[key] !== true && status === true) {
                        tarteaucitron.launch[key] = true;
                        tarteaucitron.services[key].js();
                    }
                    tarteaucitron.state[key] = status;
                    tarteaucitron.cookie.create(key, status);
                    tarteaucitron.userInterface.color(key, status);
                }
            }
        },
        "respond": function (el, status) {
            "use strict";
            var key = el.id.replace(new RegExp("(Eng[0-9]+|Allow|Deni)ed", "g"), '');

            // return if same state
            if (tarteaucitron.state[key] === status) {
                return;
            }

            if (status === false && tarteaucitron.launch[key] === true) {
                tarteaucitron.reloadThePage = true;
            }

            // if not already launched... launch the service
            if (status === true) {
                if (tarteaucitron.launch[key] !== true) {
                    tarteaucitron.launch[key] = true;
                    tarteaucitron.services[key].js();
                }
            }
            tarteaucitron.state[key] = status;
            tarteaucitron.cookie.create(key, status);
            tarteaucitron.userInterface.color(key, status);
        },
        "respondWithRedirect": function (el, status, redirection) {
            tarteaucitron.userInterface.respond(el, status);
            if (redirection) {
                window.open(redirection,'redirection');
            }
            location.reload();
        },
        "color": function (key, status) {
            "use strict";
            var gray = '#808080',
                greenDark = '#1B870B',
                greenLight = '#E6FFE2',
                redDark = '#9C1A1A',
                redLight = '#FFE2E2',
                yellowDark = '#FBDA26',
                c = 'tarteaucitron',
                nbDenied = 0,
                nbPending = 0,
                nbAllowed = 0,
                sum = tarteaucitron.job.length,
                index,
                element = document.getElementById(key + 'Line');

            if (status === true) {
                element.classList.add("c-cookies__category__service--allowedBorder");
                element.classList.remove("c-cookies__category__service--deniedBorder");
                tarteaucitron.userInterface.cssClass(key + 'Allowed', 'c-cookies__category_service--isAllowed', 1);
                tarteaucitron.userInterface.cssClass(key + 'Denied', 'c-cookies__category_service--isDenied', 0);
            } else if (status === false) {
                element.classList.add("c-cookies__category__service--deniedBorder");
                element.classList.remove("c-cookies__category__service--allowedBorder");
                tarteaucitron.userInterface.cssClass(key + 'Allowed', 'c-cookies__category_service--isAllowed', 0);
                tarteaucitron.userInterface.cssClass(key + 'Denied', 'c-cookies__category_service--isDenied', 1);
            }

            // check if all services are allowed
            for (index = 0; index < sum; index += 1) {
                if (tarteaucitron.state[tarteaucitron.job[index]] === false) {
                    nbDenied += 1;
                } else if (tarteaucitron.state[tarteaucitron.job[index]] === undefined) {
                    nbPending += 1;
                } else if (tarteaucitron.state[tarteaucitron.job[index]] === true) {
                    nbAllowed += 1;
                }
            }

            tarteaucitron.userInterface.css(c + 'DotGreen', 'width', ((100 / sum) * nbAllowed) + '%');
            tarteaucitron.userInterface.css(c + 'DotYellow', 'width', ((100 / sum) * nbPending) + '%');
            tarteaucitron.userInterface.css(c + 'DotRed', 'width', ((100 / sum) * nbDenied) + '%');

            if (nbDenied === 0 && nbPending === 0) {
                tarteaucitron.userInterface.cssClass(c + 'AllAllowed', 'c-cookies__category_all_services--isAllowed', 1);
                tarteaucitron.userInterface.cssClass(c + 'AllDenied', 'c-cookies__category_all_services--isDenied', 0);
            } else if (nbAllowed === 0 && nbPending === 0) {
                tarteaucitron.userInterface.cssClass(c + 'AllAllowed', 'c-cookies__category_all_services--isAllowed', 0);
                tarteaucitron.userInterface.cssClass(c + 'AllDenied', 'c-cookies__category_all_services--isDenied', 1);
            } else {
                tarteaucitron.userInterface.cssClass(c + 'AllAllowed', 'c-cookies__category_all_services--isAllowed', 0);
                tarteaucitron.userInterface.cssClass(c + 'AllDenied', 'c-cookies__category_all_services--isDenied', 0);
            }

            // close the alert if all service have been reviewed
            if (nbPending === 0) {
                tarteaucitron.userInterface.closeAlert();
            }

            if (tarteaucitron.services[key].cookies.length > 0 && status === false) {
                tarteaucitron.cookie.purge(tarteaucitron.services[key].cookies);
            }

            if (status === true) {
                if (document.getElementById('tacCL' + key) !== null) {
                    document.getElementById('tacCL' + key).innerHTML = '...';
                }
                setTimeout(function () {
                    tarteaucitron.cookie.checkCount(key);
                }, 2500);
            } else {
                tarteaucitron.cookie.checkCount(key);
            }
        },
        "openPanel": function () {
            "use strict";
            tarteaucitron.userInterface.css('tarteaucitron', 'display', 'block');
            document.getElementById('tarteaucitron').classList.add('is-open');
            tarteaucitron.userInterface.css('tarteaucitronBack', 'display', 'block');
            tarteaucitron.userInterface.css('tarteaucitronCookiesListContainer', 'display', 'none');
            tarteaucitron.userInterface.jsSizing('main');
            tarteaucitron.userInterface.closeAuthorizeServiceModal();
        },
        "closePanel": function () {
            "use strict";

            if (document.location.hash === tarteaucitron.hashtag) {
                document.location.hash = '';
            }
            tarteaucitron.userInterface.css('tarteaucitron', 'display', 'none');
            document.getElementById('tarteaucitron').classList.remove('is-open');
            tarteaucitron.userInterface.css('tarteaucitronCookiesListContainer', 'display', 'none');

            tarteaucitron.fallback(['tarteaucitronInfoBox'], function (elem) {
                elem.style.display = 'none';
            }, true);

            if (tarteaucitron.reloadThePage === true) {
                window.location.reload();
            } else {
                tarteaucitron.userInterface.css('tarteaucitronBack', 'display', 'none');
            }
            tarteaucitron.userInterface.closeAuthorizeServiceModal();
        },
        "openAlert": function () {
            "use strict";
            var c = 'tarteaucitron';
            tarteaucitron.userInterface.css(c + 'Percentage', 'display', 'block');
            tarteaucitron.userInterface.css(c + 'AlertSmall', 'display', 'none');
            tarteaucitron.userInterface.css(c + 'AlertBig',   'display', 'block');
            tarteaucitron.userInterface.closeAuthorizeServiceModal();
        },
        "closeAlert": function () {
            "use strict";
            var c = 'tarteaucitron';
            tarteaucitron.userInterface.css(c + 'Percentage', 'display', 'none');
            tarteaucitron.userInterface.css(c + 'AlertSmall', 'display', 'block');
            tarteaucitron.userInterface.css(c + 'AlertBig',   'display', 'none');
            tarteaucitron.userInterface.jsSizing('box');
            tarteaucitron.userInterface.closeAuthorizeServiceModal();
        },
        "authorizeServiceModal": function (element, serviceKey) {
            if (tarteaucitron.services[serviceKey]) {
                tarteaucitron.userInterface.openAuthorizeServiceModal();
                var html = '',
                    serviceDenied = 'document.getElementById(\'' + serviceKey + 'Denied\')',
                    redirection = element.href ? element.href : '';
                if (serviceDenied) {
                    html += '<div class="c-cookies__choose__intro__title">';
                    html += '   <span>';
                    html +=     tarteaucitron.services[serviceKey].name + ' ' + tarteaucitron.lang.isDisabled;
                    html += '   </span>';
                    html += '   <span> ';
                    html +=     tarteaucitron.lang.authorizeService;
                    html += '   </span>';
                    html += '</div>';
                    html += '<div class="c-cookies__authorize__buttons">';
                    html += '   <div class="c-button--alt" id="tarteaucitronCloseAlert" onclick="tarteaucitron.userInterface.respondWithRedirect(' + serviceDenied + ', true, \'' + redirection + '\');">';
                    html +=     tarteaucitron.lang.allow;
                    html += '   </div>';
                    html += '   <div class="c-button--main" id="tarteaucitronPersonalize" onclick="tarteaucitron.userInterface.openPanel();">';
                    html +=     tarteaucitron.lang.personalize;
                    html += '   </div>';
                    html += '</div>';

                    document.getElementById('tarteaucitronAuthorizeServiceContent').innerHTML = html;
                    return false;
                }
            }
            return;
        },
        "openAuthorizeServiceModal": function() {
            "use strict";
            tarteaucitron.userInterface.css('tarteaucitronAuthorizeService', 'display', 'block');
            if (tarteaucitron.overlayOnSpecificConsent) {
                tarteaucitron.userInterface.css('tarteaucitronBack-specific', 'display', 'block');
            }
        },
        "closeAuthorizeServiceModal": function() {
            "use strict";
            tarteaucitron.userInterface.css('tarteaucitronAuthorizeService', 'display', 'none');
            if (tarteaucitron.overlayOnSpecificConsent) {
                tarteaucitron.userInterface.css('tarteaucitronBack-specific', 'display', 'none');
            }
        },
        "toggleCookiesList": function () {
            "use strict";
            var div = document.getElementById('tarteaucitronCookiesListContainer');

            if (div === null) {
                return;
            }

            if (div.style.display !== 'block') {
                tarteaucitron.cookie.number();
                div.style.display = 'block';
                tarteaucitron.userInterface.jsSizing('cookie');
                tarteaucitron.userInterface.css('tarteaucitron', 'display', 'none');
                tarteaucitron.userInterface.css('tarteaucitronBack', 'display', 'block');
                tarteaucitron.fallback(['tarteaucitronInfoBox'], function (elem) {
                    elem.style.display = 'none';
                }, true);
            } else {
                div.style.display = 'none';
                tarteaucitron.userInterface.css('tarteaucitron', 'display', 'none');
                tarteaucitron.userInterface.css('tarteaucitronBack', 'display', 'none');
            }
        },
        "toggle": function (id, closeClass) {
            "use strict";
            var div = document.getElementById(id);

            if (div === null) {
                return;
            }

            if (closeClass !== undefined) {
                tarteaucitron.fallback([closeClass], function (elem) {
                    if (elem.id !== id) {
                        elem.style.display = 'none';
                    }
                }, true);
            }

            if (div.style.display !== 'block') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        },
        "order": function (id) {
            "use strict";
            var main = document.getElementById('tarteaucitronServices_' + id),
                allDivs,
                store = [],
                i;

            if (main === null) {
                return;
            }

            allDivs = main.childNodes;

            if (typeof Array.prototype.map === 'function') {
                Array.prototype.map.call(main.children, Object).sort(function (a, b) {
                    if (tarteaucitron.services[a.id.replace(/Line/g, '')].name > tarteaucitron.services[b.id.replace(/Line/g, '')].name) { return 1; }
                    if (tarteaucitron.services[a.id.replace(/Line/g, '')].name < tarteaucitron.services[b.id.replace(/Line/g, '')].name) { return -1; }
                    return 0;
                }).forEach(function (element) {
                    main.appendChild(element);
                });
            }
        },
        "jsSizing": function (type) {
            "use strict";
            var scrollbarMarginRight = 10,
                scrollbarWidthParent,
                scrollbarWidthChild,
                servicesHeight,
                e = window,
                a = 'inner',
                windowInnerHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
                mainTop,
                mainHeight,
                closeButtonHeight,
                headerHeight,
                cookiesListHeight,
                cookiesCloseHeight,
                cookiesTitleHeight,
                paddingBox,
                alertSmallHeight,
                cookiesNumberHeight;

            if (type === 'box') {
                if (document.getElementById('tarteaucitronAlertSmall') !== null && document.getElementById('tarteaucitronCookiesNumber') !== null) {

                    // reset
                    tarteaucitron.userInterface.css('tarteaucitronCookiesNumber', 'padding', '0px 10px');

                    // calculate
                    alertSmallHeight = document.getElementById('tarteaucitronAlertSmall').offsetHeight;
                    cookiesNumberHeight = document.getElementById('tarteaucitronCookiesNumber').offsetHeight;
                    paddingBox = (alertSmallHeight - cookiesNumberHeight) / 2;

                    // apply
                    tarteaucitron.userInterface.css('tarteaucitronCookiesNumber', 'padding', paddingBox + 'px 10px');
                }
            } else if (type === 'main') {

                // get the real window width for media query
                if (window.innerWidth === undefined) {
                    a = 'client';
                    e = document.documentElement || document.body;
                }

                // height of the services list container
                if (document.getElementById('tarteaucitron') !== null && document.getElementById('tarteaucitronClosePanel') !== null && document.getElementById('tarteaucitronMainLineOffset') !== null) {

                    // reset
                    tarteaucitron.userInterface.css('tarteaucitronScrollbarParent', 'height', 'auto');

                    // calculate
                    mainHeight = document.getElementById('tarteaucitron').offsetHeight;
                    closeButtonHeight = document.getElementById('tarteaucitronClosePanel').offsetHeight;
                    headerHeight = document.getElementById('tarteaucitronMainLineOffset').offsetHeight + document.getElementById('tarteaucitronPresentation').offsetHeight;

                    // apply
                    servicesHeight = (mainHeight - closeButtonHeight - headerHeight + 1);
                    tarteaucitron.userInterface.css('tarteaucitronScrollbarParent', 'height', servicesHeight + 'px');
                }

                // align the main allow/deny button depending on scrollbar width
                if (document.getElementById('tarteaucitronScrollbarParent') !== null && document.getElementById('tarteaucitronScrollbarChild') !== null) {

                    // media query
                    if (e[a + 'Width'] <= 479) {
                        tarteaucitron.userInterface.css('tarteaucitronScrollbarAdjust', 'marginLeft', '11px');
                    } else if (e[a + 'Width'] <= 767) {
                        scrollbarMarginRight = 12;
                    }

                    scrollbarWidthParent = document.getElementById('tarteaucitronScrollbarParent').offsetWidth;
                    scrollbarWidthChild = document.getElementById('tarteaucitronScrollbarChild').offsetWidth;
                    tarteaucitron.userInterface.css('tarteaucitronScrollbarAdjust', 'marginRight', ((scrollbarWidthParent - scrollbarWidthChild) + scrollbarMarginRight) + 'px');
                }

                // center the main panel
                if (document.getElementById('tarteaucitron') !== null) {

                    // media query
                    if (e[a + 'Width'] <= 767) {
                        mainTop = 0;
                    } else {
                        mainTop = ((windowInnerHeight - document.getElementById('tarteaucitron').offsetHeight) / 2) - 21;
                    }

                    // correct
                    if (mainTop < 0) {
                        mainTop = 0;
                    }

                    if (document.getElementById('tarteaucitronMainLineOffset') !== null) {
                        if (document.getElementById('tarteaucitron').offsetHeight < (windowInnerHeight / 2)) {
                            mainTop -= document.getElementById('tarteaucitronMainLineOffset').offsetHeight;
                        }
                    }

                    // apply
                    tarteaucitron.userInterface.css('tarteaucitron', 'top', mainTop + 'px');
                }


            } else if (type === 'cookie') {

                // put cookies list at bottom
                if (document.getElementById('tarteaucitronAlertSmall') !== null) {
                    tarteaucitron.userInterface.css('tarteaucitronCookiesListContainer', 'bottom', (document.getElementById('tarteaucitronAlertSmall').offsetHeight) + 'px');
                }

                // height of cookies list
                if (document.getElementById('tarteaucitronCookiesListContainer') !== null) {

                    // reset
                    tarteaucitron.userInterface.css('tarteaucitronCookiesList', 'height', 'auto');

                    // calculate
                    cookiesListHeight = document.getElementById('tarteaucitronCookiesListContainer').offsetHeight;
                    cookiesCloseHeight = document.getElementById('tarteaucitronClosePanelCookie').offsetHeight;
                    cookiesTitleHeight = document.getElementById('tarteaucitronCookiesTitle').offsetHeight;

                    // apply
                    tarteaucitron.userInterface.css('tarteaucitronCookiesList', 'height', (cookiesListHeight - cookiesCloseHeight - cookiesTitleHeight - 2) + 'px');
                }
            }
        },        
        "isServiceAllowed": function (serviceKey) {
            "use strict";
            var cookie = tarteaucitron.cookie.read();

            return (cookie.indexOf(serviceKey + '=true') >= 0) ? true : false;
        }
    },
    "cookie": {
        "owner": {},
        "create": function (key, status) {
            "use strict";
            var d = new Date(),
                time = d.getTime(),
                expireTime = time + 31536000000, // 365 days
                regex = new RegExp("!" + key + "=(wait|true|false)", "g"),
                cookie = tarteaucitron.cookie.read().replace(regex, ""),
                value = 'tarteaucitron=' + cookie + '!' + key + '=' + status;

            if (tarteaucitron.cookie.read().indexOf(key + '=' + status) === -1) {
                tarteaucitron.pro('!' + key + '=' + status);
            }

            d.setTime(expireTime);
            document.cookie = value + '; expires=' + d.toGMTString() + '; path=/;';
        },
        "read": function () {
            "use strict";
            var nameEQ = "tarteaucitron=",
                ca = document.cookie.split(';'),
                i,
                c;

            for (i = 0; i < ca.length; i += 1) {
                c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1, c.length);
                }
                if (c.indexOf(nameEQ) === 0) {
                    return c.substring(nameEQ.length, c.length);
                }
            }
            return '';
        },
        "purge": function (arr) {
            "use strict";
            var i;

            for (i = 0; i < arr.length; i += 1) {
                document.cookie = arr[i] + '=; expires=Thu, 01 Jan 2000 00:00:00 GMT; path=/;';
                document.cookie = arr[i] + '=; expires=Thu, 01 Jan 2000 00:00:00 GMT; path=/; domain=.' + location.hostname + ';';
                document.cookie = arr[i] + '=; expires=Thu, 01 Jan 2000 00:00:00 GMT; path=/; domain=.' + location.hostname.split('.').slice(-2).join('.') + ';';
            }
        },
        "checkCount": function (key) {
            "use strict";
            var arr = tarteaucitron.services[key].cookies,
                nb = arr.length,
                nbCurrent = 0,
                html = '',
                i,
                status = document.cookie.indexOf(key + '=true');

            if (status >= 0 && nb === 0) {
                html += tarteaucitron.lang.useNoCookie;
            } else if (status >= 0) {
                for (i = 0; i < nb; i += 1) {
                    if (document.cookie.indexOf(arr[i] + '=') !== -1) {
                        nbCurrent += 1;
                        if (tarteaucitron.cookie.owner[arr[i]] === undefined) {
                            tarteaucitron.cookie.owner[arr[i]] = [];
                        }
                        if (tarteaucitron.cookie.crossIndexOf(tarteaucitron.cookie.owner[arr[i]], tarteaucitron.services[key].name) === false) {
                            tarteaucitron.cookie.owner[arr[i]].push(tarteaucitron.services[key].name);
                        }
                    }
                }

                if (nbCurrent > 0) {
                    html += tarteaucitron.lang.useCookieCurrent + ' ' + nbCurrent + ' cookie';
                    if (nbCurrent > 1) {
                        html += 's';
                    }
                    html += '.';
                } else {
                    html += tarteaucitron.lang.useNoCookie;
                }
            } else if (nb === 0) {
                html = tarteaucitron.lang.noCookie;
            } else {
                html += tarteaucitron.lang.useCookie + ' ' + nb + ' cookie';
                if (nb > 1) {
                    html += 's';
                }
                html += '.';
            }

            if (document.getElementById('tacCL' + key) !== null) {
                document.getElementById('tacCL' + key).innerHTML = html;
            }
        },
        "crossIndexOf": function (arr, match) {
            "use strict";
            var i;
            for (i = 0; i < arr.length; i += 1) {
                if (arr[i] === match) {
                    return true;
                }
            }
            return false;
        },
        "number": function () {
            "use strict";
            var cookies = document.cookie.split(';'),
                nb = (document.cookie !== '') ? cookies.length : 0,
                html = '',
                i,
                name,
                namea,
                nameb,
                c,
                d,
                s = (nb > 1) ? 's' : '',
                savedname,
                regex = /^https?\:\/\/([^\/?#]+)(?:[\/?#]|$)/i,
                regexedDomain = (tarteaucitron.cdn.match(regex) !== null) ? tarteaucitron.cdn.match(regex)[1] : tarteaucitron.cdn,
                host = (tarteaucitron.domain !== undefined) ? tarteaucitron.domain : regexedDomain;

            cookies = cookies.sort(function (a, b) {
                namea = a.split('=', 1).toString().replace(/ /g, '');
                nameb = b.split('=', 1).toString().replace(/ /g, '');
                c = (tarteaucitron.cookie.owner[namea] !== undefined) ? tarteaucitron.cookie.owner[namea] : '0';
                d = (tarteaucitron.cookie.owner[nameb] !== undefined) ? tarteaucitron.cookie.owner[nameb] : '0';
                if (c + a > d + b) { return 1; }
                if (c + a < d + b) { return -1; }
                return 0;
            });

            if (document.cookie !== '') {
                for (i = 0; i < nb; i += 1) {
                    name = cookies[i].split('=', 1).toString().replace(/ /g, '');
                    if (tarteaucitron.cookie.owner[name] !== undefined && tarteaucitron.cookie.owner[name].join(' // ') !== savedname) {
                        savedname = tarteaucitron.cookie.owner[name].join(' // ');
                        html += '<div class="tarteaucitronHidden">';
                        html += '     <div class="tarteaucitronTitle">';
                        html += '        ' + tarteaucitron.cookie.owner[name].join(' // ');
                        html += '    </div>';
                        html += '</div>';
                    } else if (tarteaucitron.cookie.owner[name] === undefined && host !== savedname) {
                        savedname = host;
                        html += '<div class="tarteaucitronHidden">';
                        html += '     <div class="tarteaucitronTitle">';
                        html += '        ' + host;
                        html += '    </div>';
                        html += '</div>';
                    }
                    html += '<div class="tarteaucitronCookiesListMain">';
                    html += '    <div class="tarteaucitronCookiesListLeft"><a href="#" onclick="tarteaucitron.cookie.purge([\'' + cookies[i].split('=', 1) + '\']);tarteaucitron.cookie.number();tarteaucitron.userInterface.jsSizing(\'cookie\');return false"><b>&times;</b></a> <b>' + name + '</b>';
                    html += '    </div>';
                    html += '    <div class="tarteaucitronCookiesListRight">' + cookies[i].split('=').slice(1).join('=') + '</div>';
                    html += '</div>';
                }
            } else {
                html += '<div class="tarteaucitronCookiesListMain">';
                html += '    <div class="tarteaucitronCookiesListLeft"><b>-</b></div>';
                html += '    <div class="tarteaucitronCookiesListRight"></div>';
                html += '</div>';
            }

            html += '<div class="tarteaucitronHidden" style="height:20px;display:block"></div>';

            if (document.getElementById('tarteaucitronCookiesList') !== null) {
                document.getElementById('tarteaucitronCookiesList').innerHTML = html;
            }

            if (document.getElementById('tarteaucitronCookiesNumber') !== null) {
                document.getElementById('tarteaucitronCookiesNumber').innerHTML = nb;
            }

            if (document.getElementById('tarteaucitronCookiesNumberBis') !== null) {
                document.getElementById('tarteaucitronCookiesNumberBis').innerHTML = nb + ' cookie' + s;
            }

            for (i = 0; i < tarteaucitron.job.length; i += 1) {
                tarteaucitron.cookie.checkCount(tarteaucitron.job[i]);
            }
        }
    },
    "getLanguage": function () {
        "use strict";
        if (!navigator) { return 'en'; }

        var availableLanguages = 'cs,en,fr,es,it,de,pt,pl,ru',
            defaultLanguage = 'en',
            lang = navigator.language || navigator.browserLanguage ||
                navigator.systemLanguage || navigator.userLang || null,
            userLanguage = lang.substr(0, 2);

        if (tarteaucitronForceLanguage !== '') {
            if (availableLanguages.indexOf(tarteaucitronForceLanguage) !== -1) {
                return tarteaucitronForceLanguage;
            }
        }

        if (availableLanguages.indexOf(userLanguage) === -1) {
            return defaultLanguage;
        }
        return userLanguage;
    },
    "getLocale": function () {
        "use strict";
        if (!navigator) { return 'en_US'; }

        var lang = navigator.language || navigator.browserLanguage ||
                navigator.systemLanguage || navigator.userLang || null,
            userLanguage = lang.substr(0, 2);

        if (userLanguage === 'fr') {
            return 'fr_FR';
        } else if (userLanguage === 'en') {
            return 'en_US';
        } else if (userLanguage === 'de') {
            return 'de_DE';
        } else if (userLanguage === 'es') {
            return 'es_ES';
        } else if (userLanguage === 'it') {
            return 'it_IT';
        } else if (userLanguage === 'pt') {
            return 'pt_PT';
        } else {
            return 'en_US';
        }
    },
    "addScript": function (url, id, callback, execute, attrName, attrVal) {
        "use strict";
        var script,
            done = false;

        if (execute === false) {
            if (typeof callback === 'function') {
                callback();
            }
        } else {
            script = document.createElement('script');
            script.type = 'text/javascript';
            script.id = (id !== undefined) ? id : '';
            script.defer = true;
            script.async = false;
            script.src = url;

            if (attrName !== undefined && attrVal !== undefined) {
                script.setAttribute(attrName, attrVal);
            }

            if (typeof callback === 'function') {
                script.onreadystatechange = script.onload = function () {
                    var state = script.readyState;
                    if (!done && (!state || /loaded|complete/.test(state))) {
                        done = true;
                        callback();
                    }
                };
            }

            document.getElementsByTagName('head')[0].appendChild(script);
        }
    },
    "makeAsync": {
        "antiGhost": 0,
        "buffer": '',
        "init": function (url, id) {
            "use strict";
            var savedWrite = document.write,
                savedWriteln = document.writeln;

            document.write = function (content) {
                tarteaucitron.makeAsync.buffer += content;
            };
            document.writeln = function (content) {
                tarteaucitron.makeAsync.buffer += content.concat("\n");
            };

            setTimeout(function () {
                document.write = savedWrite;
                document.writeln = savedWriteln;
            }, 20000);

            tarteaucitron.makeAsync.getAndParse(url, id);
        },
        "getAndParse": function (url, id) {
            "use strict";
            if (tarteaucitron.makeAsync.antiGhost > 9) {
                tarteaucitron.makeAsync.antiGhost = 0;
                return;
            }
            tarteaucitron.makeAsync.antiGhost += 1;
            tarteaucitron.addScript(url, '', function () {
                if (document.getElementById(id) !== null) {
                    document.getElementById(id).innerHTML += "<span style='display:none'>&nbsp;</span>" + tarteaucitron.makeAsync.buffer;
                    tarteaucitron.makeAsync.buffer = '';
                    tarteaucitron.makeAsync.execJS(id);
                }
            });
        },
        "execJS": function (id) {
            /* not strict because third party scripts may have errors */
            var i,
                scripts,
                childId,
                type;

            if (document.getElementById(id) === null) {
                return;
            }

            scripts = document.getElementById(id).getElementsByTagName('script');
            for (i = 0; i < scripts.length; i += 1) {
                type = (scripts[i].getAttribute('type') !== null) ? scripts[i].getAttribute('type') : '';
                if (type === '') {
                    type = (scripts[i].getAttribute('language') !== null) ? scripts[i].getAttribute('language') : '';
                }
                if (scripts[i].getAttribute('src') !== null && scripts[i].getAttribute('src') !== '') {
                    childId = id + Math.floor(Math.random() * 99999999999);
                    document.getElementById(id).innerHTML += '<div id="' + childId + '"></div>';
                    tarteaucitron.makeAsync.getAndParse(scripts[i].getAttribute('src'), childId);
                } else if (type.indexOf('javascript') !== -1 || type === '') {
                    eval(scripts[i].innerHTML);
                }
            }
        }
    },
    "fallback": function (matchClass, content, noInner) {
        "use strict";
        var elems = document.getElementsByTagName('*'),
            i,
            index = 0,
            contentElt;

        for (i in elems) {
            if (elems[i] !== undefined) {
                for (index = 0; index < matchClass.length; index += 1) {
                    if ((' ' + elems[i].className + ' ')
                            .indexOf(' ' + matchClass[index] + ' ') > -1) {
                        if (typeof content === 'function') {
                            if (noInner === true) {
                                content(elems[i]);
                            } else {
                                contentElt = content(elems[i]);
                                if (typeof contentElt === 'object' && 
                                    contentElt.attribute) {
                                    elems[i].setAttribute(contentElt.attribute.name, contentElt.attribute.value);                                    
                                } else {
                                    elems[i].innerHTML = content(elems[i]);
                                }
                            }
                        } else {
                            if (content.attribute) {
                                elems[i].setAttribute(content.attribute.name, content.attribute.value);
                            }
                        }
                    }
                }
            }
        }
    },
    "engage": function (id) {
        "use strict";

        return {
            'attribute': {
                'name': 'onclick',
                'value': 'return tarteaucitron.userInterface.authorizeServiceModal(this, "' + tarteaucitron.services[id].key + '")'
            }
        };
        //var html = '',
        //    r = Math.floor(Math.random() * 100000);

        //html += '<div class="tac_activate">';
        //html += '   <div class="tac_float">';
        //html += '      <b>' + tarteaucitron.services[id].name + '</b> ' + tarteaucitron.lang.fallback;
        //html += '      <div class="tarteaucitronAllow" id="Eng' + r + 'ed' + id + '" onclick="tarteaucitron.userInterface.respond(this, true);">';
        //html += '          &#10003; ' + tarteaucitron.lang.allow;
        //html += '       </div>';
        //html += '   </div>';
        //html += '</div>';

        //return html;
    },
    "extend": function (a, b) {
        "use strict";
        var prop;
        for (prop in b) {
            if (b.hasOwnProperty(prop)) {
                a[prop] = b[prop];
            }
        }
    },
    "proTemp": '',
    "proTimer": function () {
        "use strict";
        setTimeout(tarteaucitron.proPing, 1000);
    },
    "pro": function (list) {
        "use strict";
        tarteaucitron.proTemp += list;
        clearTimeout(tarteaucitron.proTimer);
        tarteaucitron.proTimer = setTimeout(tarteaucitron.proPing, 2500);
    },
    "proPing": function () {
        "use strict";
        if (tarteaucitron.uuid !== '' && tarteaucitron.uuid !== undefined && tarteaucitron.proTemp !== '') {
            var div = document.getElementById('tarteaucitronPremium'),
                timestamp = new Date().getTime(),
                url = '//opt-out.ferank.eu/premium.php?';

            if (div === null) {
                return;
            }

            url += 'domain=' + tarteaucitron.domain + '&';
            url += 'uuid=' + tarteaucitron.uuid + '&';
            url += 'c=' + encodeURIComponent(tarteaucitron.proTemp) + '&';
            url += '_' + timestamp;

            div.innerHTML = '<img src="' + url + '" style="display:none" />';

            tarteaucitron.proTemp = '';
        }

        tarteaucitron.cookie.number();
    }
};
