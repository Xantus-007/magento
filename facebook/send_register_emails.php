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

$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'users WHERE sent IS NULL LIMIT 0,10' ; 
$lines  = $db->selectAll($sql) ;
foreach($lines as $line)
{
	$sql = 'UPDATE ' . DB_PREFIX . 'users SET sent = 1 WHERE id_user = ' . $line->id_user ;
	$db->query($sql) ;
	
	$config = substr($line->locale, 0, 2) == 'fr' ? $config_fr : $config_uk ;
	
	echo 'Envoi email ' . $line->email . ' => ' ;
    
	if($config->email_sender && $line->email)
	{
		//Mail
		$subject = replace_email_tags($config->register_subject, null, get_object_vars($line)) ;
		$message = replace_email_tags($config->register_message, null, get_object_vars($line)) ;
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
		
		$mail->AddAddress($line->email) ;
		//$mail->CharSet = 'UTF-8' ;
		$mail->SetFrom($config->email_sender, utf8_decode(NAME)) ;
		$mail->MsgHTML(utf8_decode($message)) ;
		$mail->Subject = utf8_decode($subject) ;
		
	    try
	    {
		    //Send the message
			$res = $mail->Send() ;
			
			echo $res ? 'OK' : 'NO' ;
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