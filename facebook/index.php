<?php 
include_once 'config.php' ;
include_once 'functions.php' ;

$from = 0 ;
if(array_key_exists('request_ids', $_GET))
{
    include_once 'lib/facebook/facebook.php' ;
    
    $token = getAccessToken() ;
    
    $params = array() ;
    
    $fb = new Facebook(array('appId' => FB_ID, 'secret' => FB_SECRET, 'cookie' => true)) ;
    
    $aIds = explode(',', $_GET['request_ids']) ;
    
     //var_dump($aIds) ;
    foreach($aIds as $rid)
    {
    	try
	    {
    		$r = $fb->api($rid, $params) ;
	    	
	    	$id 	= $r['id'] ;
    		$from 	= $r['from']['id'] ;
	    	$to 	= $r['to']['id'] ;
	    	
	    	/*var_dump($r) ;
	    	exit ;*/
	    	
	    	$data = array() ;
	    	if($id)
	    	{
		    	$data['id']		= $id ;
				$data['from']	= $from ;
				$data['to']		= $to ;
				$data['date']	= date('Y-m-d H:i:s') ;
				$db->insert(DB_PREFIX . 'users_invitations_clics', $data) ;
	    	}
	    	
			setcookie('id_inv', $from, time() + 600, '/') ;
	    }catch (Exception $e) {}
	    
	    try
	    {
	    	$rrid = $rid . '_' . $to ;
	    	$r = $fb->api($rrid, 'DELETE') ;
	    }catch (Exception $e) {}
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"> 
<head>
	<meta http-equiv="Content-language" content="fr" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Concours <?php echo NAME ; ?></title>
</head>
<body>
<?php 
if(array_key_exists('notif', $_GET))
{
	$id_notif = (int) substr($_GET['notif'], 6) ;
	
	$notif = $db->select('SELECT * FROM ' . DB_PREFIX . 'notifications WHERE id_notif = ' . $id_notif) ;
?>

<script type="text/javascript">
	window.open('<?php echo $notif->url ; ?>', '_top') ;
</script>
<?php
}else{
?>
<script type="text/javascript">
	window.open('<?php echo FB_URL_TAB . ($from ? ('&app_data=pid_' . $from) : (array_key_exists('app_data', $_GET) ? ('&app_data=' . $_GET['app_data']) : '')) ; ?>', '_top') ;
</script>
<?php } ?>
</body>
</html>