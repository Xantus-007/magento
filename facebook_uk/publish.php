<?php 
include_once 'config.php' ;
include_once 'functions.php' ;
include_once 'lib/facebook/facebook.php' ;

$facebook = new Facebook(FB_ID, FB_SECRET) ;

$ch = curl_init();	
$params = array('type' => 'client_cred', 'client_id' => FB_ID, 'client_secret' => FB_SECRET) ;

$opts =  array(
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_USERAGENT      => 'facebook-php-2.0',
  );
$opts[CURLOPT_POSTFIELDS] = $params;
$opts[CURLOPT_URL] = 'https://graph.facebook.com/oauth/access_token';
$opts[CURLOPT_SSL_VERIFYPEER] = false;
$opts[CURLOPT_SSL_VERIFYHOST] = 2;

curl_setopt_array($ch, $opts);
$token = curl_exec($ch);

//var_dump($token) ;

$sql = 'SELECT * FROM ' . DB_PREFIX . 'participations WHERE sent IS NULL LIMIT 0,30' ; 
$result = mysql_query($sql);
while($row = mysql_fetch_object($result))
{
	$sql = 'UPDATE ' . DB_PREFIX . 'participations SET sent = 1 WHERE id_participation = ' . $row->id_participation ;
	$res = mysql_query($sql);
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $row->id_lot ; 
	$res = mysql_query($sql);
	$lot = mysql_fetch_object($res) ;
	
	$name 		= 'Win 1 ' . $lot->name . ' just by clicking! Try your luck now !' ;
    $picture 	= BASE . 'lots/' . $lot->id_lot . '.jpg' ;
    $caption	= 'MonBento.com' ;
    $description= 'MonBento.com enables you to win ' . $lot->nb . ' ' . $lot->name . ', Try your luck now' ;
    $link 		= FB_URL_TAB ;
   
    $params = array('access_token' => substr($token, strlen('access_token=')), 'name' => $name, 'picture' => $picture, 'link' => $link, 'caption' => $caption, 'description' => $description) ;
	
    echo 'Publication sur ' . $row->id_user . ' => ' ;
    
    //var_dump($params) ;
    try
    {
	    $r = $facebook->api($row->id_user . '/feed', 'POST', $params) ;
	
	    echo 'OK' ;
	    
	    //var_dump($r) ;
    }catch(Exception $e)
    {
    	var_dump($e) ;
    	
    	echo 'NO' ;	
    }
    
    echo '<br/>' ; 
}

//var_dump($_POST) ;
?>