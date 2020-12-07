<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Rewrite/AdminSystemStore.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ ToBjSeEmQmqRwCgh('bfc4ce2f0225719eac0d193a0cb264b5'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Rewrite_AdminSystemStore extends Mage_Adminhtml_Model_System_Store
{
    public function __construct()
    {
        parent::__construct();

        if (Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled())
        {
            $this->setIsAdminScopeAllowed(false);
        }
    }
    
    protected function _loadWebsiteCollection()
    {
        $this->_websiteCollection = Mage::app()->getWebsites();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            foreach ($this->_websiteCollection as $id => $website)
            {
                if (!in_array($id, $role->getAllowedWebsiteIds()))
                {
                    unset($this->_websiteCollection[$id]);
                }
            }
        }
        
        return $this;
    }
    
    protected function _loadStoreCollection()
    {
        $this->_storeCollection = Mage::app()->getStores();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            foreach ($this->_storeCollection as $id => $store)
            {
                if (!in_array($id, $role->getAllowedStoreviewIds()))
                {
                    unset($this->_storeCollection[$id]);
                }
            }
        }

        return $this;
    }
} } 