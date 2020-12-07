<?php

class Dbm_Share_Block_Detail_Abstract extends Mage_Core_Block_Template
{
    public function getImageUrl(Dbm_Share_Model_Element $element, $size, $options = array())
    {
        return Mage::helper('dbm_share/image')->getElementImageUrl($element, $size, $options);
    }

    public function getElement()
    {
        return Mage::registry('dbm_share_current_element');
    }
}