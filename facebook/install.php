<?php
include_once 'config.php' ;

$url = 'http://www.facebook.com/dialog/pagetab?app_id=' . FB_ID . '&redirect_uri=' . BASE ;

header('Location:' . $url)  ;
exit ;