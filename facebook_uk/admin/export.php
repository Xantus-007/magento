<?php 
include_once '../config.php' ;
include_once 'session.php' ;

/** PHPExcel */
include '../lib/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include '../lib/PHPExcel/Writer/Excel5.php';

    // Create new PHPExcel object
$objPHPExcel = new PHPExcel();


// Write head
$aLetters = array('A', 'B', 'C', 'D', 'E', 'F') ;
$l = 0 ;
$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l] . '1', 'Facebook ID');
$objPHPExcel->getActiveSheet()->getColumnDimension($aLetters[$l++])->setWidth('20');
$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l] . '1', 'Email');
$objPHPExcel->getActiveSheet()->getColumnDimension($aLetters[$l++])->setWidth('40');
$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l] . '1', 'Prénom');
$objPHPExcel->getActiveSheet()->getColumnDimension($aLetters[$l++])->setWidth('20');
$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l] . '1', 'Nom');
$objPHPExcel->getActiveSheet()->getColumnDimension($aLetters[$l++])->setWidth('20');
$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l] . '1', 'Ville');
$objPHPExcel->getActiveSheet()->getColumnDimension($aLetters[$l++])->setWidth('40');
$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l] . '1', 'Date de naissance');
$objPHPExcel->getActiveSheet()->getColumnDimension($aLetters[$l++])->setWidth('20');

$lines = $db->selectAll('SELECT * FROM ' . DB_PREFIX . 'users') ;
$i = 2 ;
foreach($lines as $line)
{
	$l = 0 ;
	$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l++] . $i, $line->id_user);
	$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l++] . $i, $line->email);
	$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l++] . $i, $line->fname);
	$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l++] . $i, $line->name);
	$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l++] . $i, $line->city);
	$objPHPExcel->getActiveSheet()->SetCellValue($aLetters[$l++] . $i, $line->birthdate);
	$i++ ;
}
$file = 'export.xls' ;

$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
$objWriter->save($file);
	
$handle = fopen($file, 'rb');
$contents = fread ($handle, filesize($file));
fclose ($handle);
	
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="export.xls";');
echo $contents ;
unlink($file) ;	