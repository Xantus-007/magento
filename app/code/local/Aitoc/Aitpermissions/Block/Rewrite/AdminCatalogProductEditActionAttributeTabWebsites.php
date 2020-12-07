<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCatalogProductEditActionAttributeTabWebsites.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ chmgYMarUriIppyo('f9f2143370fc885d9acda9f0597bf495'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductEditActionAttributeTabWebsites
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Websites
{
    public function getWebsiteCollection()
    {
        $websites = parent::getWebsiteCollection();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
        	foreach ($websites as $key => $website)
            {
            	if (!in_array($website->getId(), $role->getAllowedWebsiteIds()))
            	{
            		unset($websites[$key]);
            	}
            }
        }
        
        return $websites;
    }
} } 