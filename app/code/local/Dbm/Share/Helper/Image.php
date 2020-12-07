<?php

class Dbm_Share_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getOptionsForList()
    {
        return array(
            'bgColor' => array(255, 255, 255),
            'keepFrame' => false,
            'defaultImage' => false,
            'keepAspectRatio' => true,
            'constrainOnly' => true,
            'useAdaptive' => true,
            'mode' => Varien_Image_Adapter_Gd2::RESIZE_INNER,
            'defaultImage' => false,
            'allowPlaceholder' => false,
            'quality' => 100
        );
    }
    
    public function getOptionsForListPhotos()
    {
        return array(
            'bgColor' => array(255, 255, 255),
            'keepFrame' => false,
            'defaultImage' => false,
            'keepAspectRatio' => true,
            'constrainOnly' => true,
            'useAdaptive' => true,
            'mode' => Varien_Image_Adapter_Gd2::RESIZE_INNER,
            'defaultImage' => false,
            'allowPlaceholder' => true,
            'quality' => 100
        );
    }
    
    public function getOptionsForDetailPhotos()
    {
        return array(
            'bgColor' => array(255, 255, 255),
            'keepFrame' => false,
            'defaultImage' => false,
            'keepAspectRatio' => true,
            'constrainOnly' => true,
            'useAdaptive' => true,
            'mode' => Varien_Image_Adapter_Gd2::RESIZE_OUTER,
            'defaultImage' => false,
            'allowPlaceholder' => true,
            'quality' => 100
        );
    }

    public function getOptionsForDetail()
    {
        return array(
            'bgColor' => array(255, 255, 255),
            'keepFrame' => false,
            'defaultImage' => false,
            'keepAspectRatio' => true,
            'constrainOnly' => true,
            'useAdaptive' => true,
            'mode' => Varien_Image_Adapter_Gd2::RESIZE_OUTER,
            'defaultImage' => false,
            'allowPlaceholder' => false,
            'quality' => 100
        );
    }
    
    public function getOptionsForCategory()
    {
        return array(
            'bgColor' => array(255, 255, 255),
            'keepFrame' => true,
            'defaultImage' => false,
            'keepAspectRatio' => true,
            'constrainOnly' => false,
            'useAdaptive' => false,
            'mode' => Varien_Image_Adapter_Gd2::RESIZE_OUTER,
            'allowPlaceholder' => false,
            'quality' => 100
        );
    }
    
    public function getSizes()
    {
        return array(
            'list' => array(466, 350),
            'grid' => array(738, 450),
            'detail_receipe' => array(770, null),
            'detail_photo' =>array(770, null),
            'map_thumb' => array(50, 50),
            'mobile_thumb' => array(200, 200),
            'cat_menu' => array(20, 20)
        );
    }

    public function getElementImageUrl(Dbm_Share_Model_Element $element, $size, $options = array())
    {
        $helper = Mage::helper('dbm_share');
        $imHelper = Mage::helper('dbm_utils/image');

        $photo = $element->getPhotos()->getFirstItem();
        if($photo)
        {
            $url = Dbm_Share_Helper_Data::MAIN_MEDIA_FOLDER.'/'.Dbm_Share_Model_Photo::MEDIA_FOLDER.'/'. $helper->getPhotoDir($photo->getFilename(), false, '/').$photo->getFilename();
        }

        $resized = $imHelper->resizeMediaImage($url, $size[0], $size[1], $options);

        return $resized;
    }
}