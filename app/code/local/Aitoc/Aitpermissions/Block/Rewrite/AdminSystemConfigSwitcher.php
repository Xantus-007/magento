<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminSystemConfigSwitcher.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ piakYrejhjpqhhZq('9455a65f2d34c152aed8a6ddd90d92f6'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminSystemConfigSwitcher
    extends Mage_Adminhtml_Block_System_Config_Switcher
{
    public function getStoreSelectOptions()
    {
        $options = parent::getStoreSelectOptions();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            unset($options['default']);
        }

        if ($role->isScopeStore())
        {
            $currentStore = Mage::getModel('core/store')->load($this->getRequest()->getParam('store'), 'code')->getId();
            $allowedStoreviewIds = $role->getAllowedStoreviewIds();

            if (!in_array($currentStore, $allowedStoreviewIds))
            {
                $storeViewId = $allowedStoreviewIds[0];

                // redirecting to first allowed store
                $url = Mage::getModel('adminhtml/url');
                $storeView = Mage::getModel('core/store')->load($storeViewId);
                $website = Mage::getModel('core/website')->load($storeView->getWebsiteId());

                Mage::app()->getResponse()->setRedirect($url->getUrl('*/*/*', array('store' => $storeView->getCode(), 'website' => $website->getCode())));
            }
        }

        if ($role->isScopeWebsite())
        {
            $currentWebsite = Mage::getModel('core/website')->load($this->getRequest()->getParam('website'), 'code')->getId();
            $allowedWebsites = $role->getAllowedWebsiteIds();

            if (!in_array($currentWebsite, $allowedWebsites))
            {
                $websiteId = $allowedWebsites[0];

                // redirecting to first allowed website
                $url = Mage::getModel('adminhtml/url');
                $website = Mage::getModel('core/website')->load($websiteId);

                Mage::app()->getResponse()->setRedirect($url->getUrl('*/*/*', array('website' => $website->getCode())));
            }
        }

        return $options;
    }
    
    protected function _afterToHtml($html)
    {
        if (Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled())
        {
        	$allowedStoreviewIds = Mage::getSingleton('aitpermissions/role')->getAllowedStoreviewIds();
            if (count($allowedStoreviewIds) <= 1)
            {
                return '';
            }
        }
        
        return $html;
    }
} } 