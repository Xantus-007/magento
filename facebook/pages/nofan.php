<?php 
//$config = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration') ;
$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ;
$lot = $db->select($sql) ;

if($lot)
{
	$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' ORDER BY date DESC LIMIT 15' ;
}else{
	$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE ORDER BY date DESC LIMIT 15' ;
}
$winners = $db->selectAll($sql) ;

include_once 'html/nofan.php' ;
?>