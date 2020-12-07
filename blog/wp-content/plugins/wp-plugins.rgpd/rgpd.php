<?php
/*
  Plugin Name: RGPD - Cookie management & form consent
  Description: Manage cookie according to RGPD and consent in form submission
  Version:     1.0.8
  Author:      Kévin Pignot <k.pignot@debussac.net>
 */

// URL to the fancybox directory
define('RGPD_ASSETS', plugin_dir_url(__FILE__) . 'assets');

function rgpdPluginActivation() {
    disableConflictPlugins();
}
register_activation_hook( __FILE__, 'rgpdPluginActivation' );

function disableConflictPlugins() {
    // Plugin List : 
    // MonsterInsight, Cookie Notice
    $pluginNameList = array(
        'google-analytics-for-wordpress/googleanalytics.php',
        'cookie-notice/cookie-notice.php'
    );
    foreach ($pluginNameList as $pluginName) {
        if (is_plugin_active($pluginName)) {
            deactivate_plugins($pluginName);
        }
    }
}

// Add tarteaucitron scripts/styles
add_action('wp_head', 'addRgpdScriptsAndStyles');
function addRgpdScriptsAndStyles() {
    echo '
    <link type="text/css" media="all" href="' . RGPD_ASSETS . '/css/tarteaucitron.css" rel="stylesheet" />
    <script type="text/javascript" src="' . RGPD_ASSETS . '/js/tarteaucitron/tarteaucitron.js"></script>
    <script type="text/javascript">
        var tarteaucitronForceLanguage = "fr";
        tarteaucitron.init({
            "hashtag": "#tarteaucitron",
            "highPrivacy": false,
            "orientation": "top",
            "adblocker": false,
            "showAlertSmall": false,
            "cookieslist": true,
            "removeCredit": true
        });
    </script>';
}

// Add tarteaucitron configuration
add_action('wp_footer', 'addTarteaucitronServices');
function addTarteaucitronServices() {
    echo '
    <script>
        var analyticsUa = "' . get_option('dbm_ua_code') . '";
        if (analyticsUa !== "") {
            tarteaucitron.user.analyticsUa = analyticsUa;
            tarteaucitron.user.analyticsMore = function () {
                ga("set", "forceSSL", true);
            };
            (tarteaucitron.job = tarteaucitron.job || []).push("analytics");
        }
        (tarteaucitron.job = tarteaucitron.job || []).push("facebook");
        (tarteaucitron.job = tarteaucitron.job || []).push("twitter");
        (tarteaucitron.job = tarteaucitron.job || []).push("gplus");
    </script>';
}

// Add admin configuration fields
add_filter('admin_init', 'addRgpdSettingFields');
function addRgpdSettingFields() {
    // Add setting field to configure cf7 form linked to newsletter form
    register_setting('general', 'cf7_form_newsletter', 'esc_attr');
    add_settings_field(
            'cf7_form_newsletter',
            'Identifiant formulaire CF7',
            'displayRgpdAdminField',
            'general',
            'default',
            array(
                0 => 'cf7_form_newsletter',
                'label_for' => 'cf7_form_newsletter'
            )
    );
    
    // Add setting field to configure Analytics ID
    register_setting('general', 'dbm_ua_code', 'esc_attr');
    add_settings_field(
            'dbm_ua_code',
            'Code Google Analytics UA',
            'displayRgpdAdminField',
            'general',
            'default',
            array(
                0 => 'dbm_ua_code',
                'label_for' => 'dbm_ua_code'
            )
    ); 
}
function displayRgpdAdminField($args) {
    $option = get_option($args[0]);
    echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
}

// Exclude tarteaucitron script from autoptimize
add_action('init', function() {
    if (!is_admin()) {
        checkPluginActivity();
    }
});
function checkPluginActivity() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if (is_plugin_active('autoptimize/autoptimize.php')) {
        function excludeTarteaucitronScripts($excludeJs) {
            if (!empty($excludeJs)) {
                $excludeJs .= ',';
            }
            $excludeJs .= 'tarteaucitron.js';
            return $excludeJs;
        }
        add_filter('autoptimize_filter_js_exclude', 'excludeTarteaucitronScripts', 10, 1);
    }
}

// Handle newsletter form submission to register it in flamingo
add_action('wp_loaded', 'controlNewsletterFormSubmission');
function controlNewsletterFormSubmission() {
    if (!isset($_SERVER['REQUEST_METHOD']) ||
        !function_exists('wpcf7_contact_form')) {
        return;
    }

    if ('POST' === $_SERVER['REQUEST_METHOD']) {
        if (isset($_POST['action']) && 
            $_POST['action'] === 'mailjet_subscribe_ajax_hook') {
            $cf7NewsletterFormId = (int) get_option('cf7_form_newsletter');
            if ($newsletterForm = wpcf7_contact_form($cf7NewsletterFormId)) {
                $error = empty($_POST['email']) ? 'Email field is empty' : false;
                if (false === $error) {
                    $error = empty($_POST['consent_form_submission']) ? 'Le champ consentement est obligatoire' : false;
                }
                if (false !== $error) {
                    _e($error, 'wp-mailjet-subscription-widget');
                    die;
                }
                $_POST['consent_form_submission'] = "J'accepte de recevoir la newsletter DBM. Mes données seront traitées conformément à la Protection des données et gestion des cookies que j'ai lu et accepté. Je peux me désabonner à tout moment.";
                $_POST['_wpcf7'] = $cf7NewsletterFormId;
                $_POST['_wpcf7_version'] = WPCF7_VERSION;
                $_POST['_wpcf7_locale'] = get_locale();
                $_POST['_wpcf7_unit_tag'] = 'wpcf7-f' . $cf7NewsletterFormId . '-o4';
                $_POST['_wpnonce'] = wpcf7_create_nonce($cf7NewsletterFormId);
                $newsletterForm->submit();
                // Unset fake post to avoid mailjet to pass it in url params
                unset($_POST['consent_form_submission']);
                unset($_POST['_wpcf7']);
                unset($_POST['_wpcf7_version']);
                unset($_POST['_wpcf7_locale']);
                unset($_POST['_wpcf7_unit_tag']);
                unset($_POST['_wpnonce']);
            }
        }
    }
}