<?php 
$path = dirname(dirname(__FILE__)) ;

include_once $path . '/config.php' ;

if(!empty($_POST))
{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ;
	$lot = $db->select($sql) ;
}

include_once 'html/kids.php' ;
?>