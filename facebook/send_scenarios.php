<?php 
include_once 'config.php' ;
include_once 'lib/facebook/facebook.php' ;

$sql = 'SELECT * FROM ' . DB_PREFIX . 'users_scenarios_sends s LEFT JOIN ' . DB_PREFIX . 'users u ON s.id_user = u.id_user WHERE s.sent IS NULL AND s.date < NOW() LIMIT 50' ;
$lines  = $db->selectAll($sql) ;

if(count($lines) == 0) exit ;

$fb = new Facebook(array('appId' => FB_ID, 'secret' => FB_SECRET, 'cookie' => false)) ;

$params = array() ;
$token = getAccessToken() ;
$params['access_token'] = $token ;
$params['href'] = '?app_data=option_kids' ;
$params['ref'] = 'scenario_1' ;

foreach($lines as $line)
{
	$db->update(DB_PREFIX . 'users_scenarios_sends', array('sent' => 1), 'id_send = ' . $line->id_send) ;
	
	$template = $line->fname . ', discover future tokens before anyone else, and let us know what you think' ;
	if(substr($line->locale, 0, 2) == 'fr')
	{
		$template = $line->fname . ', découvrez en avant-première les futures pastilles et donnez votre avis' ;
	}
	
	echo 'Add to ' . $line->id_user . ' => ' . $template . '/' ;
	
	try
	{
		$params['template'] = $template ;
		$r = $fb->api('/' . $line->id_user . '/notifications', 'POST', $params) ;
		var_dump($r) ;
		
		$db->update(DB_PREFIX . 'users_scenarios_sends', array('result' => "OK"), 'id_send = ' . $line->id_send) ;
		
		echo 'OK' ;
	}catch (Exception $e)
	{
		$db->update(DB_PREFIX . 'users_scenarios_sends', array('result' => "KO"), 'id_send = ' . $line->id_send) ;
		
		echo 'NOT OK' ;
		var_dump($e) ;
	}

	echo '<br/>' ;
}


//var_dump($_POST) ;
?>
<script>
<?php if(count($lines)) { ?>
	//window.location = 'send_notifications_day.php' ;
<?php } ?>
</script>