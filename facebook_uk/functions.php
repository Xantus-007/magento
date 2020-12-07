<?php
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
	$text = str_ireplace('[email]', $user['email'], $text) ;
	$text = str_ireplace('[name]', $user['name'], $text) ;
	$text = str_ireplace('[fname]', $user['fname'], $text) ;
	$text = str_ireplace('[lot]', $lot['name'], $text) ;
	$text = str_replace(chr(10), '<br/>', $text) ;
	
	return $text ; 
}