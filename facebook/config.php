<?php
ini_set('default_charset', 'UTF-8') ;

define('DB_SERVER', '10.1.94.14');
define('DB_USER', 'highlight');
define('DB_PASS', 'boolIjnies3');
define('DB_TABLE', 'monbento_facebook');
define('DB_PREFIX', 'facebook_');

global $bHttps ;
$bHttps = array_key_exists('HTTPS', $_SERVER);

define('BASE', $bHttps  ? 'https://www.monbento.com/facebook/' : 'http://www.monbento.com/facebook/') ;
define('NAME', 'monbento') ;
define('COMPANY', 'monbento') ;

define('ADMIN_PWD', 'bento13') ;

define('FB_ID', '230344033646486');
define('FB_SECRET', '1e5c280b36d88d28ae2fad4b9f964932');
define('FB_URL_PAGE', 'http://www.facebook.com/monbento') ;
define('FB_URL_TAB', FB_URL_PAGE . '?sk=app_' . FB_ID) ;
define('FB_NAMESPACE', 'monbento-concours');

define('BITLY_LOGIN', 'monbento');
define('BITLY_KEY', 'R_6b12683d9f13318b5a455d5f23b1cf9d');

define('GA_UID', 'UA-46449992-1') ;
define('VERSION', 2) ;
define('SMTP', '') ;

$link = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
mysql_select_db(DB_TABLE, $link) ;
mysql_query("SET NAMES 'UTF8'");

include_once 'lib/Database.php' ;
global $db ;
$db = new Database() ;


include_once 'functions.php' ;