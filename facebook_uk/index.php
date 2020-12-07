<?php 
include_once 'config.php' ;
include_once 'functions.php' ;

if(array_key_exists('request_ids', $_GET))
{
    include_once 'lib/facebook/facebook.php' ;
    
    $ch = curl_init();	
    $params = array('type' => 'client_cred', 'client_id' => FB_ID, 'client_secret' => FB_SECRET) ;
	
    $opts = Facebook::$CURL_OPTS;
    $opts[CURLOPT_POSTFIELDS] = $params;
    $opts[CURLOPT_URL] = 'https://graph.facebook.com/oauth/access_token';
    $opts[CURLOPT_SSL_VERIFYPEER] = false;
	$opts[CURLOPT_SSL_VERIFYHOST] = 2;
	
    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);
    
    $params = array('access_token' => substr($result, strlen('access_token='))) ;
    
    $fb = new Facebook(array('appId' => FB_ID, 'secret' => FB_SECRET, 'cookie' => true)) ;
    
    $aIds = explode(',', $_GET['request_ids']) ;
    
    //var_dump($aIds) ;
    foreach($aIds as $rid)
    {
    	try
	    {
    		$r = $fb->api($rid, $params) ;
	    	
	    	$from = $r['from']['id'] ;
	    	$to = $r['to']['id'] ;
	    	
	    	//var_dump($r) ;
	    	
	    	$data = array() ;
			$data['from']	= $from ;
			$data['to']		= $to ;
			$data['date']	= date('Y-m-d H:i:s') ;
			$db->insert(DB_PREFIX . 'users_invitations_clics', $data) ;
	    }catch (Exception $e) {}
	    
	    try
	    {
	    	$r = $fb->api($rid, 'DELETE') ;
	    }catch (Exception $e) {}
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"> 
<head>
	<meta http-equiv="Content-language" content="fr" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo NAME ; ?></title>
</head>
<body>
<script type="text/javascript">
	window.open('<?php echo FB_URL_TAB ; ?>', '_top') ;
</script>
</body>
</html>