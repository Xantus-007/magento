<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCustomerGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ IwMBSgrkTkoUqqmp('586b9907c393cb5b1896cc1344f3aa60'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminCustomerGrid extends Mage_Adminhtml_Block_Customer_Grid
{
	protected function _prepareColumns()
	{
		parent::_prepareColumns();

        $role = Mage::getSingleton('aitpermissions/role');

		if ($role->isPermissionsEnabled())
		{
            if (!Mage::helper('aitpermissions')->isShowingAllCustomers() &&
                isset($this->_columns['website_id']))
            {
                unset($this->_columns['website_id']);
                $allowedWebsiteIds = $role->getAllowedWebsiteIds();

                if (count($allowedWebsiteIds) > 1)
                {
                    $websiteFilter = array();
                    foreach ($allowedWebsiteIds as $allowedWebsiteId)
                    {
                        $website = Mage::getModel('core/website')->load($allowedWebsiteId);
                        $websiteFilter[$allowedWebsiteId] = $website->getData('name');
                    }

                    $this->addColumn('website_id', array(
                        'header' => Mage::helper('customer')->__('Website'),
                        'align' => 'center',
                        'width' => '80px',
                        'type' => 'options',
                        'options' => $websiteFilter,
                        'index' => 'website_id',
                    ));
                }
            }
		}
        
        return $this;
	}
} } 