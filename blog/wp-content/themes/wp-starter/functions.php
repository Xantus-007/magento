<?php

use Dbm\Wordpress\Starter\Init;

$init = new Init();
$init->init();

/*
* Fix php mailer
*/
add_action('phpmailer_init', function($phpmailer){
    $phpmailer->Sender = $phpmailer->From;
});

/*
 * Fix admin links if admin url is customized
 */
function overrideAdminLink($url, $path, $scheme, $blog_id) {
    return str_replace("wp/wp-login.php", "dbm_admin/", $url);
}
add_filter('site_url', 'overrideAdminLink', 10, 4);

function overrideLoggedOutLink($redirect_to, $requested_redirect_to, $user) {
    return str_replace("wp-login.php", "", $redirect_to);
}
add_filter('logout_redirect', 'overrideLoggedOutLink', 10, 3);