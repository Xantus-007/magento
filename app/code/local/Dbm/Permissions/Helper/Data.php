<?php

/**
 * Description of Permissions
 *
 * @author dlote
 */
class Dbm_Permissions_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /*
     * Check if current user is limit to a website
     */
    public function checkCurrentUserLimits(){
        $user = Mage::getSingleton('admin/session')->getUser();
        $role = $user->getRole()->getData();
//        Mage::log($role);
        
        $allStores = array();
        foreach (Mage::app()->getStores() as $_eachStoreId => $val) 
        {
            $allStores[] = Mage::app()->getStore($_eachStoreId)->getId();
        }
        
        Mage::log($allStores);
        $storeId = Mage::helper('aitpermissions/access')->getFilteredStoreIds($allStores); 
        Mage::log($storeId);
    }
}
