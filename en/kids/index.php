<?php
    require_once '../app/Mage.php';
    Mage::app();

    $lang = Mage::helper('dbm_country')->getBrowserLocale();
    if(substr($lang, 0, 2) != "fr") {
        header('Location: index.en.php');
        exit();
    }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>monbentoKids</title>
    <meta http-equiv="X-UA-Compatible" content="text/html" charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--[if lt IE 9]> <script src="https:/html5shiv.googlecode.com/svn/trunk/html5.js"></script> <![endif]--><!--[if !IE]> -->
    <link rel="stylesheet" href="styles/css/styles.css"><![endif]-->
    <!--[if lt IE 10]>
    <link rel="stylesheet" href="styles/css/oldies.css"><![endif]-->
    <!--[if gt IE 10]>
    <link rel="stylesheet" href="styles/css/styles.css"><![endif]-->
    <script src="js/plugins/modernizr.js"></script>
    <link rel="icon" href="https://www.monbento.com/skin/frontend/default/monbento/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="https://www.monbento.com/skin/frontend/default/monbento/favicon.ico" type="image/x-icon">
  </head>
  <body class="monbentoKids_home">
    <header>
      <nav class="nav_header_homepage">
        <div class="container">
          <div class="goto_monbento grid-6 txt-align-left"><a href="https://www.monbento.com/" title="Allez sur monbento.com" target="_blank">Aller sur <strong>monbento.com</strong></a></div>
          <div class="language grid-6 txt-align-right"><a href="" title="Fran&ccedil;ais" class="current">Fr</a><a href="index.en.php" title="English">En</a></div>
        </div>
      </nav>
      <div class="container">
        <div class="punchline txt-align-right">Id&eacute;es recettes, cr&eacute;ations, surprises, jeux…</div>
        <div class="punchline_sub txt-align-right">Faites plaisir a votre enfant !</div>
      </div>
    </header>
    <section class="cta_buy">
      <div class="container">
        <div class="lunch grid-6 grid-m-12 txt-align-left">
          <div class="title">Au déjeuner,</div>
          <div class="content">le <strong>MB Tresor</strong> l'accompagne pour<br>faire le plein d'énergie !</div><a href="https://www.monbento.com/shop/lunch-box/bento-enfant-mb-tresor.html" class="btn" target="_blank">acheter le mb tresor</a>
        </div>
        <div class="break grid-6 grid-m-12 txt-align-right">
          <div class="title">Au goûter,</div>
          <div class="content">le <strong>MB Gram</strong> le suit<br>dans toutes ses aventures !</div><a href="https://www.monbento.com/shop/lunch-box/boite-a-gouter-enfant-mb-gram.html" class="btn" target="_blank">acheter le mb gram</a>
        </div>
      </div>
    </section>
    <section class="boosted_afternoon">
      <div class="container">
        <div class="embed_video">
          <iframe width="890" height="501" src="https://www.youtube.com/embed/Yde93UBSOeY?rel=0&amp;amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
    </section>
    <section class="rainbow_slide">
      <div class="container">
        <div class="rainbow_bxlider_container">
          <ul class="rainbow_bxlider_startr">
            <li><img src="images/img_slide_box_pink.png"></li>
            <li><img src="images/img_slide_box_orange.png"></li>
            <li><img src="images/img_slide_box_green.png"></li>
            <li><img src="images/img_slide_box_blue.png"></li>
          </ul>
        </div>
      </div>
    </section>
    <section class="custom_box">
      <div class="container">
        <div class="title">Votre enfant est unique, sa lunch box aussi !</div>
        <div class="title_sub">Personnalisez-la en cr&eacute;ant vos propres pastilles.</div>
        <figure class="cd-image-container"><img src="images/img_gram_comp.jpg"><span data-type="original" class="cd-image-label">MB GRAM</span>
          <div class="cd-resize-img"><img src="images/img_tresor_comp.jpg"><span data-type="modified" class="cd-image-label">MB TRESOR</span>
          </div><span class="cd-handle"></span>
        </figure>
        <div class="btn_download_buy">
          <div class="btn_download grid-5 grid-m-12"><a href="#" data-href="medias/customPastille.pdf" data-reveal-id="myModal" class="btn dl">T&eacute;l&eacute;charger 12 pastilles a personnaliser</a>
            <p>&Agrave; imprimer soi-m&ecirc;me.</p>
          </div>
          <div class="btn_separator grid-2 hide-s">ou</div>
          <div class="btn_buy grid-5 grid-m-12"><a href="https://www.monbento.com/shop/lunch-box/kids.html" class="btn" target="_blank">Acheter un MB Tresor ou une MB Gram</a>
            <p>5 pastilles incluses !</p>
          </div>
          <div class="item_bon_point"></div>
          <div class="item_mot_doux"></div>
        </div>
      </div>
    </section>
    <section class="your_game">
      <div class="container">
        <div class="title">Créez vos propres jeux avec votre enfant</div>
        <div class="triptic_content">
          <div class="triptic_pink grid-4 grid-s-12">
            <div class="thumb"><img src="images/triptic_pink.jpg"></div>
            <div class="title">cindy <span class="clr_pink">&amp; </span>fanny</div>
            <div class="content">Cindy aime glisser des petits mots doux à sa fille et lui faire chaque jour la surprise d'un nouveau message.</div>
            <div class="quote clr_pink">Fanny les attend toujours avec impatience.</div>
          </div>
          <div class="triptic_green grid-4 grid-s-12">
            <div class="thumb"><img src="images/triptic_green.jpg"></div>
            <div class="title">marie <span class="clr_green">&amp; </span>louis</div>
            <div class="content">Marie aime apprendre de nouvelles choses à son fils de manière ludique.</div>
            <div class="quote clr_green">Louis s'amuse quotidiennement à trouver le légume ou le fruit que sa maman lui a dessiné.</div>
          </div>
          <div class="triptic_yellow grid-4 grid-s-12">
            <div class="thumb"><img src="images/triptic_orange.jpg"></div>
            <div class="title">marc <span class="clr_orange">&amp; </span>sophie</div>
            <div class="content">Marc aime suprendre sa fille en glissant des petites choses derrières les pastilles.</div>
            <div class="quote clr_orange">Sophie découvre avec plaisir à l'heure du goûter petits mots, pièces de monnaie "cachées" par son papa adoré.</div>
          </div>
        </div>
      </div>
    </section>
    <section class="simple_things simple_things_grey">
      <div class="container">
        <div class="row row_dot_game">
          <div class="title">Faites plaisir a votre enfant avec des choses simples</div>
          <div class="content grid-5 grid-l-7 right">
            <div class="inner_title">Un jeu a relier</div>
            <ul>
              <li>Imprimez-le.</li>
              <li>Glissez-le dans sa bo&icirc;te.</li>
              <li>C'est gagn&eacute; : vous &ecirc;tes un parent en or !</li>
            </ul><a href="#" data-href="medias/pointAtelier.pdf" data-reveal-id="myModal" class="btn dl">T&eacute;l&eacute;charger gratuitement le jeu &agrave; relier</a>
          </div>
        </div>
      </div>
    </section>
    <section class="simple_things">
      <div class="container">
        <div class="row row_paper_mask">
          <div class="content grid-5 grid-l-7 left">
            <div class="inner_title">Un masque en papier</div>
            <ul>
              <li>Imprimez-le.</li>
              <li>Aidez votre enfant &agrave; le d&eacute;couper et &agrave; ins&eacute;rer une ficelle pour pouvoir le porter</li>
              <li>Sortez l'appareil photo et faites-nous partager les plus belles poses !</li>
            </ul><a href="#" data-href="medias/masque.pdf" data-reveal-id="myModal" class="btn dl">T&eacute;l&eacute;charger gratuitement le masque</a>
          </div>
        </div>
      </div>
    </section>
    <section class="simple_things simple_things_grey">
      <div class="container">
        <div class="row row_recette">
          <div class="content grid-5 grid-l-7 right">
            <div class="inner_title">&eacute;veillez les go&ucirc;ts de votre enfant avec ce carnet gratuit cr&eacute;&eacute; pour lui</div>
            <ul>
              <li><strong>Comment ça se passe l&agrave;-dedans ?</strong><br>Explications ludiques des diff&eacute;rentes saveurs.</li>
              <li><strong>Abracadabra… Transforme le "beurk" en "miam" !</strong><br>5 "tours" pour que votre enfant d&eacute;veloppe son alimentation.</li>
              <li><strong>Cr&eacute;er ton propre potager : un jeu d'enfants !</strong><br>Des astuces pour les jardiniers en herbe.</li>
              <li><strong>3 id&eacute;es recettes</strong><br>Repas complet, go&ucirc;ter et boisson</li>
              <li><strong>Et si tu… ?</strong><br>Des conseils utiles pour mieux manger.</li>
            </ul><a href="#" data-href="medias/carnetKIDS-FR.pdf" data-reveal-id="myModal" class="btn dl">T&eacute;l&eacute;charger gratuitement le carnet</a>
          </div>
        </div>
      </div>
    </section>
    <section class="simple_things animaltabs">
        <div class="container">
            <div class="row row-tab">
                <div class="row">
                    <div class="content grid-2"><img src="styles/images/pastille_new.png"></div>
                    <div class="content grid-8 grid-s-12" id="page-title">
                        <div class="title">D&eacute;couvrez les pastilles animaux &eacute;lues par les parents !</div>
                        <div class="title_sub">5 personnages haut en couleurs &agrave; d&eacute;couvrir !</div>
                    </div>
                </div>
                <div class="row row-animals">
                    <div class="content grid-2 grid-m-12">
                        <div class="tab tab-flamand">
                            <div class="background"><span class="title">Emma</span>la belle flamand rose aventuri&egrave;re</div>
                            <img src="styles/images/flamand.png" />
                        </div>
                    </div>
                    <div class="content grid-2 grid-m-12">
                        <div class="tab tab-singe">
                            <div class="background"><span class="title">Calvin</span>le chimpanz&eacute; sportif</div>
                            <img src="styles/images/singe.png" />
                        </div>
                    </div>
                    <div class="content grid-4 grid-m-12">
                        <div class="tab tab-tigre">
                            <div class="background"><span class="title">Johnny</span>le tigre gentil</div>
                            <img src="styles/images/tigre.png" />
                        </div>
                    </div>
                    <div class="content grid-2 grid-m-12">
                        <div class="tab tab-lion">
                            <div class="background"><span class="title">Arthur</span>le lion t&ecirc;te en l'air</div>
                            <img src="styles/images/lion.png" />
                        </div>
                    </div>
                    <div class="content grid-2 grid-m-12">
                        <div class="tab tab-perroquet">
                            <div class="background"><span class="title">Corentin</span>le perroquet blagueur</div>
                            <img src="styles/images/perroquet.png" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="content grid-12">
                        <a href="#" data-href="medias/monbento-pastilles-animaux.pdf" data-reveal-id="myModal" class="btn dl">T&eacute;l&eacute;charger gratuitement les pastilles</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="gift">
      <div class="container"><a href="#" data-href="medias/Kit-complet.pdf" data-reveal-id="myModal" class="btn dl all" data-docs="customPastille;pointAtelier;masque;carnetKIDS-FR">> Télécharger les pastilles <span class="clr_lightpink">+ </span>le jeu à relier <span class="clr_lightpink">+ </span>le masque <span class="clr_lightpink">+ </span>le carnet PDF</a></div>
    </section>
    <div id="myModal" class="reveal-modal">
      <div class="modal_content"><p>Veuillez entrer votre adresse mail afin de télécharger gratuitement la / les activités spéciale(s) enfants de monbento.</p></div>
      <div class="alert-box succes grid-12">Votre t&eacute;l&eacute;chargement va maintenant commencer</div>
      <div class="alert-box error grid-12">Votre adresse e-mail n'a pas pu être vérifiée</div>
      <div class="alert-box loading grid-12"></div>
      <div class="merge_input">
        <input type="text" id="email" placeholder="Adresse e-mail" class="input-text grid-8 grid-m-12">
        <input type="hidden" id="lang" value="fr" />
        <input type="button" value="Envoyer" class="btn grid-4 grid-m-12 right btn-submit">
      </div><a class="close-reveal-modal">&#215;</a>
    </div>
    <section class="social_footer">
      <div class="container"><a href="https://www.monbento.com/" target="_blank">Retrouvez tous l'univers monbento sur <strong>monbento.com</strong><i class="icon-arrow-right"></i></a>
        <ul>
          <li><a href="https://www.facebook.com/monbento" class="facebook" target="_blank"></a></li>
          <li><a href="https://twitter.com/monbento" class="twitter" target="_blank"></a></li>
          <li><a href="https://instagram.com/monbento" class="instagram" target="_blank"></a></li>
        </ul>
      </div>
    </section>
    <footer>
      <div class="container">
        <ul>
          <li><a href="https://www.monbento.com/mentions-legales-mon-bento.html" target="_blank">Mentions l&eacute;gales</a></li>
          <li> |</li>
          <li><a href="https://www.monbento.com/" target="_blank">monbento.com</a></li>
        </ul>
      </div>
    </footer>
    <!--Script-->
    <script src="js/plugins/jquery-1.11.1.min.js"></script>
    <script src="js/plugins/jquery.bxslider.min.js"></script>
    <script src="js/plugins/jquery.mobile.custom.min.js"></script>
    <script src="js/plugins/jquery.fitvids.js"></script>
    <script src="js/plugins/jquery.reveal.js"></script>
    <script src="js/app.js"></script>
    <script type="text/javascript">
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-7629814-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
    </script>
  </body>
</html>
