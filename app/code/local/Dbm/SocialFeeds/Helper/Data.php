<?php

class Dbm_SocialFeeds_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function formatTweet($text)
    {
        $text = preg_replace( "#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text );
        $text = preg_replace( "#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text );
        $text = preg_replace( "/@(\w+)/u", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $text );
        $text = preg_replace( "/#(\w+)/u", "<a href=\"http://twitter.com/search?q=%23\\1&src=hash\" target=\"_blank\">#\\1</a>", $text );
        return $text;
    }
    
    public function formatPost($text)
    {
        $text = preg_replace( "#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text );
        $text = preg_replace( "#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text );
        return $text;
    }
    
    public function formatDate($date)
    {
        $time = strtotime($date);
        return Mage::getModel('core/date')->date('d-m-Y', $time);
    }

    public function resizeAnyImage($fileName, $width, $height = null, $defaultImageType = null, $mode = 'inner_resize', $useAdaptative = true) 
    {
        $helper = Mage::helper('dbm_utils/image');
        //$folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $folderURL = Mage_Core_Model_Store::URL_TYPE_MEDIA;
        $imageURL = $folderURL . DS . $fileName;
        
        return $helper->resizeImage($imageURL, $width, $height, array('mode' => $mode, 'useAdaptive' => $useAdaptative, 'constrainOnly' => true, 'defaultImageType' => $defaultImageType));
    }
    
    public function getAndResizeImage($file, $folder, $fileName, $width, $height = null) 
    {
        $folderPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $folder;
        
        if (!is_dir($folderPath)) 
        {
            mkdir($folderPath);
            mkdir($folderPath . DS . 'tmp');
        }
        
        $tmpPath = $folderPath . DS . 'tmp' . DS . $fileName . '.jpg';
        try {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $file);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $imageContent = curl_exec($ch);
            curl_close($ch);
            
            file_put_contents($tmpPath, $imageContent);
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return false;
        }
     
        $newPath = $folderPath . DS . $width . DS . $fileName . '.jpg';
        //if width empty then return original size image's URL
        if ($width != '') {
            //if image has already resized then just return URL
            if (file_exists($tmpPath) && is_file($tmpPath) && !file_exists($newPath)) {
                $imageObj = new Varien_Image($tmpPath);
                $imageObj->constrainOnly(true);
                $imageObj->keepAspectRatio(true);
                $imageObj->keepFrame(false);
                $imageObj->setMode('inner_resize');
                if(is_null($height)) {
                    $imageObj->adaptiveResize($width);
                } else {
                    $imageObj->adaptiveResize($width, $height);
                }
                $imageObj->save($newPath);
                
                unlink($tmpPath);
            }
        }
        
        return true;
    }
    
    public function issetCacheImage($fileName, $folder, $smallSize)
    {
        $folderPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $folder;
        $basePath = $folderPath . DS . $smallSize . DS . $fileName . '.jpg';
        
        if (file_exists($basePath) && is_file($basePath)) 
        {
            return true;
        }
        return false;
    }

}