<?php
/*
  Plugin Name: Contact Form 7 Response Magnific Popup
  Description: Contact Form 7 Response Message in Magnific Popup
  Version:     1.0
  Author:      Michaël Espeche <m.espeche@debussac.net>
 */

// URL to the fancybox directory
define('assets', plugin_dir_url(__FILE__) . 'assets');

// Add style and script
function cf7_enqueue_css_js()
{
    wp_enqueue_style('magnific.popup.style', assets . '/css/magnific-popup.css', '', '1.0.0');

    wp_enqueue_script('jquery');
    wp_enqueue_script('magnific.popup.script', assets . '/js/jquery.magnific-popup.min.js', array('jquery'), '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'cf7_enqueue_css_js', 100);

//Admin notice
function cf7_admin_notices()
{
    if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        echo '<div class="error"><p>Please install and active <a href="plugin-install.php?tab=search&s=contact+form+7"><strong>Contact Form 7</strong></a>.</p></div>';
    }
}

add_action('admin_notices', 'cf7_admin_notices');

// JS code for popup
function cf7_mpr_js()
{
    ?>
    <script>
        jQuery(function ($) {
            $('.wpcf7 form').each(function() {
                var $response = $('.wpcf7-response-output', $(this));
                var observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        if (mutation.attributeName === "class") {
                            if ($response.hasClass('wpcf7-display-none')) {
                                $.magnificPopup.open({
                                    closeOnContentClick: false,
                                    mainClass: 'my-mfp-zoom-in',
                                    overflowY: 'auto',
                                    fixedContentPos: true,
                                    fixedBgPos: true,
                                    removalDelay: 300,
                                    items: {
                                        src: '<div class="mp-popup-content">' + $response.html() + '</div>',
                                        type: 'inline'
                                    }
                                });
                            }
                        }
                    });
                });
                observer.observe($response[0], {
                    attributes: true
                });
            });
        });
    </script>
    <?php
}

add_action('wp_footer', 'cf7_mpr_js', 100);