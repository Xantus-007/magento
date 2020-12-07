<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */


class Aitoc_Aitpermissions_Block_Rewrite_AdminStoreSwitcher extends Mage_Adminhtml_Block_Store_Switcher
{
    public function getIsProductGrid()
    {
        return false;
        return $this->getRequest()->getControllerName() == 'catalog_product';
    }
}


/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminStoreSwitcher.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ ToBjSeEmQmqRwCgh('4b9b09a1be19787838e945551724138f'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Dbm_Country_Block_Rewrite_AdminStoreSwitcher extends Aitoc_Aitpermissions_Block_Rewrite_AdminStoreSwitcher
{
	public function __construct()
	{
		parent::__construct();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isScopeStore())
        {
            $this->hasDefaultOption(false);
        }

        if ($role->isScopeWebsite())
        {
            $this->setWebsiteIds($role->getAllowedWebsiteIds());
        }
	}
	
	public function getStores($group)
	{
        $stores = parent::getStores($group);

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            foreach ($stores as $storeId => $store) 
            {
                if (!in_array($storeId, $role->getAllowedStoreviewIds()))
                {
                    unset($stores[$storeId]);
                }
            }
        }
        
        return $stores;
	}
	
	public function getStoreCollection($group)
	{
		$stores = parent::getStoreCollection($group);
        
		$role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $stores->addIdFilter($role->getAllowedStoreviewIds());
        }

        return $stores;
	}
    
    public function getWebsiteCollection()
    {
        $websiteCollection = parent::getWebsiteCollection();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $websiteCollection->addIdFilter($role->getAllowedWebsiteIds());
        }

        return $websiteCollection;
    }
    
    protected function _toHtmlReports()
    {
        $role = Mage::getSingleton('aitpermissions/role');

        if (!$role->isPermissionsEnabled())
        {
            return parent::_toHtml();
        }

        $html = parent::_toHtml();
        $bAllow = false;

        $currentWebsiteId  = Mage::app()->getRequest()->getParam('website');
        $currentStoreId    = Mage::app()->getRequest()->getParam('group');

        if (Mage::app()->getRequest()->getParam('store_ids'))
        {
            $currentStoreviewIds = explode(',', Mage::app()->getRequest()->getParam('store_ids'));
        } 
        else 
        {
            $currentStoreviewIds = array();
            if (Mage::app()->getRequest()->getParam('store'))
            {
                $currentStoreviewIds = array(Mage::app()->getRequest()->getParam('store'));
            }
        }

        $allowedWebsiteIds = $role->getAllowedWebsiteIds();
        $allowedStoreIds = $role->getAllowedStoreIds();
        $AllowedStoreviewIds = $role->getAllowedStoreviewIds();

        if ($allowedWebsiteIds && $allowedStoreIds && $AllowedStoreviewIds)
        {
            if (in_array($currentWebsiteId, $allowedWebsiteIds) ||
                in_array($currentStoreId, $allowedStoreIds) ||
                array_intersect($currentStoreviewIds, $AllowedStoreviewIds))
            {
                $bAllow = true;
            }
        }

        if (!$bAllow) 
        {
            $url = Mage::getModel('adminhtml/url')->getUrl('*/*/*', array('_current' => false, 'store' => $AllowedStoreviewIds[0]));
            Mage::app()->getResponse()->setRedirect($url);
        }

        // removing <option value="">All Store Views</option> option if have limited access
        $html = preg_replace('@<option value="">(.*)</option>@', '', $html);

        // if no stores selected, need to select allowed
        if (!$currentWebsiteId && !$currentStoreId && !$currentStoreviewIds)
        {
            // enhanced switcher is used on categories page
            if (preg_match('@varienStoreSwitcher@', $html))
            {
                $html .= '
                <script type="text/javascript">
                try
                {
                    Event.observe(window, "load", varienStoreSwitcher.optionOnChange);
                } catch (err) {}
                </script>
                ';
            } 
            else
            {
                $html .= '
                <script type="text/javascript">
                permissionsSwitchStore = function()
                {
                    switchStore($("store_switcher"));
                }

                try
                {
                    Event.observe(window, "load", permissionsSwitchStore);
                } catch (err) {}
                </script>
                ';
            }
        }

        return $html;
    }
    
	protected function _toHtml()
    {
        if (strpos(Mage::app()->getRequest()->getControllerName(), 'report') !== false)
        {
            // ... and 1.3 (other) versions
            return $this->_toHtmlReports();
        }

        // the next code will work for all store selectors except reports
        $sHtml = parent::_toHtml();

        $role = Mage::getSingleton('aitpermissions/role');
        
        if ($role->isPermissionsEnabled())
        {
        	$AllowedStoreviews = $role->getAllowedStoreviewIds();
            if (!empty($AllowedStoreviews)) 
            {
            	if (!in_array(Mage::app()->getRequest()->getParam('store'), $AllowedStoreviews))
                {
                    $url = Mage::getModel('adminhtml/url');
                    Mage::app()->getResponse()->setRedirect($url->getUrl('*/*/*', array('_current'=>true, 'store'=>$AllowedStoreviews[0])));
                }
            }
            
            // removing <option value="">All Store Views</option> option if have limited access
            $sHtml = preg_replace('@<option value="">(.*)</option>@', '', $sHtml);
        }
        
        // enhanced switcher is used on categories page
        if (preg_match('@varienStoreSwitcher@', $sHtml))
        {
            $sHtml .= '
            <script type="text/javascript">
            try
            {
                Event.observe(window, "load", varienStoreSwitcher.optionOnChange);
            } catch (err) {}
            </script>
            ';
        }
        
        return $sHtml;
    }
} }

