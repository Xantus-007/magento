<?php
$path = dirname(dirname(__FILE__)) ;

include_once $path . '/config.php' ;
include_once $path . '/functions.php' ;

$id_user= array_key_exists('id_user', $_POST) ? $_POST['id_user'] : 0 ;
$avis1	= array_key_exists('avis1', $_POST) ? (int) $_POST['avis1'] : 0 ;
$avis2	= array_key_exists('avis2', $_POST) ? (int) $_POST['avis2'] : 0 ;
$avis3	= array_key_exists('avis3', $_POST) ? (int) $_POST['avis3'] : 0 ;

if($id_user && $avis1 && $avis2 && $avis3)
{
	$id_user = $user ? $user->id_user : $id_user ;
	
	$data = array() ;
	$data['id_user'] 	= $id_user ;
	$data['avis1'] 		= $avis1 ;
	$data['avis2'] 		= $avis2 ;
	$data['avis3'] 		= $avis3 ;
	$data['date'] 		= date('Y-m-d H:i:s') ;

	$db->insert(DB_PREFIX . 'users_kidsavis', $data) ;
	
	// Update Scenario
	$row = $db->select('SELECT * FROM ' . DB_PREFIX . 'users_scenarios_sends WHERE id_user = "' . mysql_escape_string($id_user) . '"') ;
	if($row && $row->sent != 1)
	{
		$db->update(DB_PREFIX . 'users_scenarios_sends', array('sent' => 2), 'id_send = ' . $row->id_send) ;
	}
}
?>