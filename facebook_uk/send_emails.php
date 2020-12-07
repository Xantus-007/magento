<?php 
include_once 'config.php' ;
include_once 'functions.php' ;
require_once 'lib/sendMail.php';

if(SMTP) ini_set('SMTP', SMTP) ;

$config = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration') ;

$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE sent IS NULL LIMIT 0,10' ; 
$result = mysql_query($sql);
while($row = mysql_fetch_object($result))
{
	$sql = 'UPDATE ' . DB_PREFIX . 'winners SET sent = 1 WHERE id_winner = ' . $row->id_winner ;
	$res = mysql_query($sql);
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $row->id_lot ; 
	$res = mysql_query($sql);
	$lot = mysql_fetch_array($res) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = ' . $row->id_user ; 
	$res = mysql_query($sql);
	$user = mysql_fetch_array($res) ;
	
	echo 'Envoi email ' . $user['email'] . ' => ' ;
    
	if($config->email_sender && $user && $user['email'])
	{
		//Mail
		$subject = replace_email_tags($config->email_subject, $lot, $user) ;
		$message = replace_email_tags($config->email_message, $lot, $user) ;
		
		$mailDest = new sendMail();
		$mailDest->destMail(array($user['email']));
		$mailDest->headMail($config->email_sender, $config->email_sender);
		
	    try
	    {
		    //Send the message
			$mailDest->envoi($message, $subject) ;
			
			echo 'OK' ;
	    }catch(Exception $e)
	    {
	    	var_dump($e) ;
	    	
	    	echo 'NO' ;	
	    }
	}else{
		echo 'Aucune config' ;	
	}
    
    echo '<br/>' ; 
    
    sleep(1) ;
}

//var_dump($_POST) ;
?>