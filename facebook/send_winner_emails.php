<?php 
include_once 'config.php' ;
include_once 'functions.php' ;
include_once 'lib/class.phpmailer.php' ;

$config_fr = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration WHERE id_config = 1') ;
$config_uk = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration WHERE id_config = 2') ;

ob_start();
include 'email.php' ;
$email_body = ob_get_clean();
ob_end_clean();

$sql = 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE sent IS NULL LIMIT 0,1' ; 
$lines  = $db->selectAll($sql) ;
foreach($lines as $line)
{
	$sql = 'UPDATE ' . DB_PREFIX . 'winners SET sent = 1 WHERE id_winner = ' . $line->id_winner ;
	$db->query($sql) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $line->id_lot ; 
	$lot = $db->select($sql) ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = ' . $line->id_user ; 
	$user = $db->select($sql) ;
	
	$config = substr($user->locale, 0, 2) == 'fr' ? $config_fr : $config_uk ;
	
	echo 'Envoi email ' . $user->email . ' => ' ;
    
	if($config->email_sender && $user && $user->email)
	{
		//Mail
		$subject = replace_email_tags($config->email_subject, get_object_vars($lot), get_object_vars($user)) ;
		$message = replace_email_tags($config->email_message, get_object_vars($lot), get_object_vars($user)) ;
		$message = str_ireplace('€', '&euro;', $message) ;
		$message = str_ireplace('[message]', $message, $email_body) ;
		$message = str_ireplace('[header_img]', BASE . $config->image_header, $message) ;
		
		$mail = new PHPMailer() ;
		
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = "in.mailjet.com"; // sets the SMTP server
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = "d1486a70a6cec7de856bde216920f7e5"; // SMTP account username
		$mail->Password   = "31092b4a2a3bd83e990f74220b632c79";        // SMTP account password
		
		$mail->AddAddress($user->email) ;
		//$mail->AddBCC('oliv@makeet.com') ;
		//$mail->CharSet = 'UTF-8' ;
		$mail->SetFrom($config->email_sender, utf8_decode(NAME)) ;
		$mail->MsgHTML(utf8_decode($message)) ;
		$mail->Subject = utf8_decode($subject) ;
		
	    try
	    {
		    //Send the message
			$res = $mail->Send() ;
			
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