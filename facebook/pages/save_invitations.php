<?php
$path = dirname(dirname(__FILE__)) ;

include_once $path . '/config.php' ;
include_once $path . '/functions.php' ;

$id_user	= array_key_exists('id_user', $_POST) ? $_POST['id_user'] : 0 ;

$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = "' . mysql_escape_string($id_user) . '"' ;
$row 	= $db->select($sql) ;

if($row && array_key_exists('friends', $_POST) && $_POST['friends'])
{
	foreach($_POST['friends'] as $to)
	{
		$data = array() ;
		$data['id_user'] 	= $id_user ;
		$data['request_id'] = $_POST['request_id'] ;
		$data['id_friend'] 	= $to ;
		$data['date'] 		= date('Y-m-d H:i:s') ;
	
		$db->insert(DB_PREFIX . 'users_invitations_sends', $data) ;
	}
}
?>