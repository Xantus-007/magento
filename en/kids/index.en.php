<?php
    $baseUrl = $_SERVER['SERVER_NAME'];
    if(strpos($baseUrl,'us') !== false || strpos($baseUrl,'hk') !== false) $baseUrl .= '/en';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>monbentoKids</title>
    <meta http-equiv="X-UA-Compatible" content="text/html" charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--[if lt IE 9]> <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script> <![endif]--><!--[if !IE]> -->
    <link rel="stylesheet" href="styles/css/styles.css"><![endif]-->
    <!--[if lt IE 10]>
    <link rel="stylesheet" href="styles/css/oldies.css"><![endif]-->
    <!--[if gt IE 10]>
    <link rel="stylesheet" href="styles/css/styles.css"><![endif]-->
    <script src="js/plugins/modernizr.js"></script>
    <link rel="icon" href="http://www.monbento.com/skin/frontend/default/monbento/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="http://www.monbento.com/skin/frontend/default/monbento/favicon.ico" type="image/x-icon">
  </head>
  <body class="monbentoKids_home monbentoKids_home_en">
    <header>
      <nav class="nav_header_homepage">
        <div class="container">
          <div class="goto_monbento grid-6 txt-align-left"><a href="http://<?php echo $baseUrl; ?>" title="Allez sur monbento.com" target="_blank">Go to <strong>monbento.com</strong></a></div>
          <div class="language grid-6 txt-align-right"><a href="index.php" title="Fran&ccedil;ais">Fr</a><a href="#" title="English" class="current">En</a></div>
        </div>
      </nav>
      <div class="container">
        <div class="punchline txt-align-right">Recipes, creations, surprises, and games …</div>
        <div class="punchline_sub txt-align-right">Fun times await !</div>
      </div>
    </header>
    <section class="cta_buy">
      <div class="container">
        <div class="lunch grid-6 grid-m-12 txt-align-left">
          <div class="title">At lunch time,</div>
          <div class="content"> the <strong>MB Tresor</strong> recharges<br>your child’s batteries !</div><a href="http://<?php echo $baseUrl; ?>/shop/lunch-box/bento-kids-mb-tresor.html" class="btn" target="_blank">Buy the MB Tresor</a>
        </div>
        <div class="break grid-6 grid-m-12 txt-align-right">
          <div class="title">At snack time,</div>
          <div class="content">the <strong>MB Gram</strong> is there for a<br>nourishing break from play time !</div><a href="http://<?php echo $baseUrl; ?>/shop/lunch-box/snack-box-kids-mb-gram.html" class="btn" target="_blank">Buy the MB Gram</a>
        </div>
      </div>
    </section>
    <section class="boosted_afternoon">
      <div class="container">
        <div class="embed_video">
          <iframe width="890" height="501" src="http://www.youtube.com/embed/Yde93UBSOeY?rel=0&amp;amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
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
        <div class="title">Your child is unique, and so is his lunch box!</div>
        <div class="title_sub">Personalise it by creating your own tabs.</div>
        <figure class="cd-image-container"><img src="images/img_gram_comp.jpg"><span data-type="original" class="cd-image-label">MB GRAM</span>
          <div class="cd-resize-img"><img src="images/img_tresor_comp.jpg"><span data-type="modified" class="cd-image-label">MB TRESOR</span>
          </div><span class="cd-handle"></span>
        </figure>
        <div class="btn_download_buy">
          <div class="btn_download grid-5 grid-m-12"><a href="#" data-href="medias/customPastille.pdf" data-reveal-id="myModal" class="btn dl">Download and print 12 tabs for customising</a>
          </div>
          <div class="btn_separator grid-2 hide-s">ou</div>
          <div class="btn_buy grid-5 grid-m-12"><a href="http://<?php echo $baseUrl; ?>/shop/kids.html" class="btn" target="_blank">Buy a MB Tresor or MB Gram</a>
            <p>Five tabs are included !</p>
          </div>
          <div class="item_bon_point"></div>
          <div class="item_mot_doux"></div>
        </div>
      </div>
    </section>
    <section class="your_game">
      <div class="container">
        <div class="title">Create your own games with your child</div>
        <div class="triptic_content">
          <div class="triptic_pink grid-4 grid-s-12">
            <div class="thumb"><img src="images/triptic_pink.jpg"></div>
            <div class="title">cindy <span class="clr_pink">&amp; </span>clara</div>
            <div class="content">Cindy likes to slip a note in her daughter’s lunch box, with a new one each day.</div>
            <div class="quote clr_pink">Clara can’t wait to read them.</div>
          </div>
          <div class="triptic_green grid-4 grid-s-12">
            <div class="thumb"><img src="images/triptic_green.jpg"></div>
            <div class="title">marie <span class="clr_green">&amp; </span>kevin</div>
            <div class="content">Marie loves to teach her son something new in a fun way.</div>
            <div class="quote clr_green">Kevin thinks the fruits and veggies his mum draws him are fun.</div>
          </div>
          <div class="triptic_yellow grid-4 grid-s-12">
            <div class="thumb"><img src="images/triptic_orange.jpg"></div>
            <div class="title">mark <span class="clr_orange">&amp; </span>sophie</div>
            <div class="content">Mark likes to surprise his daughter by hiding something behind the tabs.</div>
            <div class="quote clr_orange">Sophie is excited to discover notes and coins “hidden” by her darling daddy.</div>
          </div>
        </div>
      </div>
    </section>
    <section class="simple_things simple_things_grey">
      <div class="container">
        <div class="row row_dot_game">
          <div class="title">Make your child happy with the simple things</div>
          <div class="content grid-5 grid-l-7 right">
            <div class="inner_title">Connect the dots</div>
            <ul>
              <li>Print it.</li>
              <li>Slip it in your child’s box.</li>
              <li>Now you’re the best parent in the world!</li>
            </ul><a href="#" data-href="medias/pointAtelier.pdf" data-reveal-id="myModal" class="btn dl">Download the connect the dots picture for free</a>
          </div>
        </div>
      </div>
    </section>
    <section class="simple_things">
      <div class="container">
        <div class="row row_paper_mask">
          <div class="content grid-5 grid-l-7 left">
            <div class="inner_title">A paper mask</div>
            <ul>
              <li>Print it.</li>
              <li>Help your child cut it out and add a string to wear it.</li>
              <li>Take a photo of it and share it with us.</li>
            </ul><a href="#" data-href="medias/masque.pdf" data-reveal-id="myModal" class="btn dl">Donwload the mask for free</a>
          </div>
        </div>
      </div>
    </section>
    <section class="simple_things simple_things_grey">
      <div class="container">
        <div class="row row_recette">
          <div class="content grid-5 grid-l-7 right">
            <div class="inner_title">Help your child discover new flavours with this free booklet created just for him.</div>
            <ul>
              <li><strong>What’s going on in there?</strong><br>Fun explanations of different flavours.</li>
              <li><strong>Abracadabra… Turn "yuck" into "yum"!</strong><br>Five "tricks" to help your child try new foods.</li>
              <li><strong>Create your own vegetable garden: it’s easy!</strong><br>Tips for budding gardeners.</li>
              <li><strong>Three recipe ideas:</strong><br>a complete meal, snack and beverage.</li>
              <li><strong>What if you… ?</strong><br> Useful tips for healthier eating.</li>
            </ul><a href="#" data-href="medias/carnetKIDS-EN.pdf" data-reveal-id="myModal" class="btn dl">Download the PDF booklet for free</a>
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
                        <div class="title">Discover the tabs from the animal theme elected by the parents !</div>
                        <div class="title_sub">5 colorful characters to discover !</div>
                    </div>
                </div>
                <div class="row row-animals">
                    <div class="content grid-2 grid-m-12">
                        <div class="tab tab-flamand">
                            <div class="background"><span class="title">Emma</span>the beautiful and adventurous flamingo</div>
                            <img src="styles/images/flamand.png" />
                        </div>
                    </div>
                    <div class="content grid-2 grid-m-12">
                        <div class="tab tab-singe">
                            <div class="background"><span class="title">Calvin</span>the sporty chimpanzee</div>
                            <img src="styles/images/singe.png" />
                        </div>
                    </div>
                    <div class="content grid-4 grid-m-12">
                        <div class="tab tab-tigre">
                            <div class="background"><span class="title">Johnny</span>the nice tiger</div>
                            <img src="styles/images/tigre.png" />
                        </div>
                    </div>
                    <div class="content grid-2 grid-m-12">
                        <div class="tab tab-lion">
                            <div class="background"><span class="title">Arthur</span>the distracted lion</div>
                            <img src="styles/images/lion.png" />
                        </div>
                    </div>
                    <div class="content grid-2 grid-m-12">
                        <div class="tab tab-perroquet">
                            <div class="background"><span class="title">Corentin</span>the witty parrot</div>
                            <img src="styles/images/perroquet.png" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="content grid-12">
                        <a href="#" data-href="medias/monbento-pastilles-animaux.pdf" data-reveal-id="myModal" class="btn dl">Download the tabs for free</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="gift">
      <div class="container"><a href="#" data-href="medias/Kit-complet.pdf" data-reveal-id="myModal" class="btn dl all" data-docs="customPastille;pointAtelier;masque;carnetKIDS-EN">> Download the tabs <span class="clr_lightpink">+ </span>connect the dots <span class="clr_lightpink">+ </span>mask <span class="clr_lightpink">+ </span>PDF booklet</a></div>
    </section>
    <div id="myModal" class="reveal-modal">
      <div class="modal_content"><p>Please enter your e-mail address and download for free the monbento special activity(ies) for children.</p></div>
      <div class="alert-box succes grid-12">Votre t&eacute;l&eacute;chargement va maintenant commencer</div>
      <div class="alert-box error grid-12">Votre adresse e-mail n'a pas pu être vérifiée</div>
      <div class="alert-box loading grid-12"></div>
      <div class="merge_input">
        <input type="text" id="email" placeholder="Your e-mail" class="input-text grid-8 grid-m-12">
        <input type="hidden" id="lang" value="en" />
        <input type="button" value="Valid" class="btn grid-4 grid-m-12 right btn-submit">
      </div><a class="close-reveal-modal">&#215;</a>
    </div>
    <section class="social_footer">
      <div class="container"><a href="http://<?php echo $baseUrl; ?>" target="_blank">Check out the entire monbento collection on <strong>monbento.com</strong><i class="icon-arrow-right"></i></a>
        <ul>
          <li><a href="https://www.facebook.com/monbento" class="facebook" target="_blank"></a></li>
          <li><a href="https://twitter.com/monbento" class="twitter" target="_blank"></a></li>
          <li><a href="http://instagram.com/monbento" class="instagram" target="_blank"></a></li>
        </ul>
      </div>
    </section>
    <footer>
      <div class="container">
        <ul>
          <li><a href="http://<?php echo $baseUrl; ?>/legals-mon-bento.html" target="_blank">Legal notice</a></li>
          <li> |</li>
          <li><a href="http://<?php echo $baseUrl; ?>" target="_blank">monbento.com</a></li>
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