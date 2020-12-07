<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/
function clean_file_name($file)
{
  // nettoyage du nom de fichier
  $file = strtr($file,"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
  $file = strtolower($file);
  $file = eregi_replace("[^a-z0-9\.\-]","",$file);
  return $file;
}

$verifyToken = md5('unique_salt' . $_POST['timestamp']);
// Define a destination
$targetFolder = '/media/recrutement'. '/' . $verifyToken; // Relative to the root

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	if (!is_dir($targetPath)) mkdir($targetPath);
	$targetFile = rtrim($targetPath,'/') .  '/'. clean_file_name($_FILES['Filedata']['name']);
	
	// Validate the file type
	$fileTypes = array('doc','docx','pdf','rtf','odt'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		echo $targetFolder . '/' . clean_file_name($_FILES['Filedata']['name']);
	} else {
		echo 'Invalid';
	}
}
?>