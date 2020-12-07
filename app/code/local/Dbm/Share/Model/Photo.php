<?php

class Dbm_Share_Model_Photo extends Dbm_Share_Model_Timelogged_Abstract
{
    const MEDIA_FOLDER = 'element';

    public function _construct()
    {
        parent::_construct();
        $this->_setResourceModel('dbm_share/photo', 'dbm_share/photo_collection');
    }
/*
    public function loadByMd5($md5, Dbm_Share_Model_Element $element = null)
    {
        $collection = $this->getCollection()->addFieldToFilter('md5', $md5);

        if($element && $element->getId() > 0)
        {
            $collection->addFieldToFilter('id_element', $element->getId());
        }

        return $collection;
    }
*/
    public function toApiArray()
    {
        $helper = Mage::helper('dbm_share');
        
        $sizes = Mage::helper('dbm_share/image')->getSizes();
        $options = Mage::helper('dbm_share/image')->getOptionsForList();
        $imageUrl = $helper->getPhotoUrl($this->getFilename());
        
        $element = Mage::getModel('dbm_share/element')->load($this->getIdElement());
        
        $thumb_100 = str_replace('index.php/', '', Mage::helper('dbm_share/image')->getElementImageUrl($element, $sizes['mobile_thumb'], $options));
        
        if($this->getId() > 0)
        {
            return array(
                'id' => $this->getId(),
                'url' => $imageUrl,
                'lat' => $this->getLat(),
                'lng' => $this->getLng(),
                'position' => $this->getGmapsLabel(),
                'thumb_100' => $thumb_100
            );
        }
    }
}