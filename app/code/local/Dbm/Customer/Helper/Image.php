<?php

class Dbm_Customer_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getOptionsForProfile()
    {
        return array(
            'bgColor' => array(255, 255, 255),
            'keepFrame' => false,
            'defaultImage' => false,
            'keepAspectRatio' => true,
            'constrainOnly' => false,
            'useAdaptive' => true,
            'mode' => Varien_Image_Adapter_Gd2::RESIZE_INNER,
            'defaultImage' => false,
            'allowPlaceholder' => false,
            'quality' => 100
        );
    }
    
    public function getSizes()
    {
        return array(
            'profile_header' => array(73, 73),
            'subscriber_list_odd' => array(72, 73),
            'subscriber_list_even' => array(72, 73),
            'element_list' => array(46, 46),
            'profile_grid' => array(36, 36),
            'profile_edit' => array(50, 50),
            'mobile_thumb' => array(200, 200)
        );
    }
    
    public function getProfileImage(Mage_Customer_Model_Customer $customer, $size, $options = array())
    {
        $result = '';
        
        if($customer->getId())
        {
            $helper = Mage::helper('dbm_share');
            $imHelper = Mage::helper('dbm_utils/image');
            
            if($customer->getProfileImage())
            {
                $url = Dbm_Customer_Helper_Data::MEDIA_FOLDER.$customer->getProfileImage();
                $result = $imHelper->resizeMediaImage($url, $size[0], $size[1], $options);
            }
        }
        
        return $result;
    }
}