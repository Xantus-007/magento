<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Main
 *
 * @author dlote
 */
class Dbm_Permissions_Block_Adminhtml_Promo_Quote_Edit_Tab_Main extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main{
    
    protected function _prepareForm()
    {
        parent::_prepareForm();
        
        $allStores = array();
        foreach (Mage::app()->getStores() as $_eachStoreId => $val) 
        {
            $allStores[] = Mage::app()->getStore($_eachStoreId)->getId();
        }
        $storeIds = Mage::helper('aitpermissions/access')->getFilteredStoreIds($allStores); 
        
        if(count($storeIds) != count($allStores)){
            $websiteIds = array();
            foreach ($storeIds as $storeId) {
                if(!in_array($storeId, $websiteIds)){
                    $websiteIds[] = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
                }
            }
            $form = $this->getForm();
            $website_ID = $form->getElement('website_ids');
            $website_ID->setReadonly(true, true);
            
            $website_ID->setValue($websiteIds);
        }
        return $this;
    }
}

?>
