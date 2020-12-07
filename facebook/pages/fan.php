<?php
$path = dirname(dirname(__FILE__)) ;

include_once $path . '/config.php' ;

if(!$lot)
{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ;
	$lot = $db->select($sql) ;
}

$bCanPlay = true ;
$user = null ;
if($fid)
{
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'participations WHERE id_user = ' . $fid ;
	$sql.= ' AND SUBSTRING(date, 1, 10) = SUBSTRING(NOW(), 1, 10)' ;
	$row = $db->select($sql) ;
		
	if($row) $bCanPlay = false ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'users WHERE id_user = "' . mysql_escape_string($fid) . '"' ;
	$user = $db->select($sql) ;
}

// Winners
$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $lot->id_lot . ' ORDER BY date DESC LIMIT 15' ;
$winners = $db->selectAll($sql) ;

$aExcludesCountry = explode(',', $lot->exclude_countries) ;

if(!in_array(strtoupper($country), $aExcludesCountry) && $app_data != 'strangers')
{
	include_once 'html/fan.php' ;
}else{
	include_once 'pages/strangers.php' ;
	$page = 'strangers' ;
}
?>