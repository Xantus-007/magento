<?php
ini_set('default_charset', 'UTF-8') ;

define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'ZzgxL2Fo');
define('DB_TABLE', 'monbento');
define('DB_PREFIX', 'facebook_uk_');

define('BASE', 'http://www.monbento.com/facebook_uk/') ;
define('NAME', 'MonBento Contest') ;

define('ADMIN_PWD', 'pZyjnZu') ;

define('FB_ID', '255321471160928');
define('FB_KEY', '255321471160928');
define('FB_SECRET', '4dee9f3006474df0c7e5d0becd80f8b1');
define('FB_URL_PAGE', 'http://www.facebook.com/monbento.europe') ;
define('FB_URL_TAB', 'http://www.facebook.com/monbento.europe?sk=app_255321471160928') ;
define('FB_PAGE_ID', '165475330151872');

define('GA_UID', '') ;
define('VERSION', 1) ;
define('SMTP', '') ;

$link = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
mysql_select_db(DB_TABLE, $link) ;
mysql_query("SET NAMES 'UTF8'");

include_once 'lib/Database.php' ;
global $db ;
$db = new Database() ;