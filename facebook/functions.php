<?php
global $config, $dataSigned, $bFan, $fid, $signed, $country, $locale, $user, $nbPlay, $app_data ;
function initFacebook()
{
	global $db, $config, $dataSigned, $bFan, $fid, $signed, $country, $locale, $user, $nbPlay, $app_data ;
	
	$dataSigned = !empty($_POST) ? parse_signed_request($_POST['signed_request'], FB_SECRET) : null ;
	$bFan 		= $dataSigned ? $dataSigned['page']['liked'] : true ;
	$fid 		= $dataSigned && array_key_exists('user_id', $dataSigned) ? $dataSigned['user_id'] : 0 ;
	$signed		= !empty($_POST) ? $_POST['signed_request'] : '' ;
	$app_data	= $dataSigned && array_key_exists('app_data', $dataSigned) ? $dataSigned['app_data'] : -1 ;
	$country	= $dataSigned && array_key_exists('user', $dataSigned) ? $dataSigned['user']['country'] : 'fr' ;
	$locale		= $dataSigned && array_key_exists('user', $dataSigned) ? $dataSigned['user']['locale'] : 'fr_FR' ;
	//$fid 		= 1120826219 ;
	
	$user = null ;
	$nbPlay = 0 ;
	if($fid)
	{
		$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = "' . mysql_escape_string($fid) . '"' ;
		$user 	= $db->select($sql) ;
		if($user)
		{
			$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations WHERE id_user = ' . $fid ;
			$row = $db->select($sql) ;
			$nbPlay = $row->nb ;
			
			if(!array_key_exists('user', $dataSigned))
			{
				$locale = $user->locale ;
				if($user->country)
				{
					$country = $user->country ;
				}else{
					$country = substr($user->locale, 3) ;
				}
			}
		}else{
			$fid = 0 ;
		}
	}
	
	if(array_key_exists('app_data', $_GET)) $app_data = $_GET['app_data'] ;
	
	if($app_data == 'lang_uk') $locale = 'en_US' ;
	
	if(substr($locale, 0, 2) == 'fr')
	{
		$config = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration WHERE id_config = 1') ;
	}else{
		$config = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration WHERE id_config = 2') ;
	}
}
initFacebook() ;

function parse_signed_request($signed_request, $secret)
{
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input)
{
  return base64_decode(strtr($input, '-_', '+/'));
}

function getParam($key, $aData, $default = '')
{
	return array_key_exists($key, $aData) ? $aData[$key] : $default ;
}

function getAccessToken()
{
	$ch = curl_init();	
	$params = array('grant_type' => 'client_credentials', 'client_id' => FB_ID, 'client_secret' => FB_SECRET) ;
	
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
	
	return substr($token, strlen('access_token=')) ;
}

function _mktime($date)
{
	if(!$date) return mktime() ;
	
	$h	= substr($date, 11, 2) ;
	$mn	= substr($date, 14, 2) ;
	$s	= substr($date, 17, 2) ;
	
	$d = substr($date, 8, 2) ;
	$m = substr($date, 5, 2) ;
	$y = substr($date, 0, 4) ;
	
	return mktime($h, $mn, $s, $m, $d, $y) ;
}

function getDateFromFaceBook($birthday)
{
	if(strlen($birthday) == 5)
	{
		return '0000-' . str_replace('/', '-', $birthday) ;
	}else{
		return substr($birthday, 6, 4) . '-' . substr($birthday, 0, 2) . '-' . substr($birthday, 3, 2) ;
	}
}

function formatDate($sDate, $format = '%d %b %Y')
{
	setlocale(LC_ALL, 'fr_FR');
	return utf8_encode(strftime(utf8_decode($format), is_numeric($sDate) ? $sDate : _mktime($sDate))) ;
}

function replace_email_tags($text, $lot, $user)
{
	global $locale ;
	
	if($user)
	{
		$text = str_ireplace('[email]', $user['email'], $text) ;
		$text = str_ireplace('[name]', $user['name'], $text) ;
		$text = str_ireplace('[fname]', $user['fname'], $text) ;
		$text = str_ireplace('[link_parrain]', $user['bitly'], $text) ;
		$text = str_ireplace('[link_parrain2]', str_replace('http://', '', $user['bitly']), $text) ;
	}
	if($lot)
	{
		$text = str_ireplace('[lot]', substr($locale, 0, 2) == 'fr' ? $lot['name_fr'] : $lot['name_en'], $text) ;
		$text = str_ireplace('[frequency]', $lot['frequency'], $text) ;
	}
	$text = str_replace(chr(13), '<br/>', $text) ;
	
	return $text ; 
}

function replace_tags($text, $lot = null, $user = null)
{
	return replace_email_tags($text, $lot ? get_object_vars($lot) : null, $user ? get_object_vars($user) : null) ;
}

function hyperlink($str)
{
	if($str=='' or !preg_match('/(ftp|http|www\.|@)/i', $str)) {
		return $str;
	}

	$str = preg_replace("/([ \t]|^)www\./i", "\\1http://www.", $str);
	$str = preg_replace("/([ \t]|^)ftp\./i", "\\1ftp://ftp.", $str);
	$str = preg_replace("/(http:\/\/[^ )\r\n!]+)/i", "<a target=\"_blank\" href=\"\\1\">\\1</a>", $str);
	$str = preg_replace("/(https:\/\/[^ )\r\n!]+)/i", "<a target=\"_blank\" href=\"\\1\">\\1</a>", $str);
	$str = preg_replace("/(ftp:\/\/[^ )\r\n!]+)/i", "<a target=\"_blank\" href=\"\\1\">\\1</a>", $str);
	$str = preg_replace("/([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))/i", "<a target=\"_blank\" href=\"mailto:\\1\">\\1</a>", $str);

	return $str;
}

function trunc($text, $maxLength = 78, $bDot = true)
{
    if (strlen($text) > $maxLength && $maxLength)
	{
		$text = substr($text, 0, $maxLength);
		$space = strrpos($text, ' ');
		if($space === false) $space = $maxLength ;
		$text = substr($text, 0, $space);
		if($bDot) $text = $text . '...' ;
	}
	
	return $text ;
}

function uploadFile($aFile, $dir = 'images')
{
	$a = explode('.', $aFile['name']) ;
	$ext = strtolower($a[count($a) - 1]) ;

	$path = dirname(__FILE__) . '/' . $dir . '/' ;
	$file = uniqid() . '.' . $ext ;

	move_uploaded_file($aFile['tmp_name'], $path . $file) ;

	return $dir . '/' . $file ;
}

function catch_error($e)
{
	global $db ;
	//include_once('lib/class.phpmailer.php');
	
	$message = $e->getMessage()."\n".$e->getTraceAsString()."\n\n";
	
	ob_start();
    var_dump($_SERVER);
    echo '<br/>GET :<br/>' ;
    var_dump($_GET);
    echo '<br/>POST :<br/>' ;
    var_dump($_POST);
	$message .= ob_get_clean();	
	
	/*$mail = new PHPMailer() ;
	$mail->AddAddress('coste.oliv@gmail.com') ;
	$mail->SetFrom('error@boostmyfans.com') ;
	$mail->MsgHTML($message) ;
	$mail->Subject = 'Error ' . NAME ;*/
		
    try
    {
	    //Send the message
		//$res = $mail->Send() ;
		//var_dump($e) ;
    }catch(Exception $e){}
}

function get_bitly_short_url($url, $format='txt')
{
	$connectURL = 'http://api.bit.ly/v3/shorten?login=' . BITLY_LOGIN . '&apiKey=' . BITLY_KEY . '&uri='.urlencode($url).'&format='.$format;
	return trim(curl_get_result($connectURL)) ;
}

function curl_get_result($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function my_utf8_decode($str)
{
	return iconv("UTF-8", "CP1252", $str) ;
}

function my_utf8_encode($str)
{
	return iconv("CP1252", "UTF-8", $str) ;
}

function publishOpenGraph($id_lot, $action = 'try_to_win')
{
	include_once 'lib/facebook/facebook.php' ;
	$facebook = new Facebook(array('appId' => FB_ID, 'secret' => FB_SECRET, 'cookie' => true)) ;

	$result = null ;
	try
	{
		$token	= getParam('token', $_POST, '') ;

		if(array_key_exists('signed_request', $_POST) && $_POST['signed_request'])
		{
			$dataSigned = parse_signed_request($_POST['signed_request'], FB_SECRET) ;
			$token = $dataSigned['oauth_token'] ;
		}

		$params = array('object' => BASE . 'lot.php?id_lot=' . $id_lot, 'access_token' => $token) ;
		$result = $facebook->api('/me/' . FB_NAMESPACE . ':' . $action, 'POST', $params) ;
	}catch (Exception $e) {
		catch_error($e);
	}
}