<?php

class Dbm_Utils_Helper_File extends Mage_Core_Helper_Abstract
{
    /**
     * Sends appropriate headers for file download.
     * @param Zend_Controller_Response_Http $response
     * @param string $fileName
     * @param mixed $content
     * @param int $contentLength
     */
    public function forceFileDownload(Zend_Controller_Response_Http $response, $fileName, $content, $contentLength = null)
    {
        $response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
            ->setHeader('Last-Modified', date('r'));

        if(!is_null($content))
        {
            $response->setBody($content);
        }
    }

    /**
     * Recursive delete of a folder.
     *
     * @param string $dir
     */
    public function rrmdir($dir)
    {
        if ($handle = opendir($dir))
        {
            while (false !== ($entry = readdir($handle)))
            {
                if ($entry != "." && $entry != "..")
                {
                    if (is_dir($dir."/".$entry) === true)
                    {
                        rrmdir($dir."/".$entry);
                    }
                    else
                    {
                        unlink($dir."/".$entry);
                    }
                }
            }

            closedir($handle);
            rmdir($dir);
        }
    }
}