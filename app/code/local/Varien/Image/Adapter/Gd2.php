<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Varien
 * @package    Varien_Image
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Varien_Image_Adapter_Gd2 extends Varien_Image_Adapter_Abstract {

    protected $_requiredExtensions = Array("gd");
    private static $_callbacks = array(
        IMAGETYPE_GIF => array('output' => 'imagegif', 'create' => 'imagecreatefromgif'),
        IMAGETYPE_JPEG => array('output' => 'imagejpeg', 'create' => 'imagecreatefromjpeg'),
        IMAGETYPE_PNG => array('output' => 'imagepng', 'create' => 'imagecreatefrompng'),
        IMAGETYPE_XBM => array('output' => 'imagexbm', 'create' => 'imagecreatefromxbm'),
        IMAGETYPE_WBMP => array('output' => 'imagewbmp', 'create' => 'imagecreatefromxbm'),
    );

    protected $_mode = null;

    const RESIZE_INNER='inner_resize';
    const RESIZE_OUTER='outer_resize';
    const RESIZE_STRETCH='stretch_resize';

    /**
     * Whether image was resized or not
     *
     * @var bool
     */
    protected $_resized = false;

    public function open($filename) {
        $this->_fileName = $filename;
        $this->getMimeType();
        $this->_getFileAttributes();
        $this->_imageHandler = call_user_func($this->_getCallback('create'), $this->_fileName);
    }

    public function save($destination = null, $newName = null) {
        $fileName = (!isset($destination) ) ? $this->_fileName : $destination;

        if (isset($destination) && isset($newName)) {
            $fileName = $destination . "/" . $newName;
        } elseif (isset($destination) && !isset($newName)) {
            $info = pathinfo($destination);
            $fileName = $destination;
            $destination = $info['dirname'];
        } elseif (!isset($destination) && isset($newName)) {
            $fileName = $this->_fileSrcPath . "/" . $newName;
        } else {
            $fileName = $this->_fileSrcPath . $this->_fileSrcName;
        }

        $destinationDir = ( isset($destination) ) ? $destination : $this->_fileSrcPath;

        if (!is_writable($destinationDir)) {
            try {
                $io = new Varien_Io_File();
                $io->mkdir($destination);
            } catch (Exception $e) {
                throw new Exception("Unable to write file into directory '{$destinationDir}'. Access forbidden.");
            }
        }

        if (!$this->_resized) {
            // keep alpha transparency
            $isAlpha = false;
            $isTrueColor = false;
            $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha, $isTrueColor);
            if ($isAlpha) {
                if ($isTrueColor) {
                    $newImage = imagecreatetruecolor($this->_imageSrcWidth, $this->_imageSrcHeight);
                } else {
                    $newImage = imagecreate($this->_imageSrcWidth, $this->_imageSrcHeight);
                }
                $this->_fillBackgroundColor($newImage);
                imagecopy(
                        $newImage, $this->_imageHandler, 0, 0, 0, 0, $this->_imageSrcWidth, $this->_imageSrcHeight
                );
                $this->_imageHandler = $newImage;
            }
        }

        $functionParameters = array();
        $functionParameters[] = $this->_imageHandler;
        $functionParameters[] = $fileName;

        // set quality param for JPG file type
        if (!is_null($this->quality()) && $this->_fileType == IMAGETYPE_JPEG) {
            $functionParameters[] = $this->quality();
        }

        // set quality param for PNG file type
        if (!is_null($this->quality()) && $this->_fileType == IMAGETYPE_PNG) {
            $quality = round(($this->quality() / 100) * 10);
            if ($quality < 1) {
                $quality = 1;
            } elseif ($quality > 10) {
                $quality = 10;
            }
            $quality = 10 - $quality;
            $functionParameters[] = $quality;
        }

        call_user_func_array($this->_getCallback('output'), $functionParameters);
    }

    public function display() {
        header("Content-type: ".$this->getMimeTypeWithOutFileType());
        call_user_func($this->_getCallback('output'), $this->_imageHandler);
    }

    /**
     * Obtain function name, basing on image type and callback type
     *
     * @param string $callbackType
     * @param int $fileType
     * @return string
     * @throws Exception
     */
    private function _getCallback($callbackType, $fileType = null, $unsupportedText = 'Unsupported image format.') {
        if (null === $fileType) {
            $fileType = $this->_fileType;
        }
        if (empty(self::$_callbacks[$fileType])) {
            throw new Exception($unsupportedText);
        }
        if (empty(self::$_callbacks[$fileType][$callbackType])) {
            throw new Exception('Callback not found.');
        }
        return self::$_callbacks[$fileType][$callbackType];
    }

    private function _fillBackgroundColor(&$imageResourceTo) {
        // try to keep transparency, if any
        //if ($this->_keepTransparency) {
	if (true) {
            $isAlpha = false;
            $transparentIndex = $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha);
            try {
                // fill truecolor png with alpha transparency
                if ($isAlpha) {

                    if (!imagealphablending($imageResourceTo, false)) {
                        throw new Exception('Failed to set alpha blending for PNG image.');
                    }
                    $transparentAlphaColor = imagecolorallocatealpha($imageResourceTo, 0, 0, 0, 127);
                    if (false === $transparentAlphaColor) {
                        throw new Exception('Failed to allocate alpha transparency for PNG image.');
                    }
                    if (!imagefill($imageResourceTo, 0, 0, $transparentAlphaColor)) {
                        throw new Exception('Failed to fill PNG image with alpha transparency.');
                    }
                    if (!imagesavealpha($imageResourceTo, true)) {
                        throw new Exception('Failed to save alpha transparency into PNG image.');
                    }

                    return $transparentAlphaColor;
                }
                // fill image with indexed non-alpha transparency
                elseif (false !== $transparentIndex) {
                    $transparentColor = false;
                    if ($transparentIndex >= 0 && $transparentIndex <= imagecolorstotal($this->_imageHandler)) {
                        list($r, $g, $b) = array_values(imagecolorsforindex($this->_imageHandler, $transparentIndex));
                        $transparentColor = imagecolorallocate($imageResourceTo, $r, $g, $b);
                    }
                    if (false === $transparentColor) {
                        throw new Exception('Failed to allocate transparent color for image.');
                    }
                    if (!imagefill($imageResourceTo, 0, 0, $transparentColor)) {
                        throw new Exception('Failed to fill image with transparency.');
                    }
                    imagecolortransparent($imageResourceTo, $transparentColor);
                    return $transparentColor;
                }
            } catch (Exception $e) {
                // fallback to default background color
            }
        }

        list($r, $g, $b) = $this->_backgroundColor;
        $color = imagecolorallocate($imageResourceTo, $r, $g, $b);
        if (!imagefill($imageResourceTo, 0, 0, $color)) {
            throw new Exception("Failed to fill image background with color {$r} {$g} {$b}.");
        }

        return $color;
    }

    /**
     * Gives true for a PNG with alpha, false otherwise
     *
     * @param string $fileName
     * @return boolean
     */
    public function checkAlpha($fileName) {
        return ((ord(file_get_contents($fileName, false, null, 25, 1)) & 6) & 4) == 4;
    }

    private function _getTransparency($imageResource, $fileType, &$isAlpha = false, &$isTrueColor = false) {
        $isAlpha = false;
        $isTrueColor = false;
        // assume that transparency is supported by gif/png only
        if ((IMAGETYPE_GIF === $fileType) || (IMAGETYPE_PNG === $fileType)) {
            // check for specific transparent color
            $transparentIndex = imagecolortransparent($imageResource);
            if ($transparentIndex >= 0) {
                return $transparentIndex;
            }
            // assume that truecolor PNG has transparency
            elseif (IMAGETYPE_PNG === $fileType) {
                $isAlpha = $this->checkAlpha($this->_fileName);
                $isTrueColor = true;
                return $transparentIndex; // -1
            }
        }
        if (IMAGETYPE_JPEG === $fileType) {
            $isTrueColor = true;
        }
        return false;
    }

    /**
     * Change the image size
     *
     * @param int $frameWidth
     * @param int $frameHeight
     */
    public function resize($frameWidth = null, $frameHeight = null) {
        if (empty($frameWidth) && empty($frameHeight)) {
            throw new Exception('Invalid image dimensions.');
        }

        // calculate lacking dimension
        if (!$this->_keepFrame) {
            if (null === $frameWidth) {
                $frameWidth = round($frameHeight * ($this->_imageSrcWidth / $this->_imageSrcHeight));
            } elseif (null === $frameHeight) {
                $frameHeight = round($frameWidth * ($this->_imageSrcHeight / $this->_imageSrcWidth));
            }
        } else {
            if (null === $frameWidth) {
                $frameWidth = $frameHeight;
            } elseif (null === $frameHeight) {
                $frameHeight = $frameWidth;
            }
        }

        // define coordinates of image inside new frame
        $srcX = 0;
        $srcY = 0;
        $dstX = 0;
        $dstY = 0;
        $dstWidth = $frameWidth;
        $dstHeight = $frameHeight;
        if ($this->_keepAspectRatio) {
            // do not make picture bigger, than it is, if required
            if ($this->_constrainOnly) {
                if (($frameWidth >= $this->_imageSrcWidth) && ($frameHeight >= $this->_imageSrcHeight)) {
                    $dstWidth = $this->_imageSrcWidth;
                    $dstHeight = $this->_imageSrcHeight;
                }
            }
            // keep aspect ratio
            if ($this->_imageSrcWidth / $this->_imageSrcHeight >= $frameWidth / $frameHeight) {
                $dstHeight = round(($dstWidth / $this->_imageSrcWidth) * $this->_imageSrcHeight);
            } else {
                $dstWidth = round(($dstHeight / $this->_imageSrcHeight) * $this->_imageSrcWidth);
            }
        }
        // define position in center (TODO: add positions option)
        $dstY = round(($frameHeight - $dstHeight) / 2);
        $dstX = round(($frameWidth - $dstWidth) / 2);

        // get rid of frame (fallback to zero position coordinates)
        if (!$this->_keepFrame) {
            $frameWidth = $dstWidth;
            $frameHeight = $dstHeight;
            $dstY = 0;
            $dstX = 0;
        }

        // create new image
        $isAlpha = false;
        $isTrueColor = false;
        $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha, $isTrueColor);
        if ($isTrueColor) {
            $newImage = imagecreatetruecolor($frameWidth, $frameHeight);
        } else {
            $newImage = imagecreate($frameWidth, $frameHeight);
        }

        // fill new image with required color
        $this->_fillBackgroundColor($newImage);

        // resample source image and copy it into new frame
        imagecopyresampled(
                $newImage, $this->_imageHandler, $dstX, $dstY, $srcX, $srcY, $dstWidth, $dstHeight, $this->_imageSrcWidth, $this->_imageSrcHeight
        );
        $this->_imageHandler = $newImage;
        $this->refreshImageDimensions();
        $this->_resized = true;
    }

    /**
     * Change the image size down to a best match then crop from the center
     *
     * @param int $frameWidth
     * @param int $frameHeight
     */
    public function adaptiveResize($frameWidth = null, $frameHeight = null) {
        if(is_null($this->_mode))
        {
            $mode = self::RESIZE_STRETCH;
        }
        else
        {
            $mode = $this->_mode;
        }

        if (empty($frameWidth) && empty($frameHeight)) {
            throw new Exception('Invalid image dimensions.');
        }

        if(is_null($frameHeight))
        {
            $frameHeight = round($frameWidth * ($this->_imageSrcHeight / $this->_imageSrcWidth));
        }

        /*
        $widthDistance = abs($this->_imageSrcWidth - $frameWidth);
        $heightDistance = abs($this->_imageSrcHeight - $frameHeight);

        if (($frameWidth / $frameHeight) > ($this->_imageSrcWidth / $this->_imageSrcHeight)) {
            $this->resize($frameWidth, null);
        } else {
            $this->resize(null, $frameHeight);
        }

        $cropX = 0;
        $cropY = 0;
        if ($this->_imageSrcWidth > $frameWidth) {
            $cropX = intval(($this->_imageSrcWidth - $frameWidth) / 2);
        } elseif ($this->_imageSrcHeight > $frameHeight) {
            $cropY = intval(($this->_imageSrcHeight - $frameHeight) / 2);
        }

        $isAlpha = false;
        $isTrueColor = false;
        $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha, $isTrueColor);
        if ($isTrueColor) {
            $newImage = imagecreatetruecolor($frameWidth, $frameHeight);
        } else {
            $newImage = imagecreate($frameWidth, $frameHeight);
        }
        */

        $outputMarginX = 0;
        $outputMarginY = 0;
        $inputMarginX = 0;
        $inputMarginY = 0;

        $paddingLeft = 0;
        $paddingTop = 0;

        $newImage=imagecreatetruecolor($frameWidth, $frameHeight);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        
        switch($mode){
            case self::RESIZE_STRETCH:{
                imagecopyresampled($newImage, $this->_imageHandler, $outputMarginX, $outputMarginY, $inputMarginX, $inputMarginY, $frameWidth, $frameHeight, $this->_imageSrcWidth, $this->_imageSrcHeight);
                $this->_imageSrcWidth = $frameWidth;
                $this->_imageSrcHeight = $frameHeight;
                
                break;
            }
            case self::RESIZE_INNER:{
            	$newW=$frameWidth;
                $newH=$frameHeight;

                
                $coefInput = $this->_imageSrcWidth/$this->_imageSrcHeight;//$width_orig/$height_orig;

                if($frameWidth/$frameHeight > $coefInput){
                    $newH = $frameWidth/$coefInput;
                } else {
                    $newW = $frameHeight*$coefInput;
                }
                
                if($newW <= $paddingLeft*2) $paddingLeft=0;
                else $paddingLeft = $paddingLeft;

                if($newH<=$paddingTop*2) $paddingTop=0;
                else $paddingTop=$paddingTop;

                if($frameWidth/$coefInput>$newW)
                    $coefX=-1;
                else $coefX=-1;

                if($frameHeight/$coefInput>$newH)
                    $coefY=-1;
                else $coefY=-1;

                $marginLeft=$coefX*($newW-$frameWidth)/2+$paddingLeft;
                $marginTop=$coefY*($newH-$frameHeight)/2+$paddingTop;

                if($marginLeft+$paddingLeft>$newW || $marginTop+$paddingTop>$newH){
                    $paddingLeft=0;
                    $paddingTop=0;
                }

                $color = $this->_arrayColorAllocate($newImage, $this->_backgroundColor);
                
                imagefilledrectangle($newImage, 0, 0, $frameWidth, $frameHeight, $color);
                imagecopyresampled($newImage, $this->_imageHandler, $marginLeft, $marginTop, 0, 0, $newW-$paddingLeft*2, $newH-$paddingTop*2, $this->_imageSrcWidth, $this->_imageSrcHeight);
                
//                $this->_imageSrcWidth = $newW-$paddingLeft*2;
//                $this->_imageSrcHeight = $newH-$paddingTop*2;
                
                
                if($frameWidth/$frameHeight > $coefInput){
                    $this->_imageSrcHeight = $frameHeight;
                    $this->_imageSrcWidth = $newW-$paddingLeft*2;
                } else {
                    $this->_imageSrcHeight = $newH-$paddingLeft*2;
                    $this->_imageSrcWidth = $frameWidth;
                }
                
                
                break;
            }
            case self::RESIZE_OUTER:{
                $newW=$frameWidth;
                $newH=$frameHeight;

                $coefInput = $this->_imageSrcWidth/$this->_imageSrcHeight;//$width_orig/$height_orig;

                if($frameWidth/$frameHeight > $coefInput){
                    $newW = $frameHeight*$coefInput;
                } else {
                    $newH = $frameWidth/$coefInput;
                }

                if($newW<=$paddingLeft*2) $paddingLeft=0;
                else $paddingLeft=$paddingLeft;

                if($newH<=$paddingTop*2) $paddingTop=0;
                else $paddingTop=$paddingTop;

                $marginLeft=abs($newW-$frameWidth)/2+$paddingLeft;
                $marginTop=abs($newH-$frameHeight)/2+$paddingTop;

                if($marginLeft+$paddingLeft>$newW || $marginTop+$paddingTop>$newH){
                    $paddingLeft=0;
                    $paddingTop=0;
                }
                $color = $this->_arrayColorAllocate($newImage, $this->_backgroundColor);

                imagefilledrectangle($newImage, 0, 0, $frameWidth, $frameHeight, $color);
                imagecopyresampled($newImage, $this->_imageHandler, $marginLeft, $marginTop, 0, 0, $newW-$paddingLeft*2, $newH-$paddingTop*2, $this->_imageSrcWidth, $this->_imageSrcHeight);
                
                $this->_imageSrcWidth = $newW-$paddingLeft*2;
                $this->_imageSrcHeight = $newH-$paddingTop*2;
                
                break;
            }
        }

        //$this->_fillBackgroundColor($newImage);
        //imagecopyresampled($newImage, $this->_imageHandler, 0, 0, $cropX, $cropY, $frameWidth, $frameHeight, $frameWidth, $frameHeight);
        $this->_imageHandler = $newImage;
        //$this->refreshImageDimensions();
    }

    public function rotate($angle) {
        /*
          $isAlpha = false;
          $backgroundColor = $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha);
          list($r, $g, $b) = $this->_backgroundColor;
          if ($isAlpha) {
          $backgroundColor = imagecolorallocatealpha($this->_imageHandler, 0, 0, 0, 127);
          }
          elseif (false === $backgroundColor) {
          $backgroundColor = imagecolorallocate($this->_imageHandler, $r, $g, $b);
          }
          $this->_imageHandler = imagerotate($this->_imageHandler, $angle, $backgroundColor);
          // */
        $this->_imageHandler = imagerotate($this->_imageHandler, $angle, $this->imageBackgroundColor);
        $this->refreshImageDimensions();
    }

    public function watermark($watermarkImage, $positionX = 0, $positionY = 0, $watermarkImageOpacity = 30, $repeat = false) {
        list($watermarkSrcWidth, $watermarkSrcHeight, $watermarkFileType, ) = getimagesize($watermarkImage);
        $this->_getFileAttributes();
        $watermark = call_user_func($this->_getCallback(
                        'create', $watermarkFileType, 'Unsupported watermark image format.'
                ), $watermarkImage);

        $merged = false;

        if ($this->getWatermarkWidth() &&
                $this->getWatermarkHeigth() &&
                ($this->getWatermarkPosition() != self::POSITION_STRETCH)
        ) {
            $newWatermark = imagecreatetruecolor($this->getWatermarkWidth(), $this->getWatermarkHeigth());
            imagealphablending($newWatermark, false);
            $col = imagecolorallocate($newWatermark, 255, 255, 255);
            imagecolortransparent($newWatermark, $col);
            imagefilledrectangle($newWatermark, 0, 0, $this->getWatermarkWidth(), $this->getWatermarkHeigth(), $col);
            imagealphablending($newWatermark, true);
            imageSaveAlpha($newWatermark, true);
            imagecopyresampled(
                    $newWatermark, $watermark, 0, 0, 0, 0, $this->getWatermarkWidth(), $this->getWatermarkHeigth(), imagesx($watermark), imagesy($watermark)
            );
            $watermark = $newWatermark;
        }

        if ($this->getWatermarkPosition() == self::POSITION_TILE) {
            $repeat = true;
        } elseif ($this->getWatermarkPosition() == self::POSITION_STRETCH) {

            $newWatermark = imagecreatetruecolor($this->_imageSrcWidth, $this->_imageSrcHeight);
            imagealphablending($newWatermark, false);
            $col = imagecolorallocate($newWatermark, 255, 255, 255);
            imagecolortransparent($newWatermark, $col);
            imagefilledrectangle($newWatermark, 0, 0, $this->_imageSrcWidth, $this->_imageSrcHeight, $col);
            imagealphablending($newWatermark, true);
            imageSaveAlpha($newWatermark, true);
            imagecopyresampled(
                    $newWatermark, $watermark, 0, 0, 0, 0, $this->_imageSrcWidth, $this->_imageSrcHeight, imagesx($watermark), imagesy($watermark)
            );
            $watermark = $newWatermark;
        } elseif ($this->getWatermarkPosition() == self::POSITION_CENTER) {
            $positionX = ($this->_imageSrcWidth / 2 - imagesx($watermark) / 2);
            $positionY = ($this->_imageSrcHeight / 2 - imagesy($watermark) / 2);
            imagecopymerge(
                    $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx($watermark), imagesy($watermark), $this->getWatermarkImageOpacity()
            );
        } elseif ($this->getWatermarkPosition() == self::POSITION_TOP_RIGHT) {
            $positionX = ($this->_imageSrcWidth - imagesx($watermark));
            imagecopymerge(
                    $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx($watermark), imagesy($watermark), $this->getWatermarkImageOpacity()
            );
        } elseif ($this->getWatermarkPosition() == self::POSITION_TOP_LEFT) {
            imagecopymerge(
                    $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx($watermark), imagesy($watermark), $this->getWatermarkImageOpacity()
            );
        } elseif ($this->getWatermarkPosition() == self::POSITION_BOTTOM_RIGHT) {
            $positionX = ($this->_imageSrcWidth - imagesx($watermark));
            $positionY = ($this->_imageSrcHeight - imagesy($watermark));
            imagecopymerge(
                    $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx($watermark), imagesy($watermark), $this->getWatermarkImageOpacity()
            );
        } elseif ($this->getWatermarkPosition() == self::POSITION_BOTTOM_LEFT) {
            $positionY = ($this->_imageSrcHeight - imagesy($watermark));
            imagecopymerge(
                    $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx($watermark), imagesy($watermark), $this->getWatermarkImageOpacity()
            );
        }

        if ($repeat === false && $merged === false) {
            imagecopymerge(
                    $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx($watermark), imagesy($watermark), $this->getWatermarkImageOpacity()
            );
        } else {
            $offsetX = $positionX;
            $offsetY = $positionY;
            while ($offsetY <= ($this->_imageSrcHeight + imagesy($watermark))) {
                while ($offsetX <= ($this->_imageSrcWidth + imagesx($watermark))) {
                    imagecopymerge(
                            $this->_imageHandler, $watermark, $offsetX, $offsetY, 0, 0, imagesx($watermark), imagesy($watermark), $this->getWatermarkImageOpacity()
                    );
                    $offsetX += imagesx($watermark);
                }
                $offsetX = $positionX;
                $offsetY += imagesy($watermark);
            }
        }

        imagedestroy($watermark);
        $this->refreshImageDimensions();
    }

    public function crop($top = 0, $left = 0, $right = 0, $bottom = 0) {
        if ($left == 0 && $top == 0 && $right == 0 && $bottom == 0) {
            return;
        }

        $newWidth = $this->_imageSrcWidth - $left - $right;
        $newHeight = $this->_imageSrcHeight - $top - $bottom;

        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        if ($this->_fileType == IMAGETYPE_PNG) {
            $this->_saveAlpha($canvas);
        }

        imagecopyresampled(
                $canvas, $this->_imageHandler, 0, 0, $left, $top, $newWidth, $newHeight, $newWidth, $newHeight
        );

        $this->_imageHandler = $canvas;
        $this->refreshImageDimensions();
    }

    public function checkDependencies() {
        foreach ($this->_requiredExtensions as $value) {
            if (!extension_loaded($value)) {
                throw new Exception("Required PHP extension '{$value}' was not loaded.");
            }
        }
    }

    private function refreshImageDimensions() {
        $this->_imageSrcWidth = imagesx($this->_imageHandler);
        $this->_imageSrcHeight = imagesy($this->_imageHandler);
    }

    function __destruct() {
        @imagedestroy($this->_imageHandler);
    }

    /*
     * Fixes saving PNG alpha channel
     */

    private function _saveAlpha($imageHandler) {
        $background = imagecolorallocate($imageHandler, 0, 0, 0);
        ImageColorTransparent($imageHandler, $background);
        imagealphablending($imageHandler, false);
        imagesavealpha($imageHandler, true);
    }

    /**
     * Private method to allocate a color to an image using hex color code
     */
    protected function _hexColorAllocate($hex, $img, $alpha=0){
        if($img){
            sscanf($hex, "%2x%2x%2x", $r, $g, $b);
            return imagecolorallocatealpha($img, $r, $g, $b, $alpha);
        }
        else return null;
    }

    /**
     * Allocates color from RGB color array.
     * @param resource $image
     * @param array $colorArray
     */
    protected function _arrayColorAllocate($image, $colorArray)
    {
        list($r, $g, $b) = $colorArray;

        return imagecolorallocatealpha($image, $r, $g, $b, 0);
    }

    public function setMode($value)
    {
        $this->_mode = $value;
    }

    /**
     * Gives real mime-type with not considering file type field
     *
     * @return string
     */
    public function getMimeTypeWithOutFileType()
    {
        return $this->_fileMimeType;
    }
}
