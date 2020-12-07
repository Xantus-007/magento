<?php

require_once('abstract.php');

class Dbm_archiveCustomMedia extends Mage_Shell_Abstract
{
	private $_deleteFileAge = 3600*24*4; //4 days

    public function run()
    {
        ini_set('memory_limit', '1G');

		// Get real path for our folder
		$rootPath = Mage::getBaseDir('media').'/custom';

		$this->log($rootPath);

		// Initialize archive object
		$zip = new ZipArchive();
		$zip_open = $zip->open(Mage::getBaseDir('media').'/media_custom_archive.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

		$this->log('Zip open : '.$zip_open);

		if ($zip_open)
		{
			// Initialize empty "delete list"
			$filesToDelete = array();

			// Create recursive directory iterator
			/** @var SplFileInfo[] $files */
			$files = new RecursiveIteratorIterator(
			    new RecursiveDirectoryIterator($rootPath),
			    RecursiveIteratorIterator::LEAVES_ONLY
			);

			foreach ($files as $name => $file)
			{
			    // Skip directories (they would be added automatically)
			    if (!$file->isDir())
			    {
			        // Get real and relative path for current file
			        $filePath = $file->getRealPath();
			        $relativePath = substr($filePath, strlen($rootPath) + 1);

			        // Add current file to archive
			        $zip->addFile($filePath, $relativePath);

			        $dontDelete = false;
			        if(strpos($relativePath, '/') !== false)
			        {
			        	$pathParts = explode('/', $relativePath);
			        	if(strlen($pathParts[0]) == 4) $dontDelete = true;
			        }

			        // Add current file to "delete list"
			        // delete it later cause ZipArchive create archive only after calling close function and ZipArchive lock files until archive created)
			        if (time()-filemtime($file) > $this->_deleteFileAge && !$dontDelete)
			        {
			            $filesToDelete[] = $filePath;
			        }
			    }
			}

			// Zip archive will be created only after closing object
			if ($zip->close())
			{
				$this->log('Archive created');
				$this->log('Delete '.count($filesToDelete).' pictures.');
				// Delete all files from "delete list"
				foreach ($filesToDelete as $file)
				{
				    unlink($file);
				}
			}
			else
			{
				$this->log('Archive not create');
			}
		}
		else 
		{
			$this->log('Zip not open');
		}

    }

    public function log($data)
    {
        echo  $data."\r\n";
    }
}

$shell = new Dbm_archiveCustomMedia();
$shell->run();
