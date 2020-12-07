<?php

class Dbm_Utils_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getResizePath($width, $height, $options)
    {
        return Mage::getBaseDir().DS.'media'.DS.'resized'.DS.$width.DS.$height.DS. $this->_getPathFromOptions($options) /*base64_encode(serialize($bgColor))*/;
    }

    public function getResizeBaseUrl($width, $height, $options)
    {
        return Mage::getBaseUrl().'media/resized/'.$width.'/'.$height.'/'. $this->_getPathFromOptions($options, '/') /*base64_encode(serialize($bgColor))*/.'/';
    }

    public function resizeMediaImage($url, $width, $height, $options)
    {
        return $this->resizeImage('media'.DS.$url, $width, $height, $options);
    }

    public function resizeMediaCatalogImage($url, $width, $height, $options)
    {
        return $this->resizeImage('media'.DS . 'catalog' . $url, $width, $height, $options);
    }

    public function resizeProductImage($url, $width, $height, $options)
    {
        return $this->resizeImage('media'.DS . 'catalog' . DS .'product' . $url, $width, $height, $options);
    }

    public function resizeImage($url, $width, $height, $options = array())//$bgColor = array(255, 255, 255), $displayDefault = true)
    {
        $result = null;
        $useDefault = false;

        $bgColor = isset($options['bgColor']) ? $options['bgColor'] : array(255, 255, 255);
        
        if(substr($url, 0, 7) == 'http://')
        {
            $isUrl = true;
            $ext = substr($url, strrpos($url, '.') +1);
            
            $pathUrl = md5($url).'.'.$ext;
        }
        else
        {
            $isUrl = false;
            $pathUrl = $url;
        }
        
        if(!$isUrl && $url[0] == '/')
        {
            $url = substr($url, 1);
        }

        $useAdaptive = $this->_getOption($options, 'useAdaptive', false);
        unset($options['useAdaptive']);

        if(!$isUrl)
        {
            $imagePath = Mage::getBaseDir().DS.$url;
        }
        else
        {
            $imagePath = $url;
        }
   
        if((!file_exists($imagePath) || !is_file($imagePath))
                && 
            (!isset($options['allowPlaceholder']) || $options['allowPlaceholder'] == true))
        {
            
            $skinUrl = Mage::getDesign()->getSkinUrl('images/club/element/placeholder_photo.png');
            
            $baseUrl = Mage::getBaseUrl();
            $baseUrl = explode('/', $baseUrl);
            if(isset($baseUrl[3]))
            {
                unset($baseUrl[3]);
            }
 
            $baseUrl = implode('/', $baseUrl);

            $skinUrl = '/'.str_replace($baseUrl, '', $skinUrl);
            $imagePath = Mage::getBaseDir().$skinUrl;
        }

        $resizedPath = $this->getResizePath($width, $height, $options).DS.$pathUrl;

        if($isUrl || (file_exists($imagePath) && is_file($imagePath) && !file_exists($resizedPath)))
        {
            if(!$isUrl && pathinfo($imagePath, PATHINFO_EXTENSION) == 'png' && extension_loaded('imagick'))
            {
                $crop = $this->_getOption($options, 'crop', false);
                $this->_imageMagickResize($imagePath, $resizedPath, $width, $height, $crop);
            }
            else
            {
                $imageObj = new Varien_Image($imagePath);

                $imageObj->constrainOnly($this->_getOption($options, 'constrainOnly', false));
                $imageObj->keepAspectRatio($this->_getOption($options, 'keepAspectRatio', true));
                $imageObj->keepFrame($this->_getOption($options, 'keepFrame', true));
                $imageObj->quality($this->_getOption($options, 'quality', 88));
                $imageObj->setImageBackgroundColor($bgColor);
                $imageObj->backgroundColor($bgColor);
                $imageObj->setMode($this->_getOption($options, 'mode', null));
                if($useAdaptive)
                {
                    $imageObj->adaptiveResize($width, $height);
                }
                else
                {
                    $imageObj->resize($width, $height);
                }

                $imageObj->save($resizedPath);
            }
            
            $result = $this->getResizeBaseUrl($width, $height, $options).$pathUrl;
        }
        elseif(file_exists($resizedPath) && is_file($resizedPath))
        {
            $result = $this->getResizeBaseUrl($width, $height, $options).$pathUrl;
        }

        return $result;
    }

    public function getDefaultImagePlaceholder($imageType = 'image', $withBase = true)
    {
        $baseDir = '/images/catalog/product/placeholder/'.$imageType.'.jpg';

        if($withBase)
        {
            $baseDir = Mage::getDesign()->getSkinBaseDir().$baseDir;
        }

        return $baseDir;
    }

    protected function _getOption($values, $key, $default = null)
    {
        $result = is_null($default) ? null: $default;
        return isset($values[$key]) ? $values[$key] : $result;
    }

    protected function _getPathFromOptions($options, $separator = DS)
    {
        ksort($options);
        $keys = array();

        foreach($options as $var => $key)
        {
            if(!is_null($key))
            {
                if(is_array($key))
                {
                    $key = implode('-', $key);
                }

                $keys[] = strtolower(trim($var)).'-'.strtolower(trim($key));
            }
        }

        return implode($separator, $keys);
    }
    
    protected function _imageMagickResize($imagePath, $resizedPath, $width, $height, $crop = false)
    {
        $io = new Varien_Io_File();
        $io->checkAndCreateFolder($io->dirname($resizedPath));
            
        $imageObj = new Imagick($imagePath);
        $imageObj->setImageBackgroundColor(new ImagickPixel("transparent"));
        $w = $imageObj->getImageWidth();
        $h = $imageObj->getImageHeight();
        if(!$crop)
        {
            if($width > $w || $height > $h)
            {
                $imageObj->extentImage($width, $height, ($w-$width) / 2, ($h-$height) / 2);
            }
            elseif($width != $w || $height != $h)
            {
                $imageObj->scaleImage($width,$height, true);
            }
        }
        else
        {
            if ($w > $h)
            {
                $resize_w = $w * $height / $h;
                $resize_h = $height;
            }
            else 
            {
                $resize_w = $width;
                $resize_h = $h * $width / $w;
            }
            $imageObj->cropImage($width, $height, ($resize_w - $width) / 2, ($resize_h - $height) / 2);
        }
        
        $imageObj->setImageFormat("png32");
            
        $imageObj->writeImage($resizedPath);
        $imageObj->destroy();
    }
}
