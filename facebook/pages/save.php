<?php
$path = dirname(dirname(__FILE__)) ;

include_once $path . '/config.php' ;

$token 		= array_key_exists('token', $_POST) && $_POST['token'] ? $_POST['token'] : '' ;
$dataSigned = array_key_exists('signed_request', $_POST) ? parse_signed_request($_POST['signed_request'], FB_SECRET) : null ;
$id_user	= array_key_exists('id_user', $_POST) ? $_POST['id_user'] : 0 ;

$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = "' . mysql_escape_string($id_user) . '"' ;
$row 	= $db->select($sql) ;

if(!$row || !array_key_exists('id_user', $row))
{
	include_once $path . '/lib/facebook/facebook.php' ;

	$fb = new Facebook(array('appId' => FB_ID, 'secret' => FB_SECRET, 'cookie' => true)) ;
	
	try
	{
		$params = array('access_token' => $token ? $token : $dataSigned['oauth_token']) ;
		
		$dataFb = $fb->api($id_user, $params) ;
		
		$data = array() ;
		$data['id_user'] 	= $id_user ;
		$data['birthdate'] 	= array_key_exists('birthday', $dataFb) ? getDateFromFaceBook($dataFb['birthday']) : '' ;
		$data['name'] 		= $dataFb['last_name'] ;
		$data['fname'] 		= $dataFb['first_name'] ;
		$data['email'] 		= $dataFb['email'] ;
		$data['sexe'] 		= $dataFb['gender'] ;
		$data['city'] 		= array_key_exists('location', $dataFb) ? $dataFb['location']['name'] : '' ;
		$data['optin1'] 	= 1 ;
		$data['sent'] 		= 1 ;
		$data['date'] 		= date('Y-m-d H:i:s') ;
		
		$db->insert(DB_PREFIX . 'users', $data) ;
	}catch (Exception $e) {var_dump($e);}
}else{
	$sql 	= 'UPDATE ' . DB_PREFIX . 'users SET optin1 = 1 WHERE id_user = "' . mysql_escape_string($id_user) . '"' ;
	$db->query($sql) ;
}
?>