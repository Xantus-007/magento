<?php 
include_once '../config.php' ;
include_once 'session.php' ;

include 'CsvFile.php';

ini_set('memory_limit', '512M');

$contests = $db->selectAll('SELECT * FROM ' . DB_PREFIX . 'lots') ;

$aCols = array('Facebook ID');
foreach($contests as $c)
{
	array_push($aCols, 'Concours ' . $c->name_fr) ;
}
 
$aCols = array_merge($aCols, array('Email', 'Prénom', 'Nom', 'Sexe', 'Ville', 'Pays', 'Date de naissance', 'Optin1', 'Optin2')) ;

// Create new csv file
$csv = new CsvFile($aCols);

$lines = $db->selectAll('SELECT DISTINCT id_user, id_lot FROM ' . DB_PREFIX . 'participations') ;
$aUsers = array() ;
foreach($lines as $line)
{
	if(!array_key_exists($line->id_user, $aUsers)) $aUsers[$line->id_user] = array() ;
	
	array_push($aUsers[$line->id_user], $line->id_lot) ;
}

$users = $db->selectAll('SELECT * FROM ' . DB_PREFIX . 'users') ;
foreach($users as $line)
{
	$row = array() ;
	array_push($row, $line->id_user);
	
	foreach($contests as $c)
	{
		array_push($row, array_key_exists($line->id_user, $aUsers) && in_array($c->id_lot, $aUsers[$line->id_user]) ? 1 : 0) ;
	}
	
	array_push($row, $line->email);
	array_push($row, $line->fname);
	array_push($row, $line->name);
	array_push($row, $line->sexe);
	array_push($row, $line->city);
	array_push($row, $line->country);
	array_push($row, $line->birthdate);
	array_push($row, $line->optin1);
	array_push($row, $line->optin2);

	$csv->addLine($row) ;
}

header('Content-Description: File Transfer');
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="export_monbento.csv";');
$csv->write('php://output');