<?php 
// Winners
$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'winners ORDER BY date DESC LIMIT 9' ;
$winners = $db->selectAll($sql) ;

include_once 'html/end.php' ;
?>