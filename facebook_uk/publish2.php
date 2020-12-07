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

$sql = 'SELECT * FROM ' . DB_PREFIX . 'publications WHERE sent IS NULL AND date <= NOW() ORDER BY id_publication DESC LIMIT 0,1' ; 
$result = mysql_query($sql);
$row = mysql_fetch_object($result);
if($row)
{
	$sql = 'UPDATE ' . DB_PREFIX . 'publications SET sent = 1 AND date <= NOW() WHERE sent IS NULL' ;
	//$res = mysql_query($sql);
		
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $row->id_lot ; 
	$result = mysql_query($sql);
	$lot = mysql_fetch_object($result) ;
		
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = ' . $row->id_user1 ; 
	$result = mysql_query($sql);
	$user1 = mysql_fetch_object($result) ;
		
	$name 		= 'Win 1 ' . $lot->name . ' just by clicking! Try your luck now !' ;
	$picture 	= BASE . 'lots/' . $lot->id_lot . '.jpg' ;
	$caption	= 'MonBento.com' ;
	$description= 'MonBento.com enables you to win ' . $lot->nb . ' ' . $lot->name . ', Try your luck nowe' ;
	$link 		= FB_URL_TAB ;
	$message	= $user1->fname . ' won 1 ' . $lot->name . ' some days ago, Congratulations !' ;
	if($lot->state)
	{
	  	 $message .= ' At the moment 1 ' . $lot->name . ' is still at stake' ;
	}
	   
	$params = array('access_token' => substr($token, strlen('access_token=')), 'name' => $name, 'picture' => $picture, 'link' => $link, 'caption' => $caption, 'description' => $description, 'message' => $message) ;
		
	echo 'Publication sur ' . FB_PAGE_ID. ' => ' ;
	    
	//var_dump($params) ;
	try
	{
		$r = $facebook->api(FB_PAGE_ID . '/feed', 'POST', $params) ;
		
		echo 'OK' ;
		    
		var_dump($r) ;
	}catch(Exception $e)
	{
	    var_dump($e) ;
	    	
	    echo 'NO' ;
	    
	    echo '<br/>' ; 
		echo '<br/>' ; 
	
	    echo 'http://www.facebook.com/connect/uiserver.php?app_id=' . FB_ID . '&next=http://www.facebook.com/&display=popup&locale=fr_FR&perms=publish_stream&enable_profile_selector=1&fbconnect=true&legacy_return=1&method=permissions.request' ;
	}
}  
echo '<br/>' ; 

//var_dump($_POST) ;
?>