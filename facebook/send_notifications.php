<?php 
include_once 'config.php' ;
include_once 'lib/facebook/facebook.php' ;

$sql = 'SELECT * FROM ' . DB_PREFIX . 'users_notifications_sends s LEFT JOIN ' . DB_PREFIX . 'users u ON s.id_user = u.id_user LEFT JOIN ' . DB_PREFIX . 'notifications n ON s.id_notif = n.id_notif WHERE s.sent IS NULL LIMIT 50' ;
$lines  = $db->selectAll($sql) ;

if(count($lines) == 0) exit ;

$fb = new Facebook(array('appId' => FB_ID, 'secret' => FB_SECRET, 'cookie' => false)) ;

$params = array() ;
$token = getAccessToken() ;
$params['access_token'] = $token ;

$aMethods = array() ;
foreach($lines as $line)
{
	$db->update(DB_PREFIX . 'users_notifications_sends', array('sent' => 1, 'date' => date('Y-m-d H:i:s')), 'id_send = ' . $line->id_send) ;
	
	$template = $line->text_en ;
	if(substr($line->locale, 0, 2) == 'fr')
	{
		$template = $line->text_fr ;
	}
	
	echo 'Add to ' . $line->id_user . ' => ' . $template . '/' ;
	
	$a = array('method' => 'POST', 'relative_url' => ($line->id_user . '/notifications')) ;
	$a['body'] = http_build_query(array("href" => '?notif=notif_' . $line->id_notif, 'ref' => $line->ref,  "template" => $template)) ;
	$a['access_token'] = $token ;
	
	array_push($aMethods, $a) ;

	echo '<br/>' ;
}


try
{
	$params['batch'] = $aMethods ;
	$r = $fb->api('', 'POST', $params) ;
	var_dump($r) ;

	$i = 0 ;
	foreach($lines as $line)
	{
		$resp = $r[$i] ;
		$code = $resp['code'] ;

		$sql = 'UPDATE ' . DB_PREFIX . 'users_notifications_sends SET result = "' . ($code == 200 ? 'OK' : 'KO') . '" WHERE id_user = ' . $line->id_user ;
		$db->query($sql) ;

		$i++ ;
	}

	echo 'OK' ;
}catch (Exception $e)
{
	echo 'NOT OK' ;
	var_dump($e) ;
}

//var_dump($_POST) ;
?>
<script>
<?php if(count($lines)) { ?>
	//window.location = 'send_notifications_day.php' ;
<?php } ?>
</script>