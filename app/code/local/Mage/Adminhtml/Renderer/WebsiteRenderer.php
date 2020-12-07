<?php

class Mage_Adminhtml_Renderer_WebsiteRenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $storeId = $row->getStoreId();
        
        $store = Mage::getModel('core/store')->load($storeId);
        $website = $store->getWebsite();
        
        return $website->getName();
    }
}