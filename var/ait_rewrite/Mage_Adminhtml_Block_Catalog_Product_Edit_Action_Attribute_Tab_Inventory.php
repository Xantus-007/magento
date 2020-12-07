<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */

if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ ZUeUPwUDhewkjUMZ('d1b199fdae532cef20364389ac16ba64');
/**
 * Multi-Location Inventory
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitquantitymanager
 * @version      2.1.9
 * @license:     EBR5kWF9n2SX6a9ZiEug4hNJ2bkUly0f6aLFfKrYjH
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogProductEditActionAttributeTabInventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Inventory
{
    
// start aitoc
    public function isDefaultWebsite()
    {
        $iWebsiteId = 0;
        
        if ($store = $this->getRequest()->getParam('store')) 
        {
            $iWebsiteId = Mage::app()->getStore($store)->getWebsiteId();
        }
        
        if (!$iWebsiteId) 
        {
            return true;
        }
        else 
        {
            return false;
        }
    }
// finish aitoc

} }


/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminhtmlCatalogProductEditActionAttributeTabInventory.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ QqjZSDWgfgCNoIei('21db19e564f9d1c26292521a65a14aa3'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductEditActionAttributeTabInventory extends Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogProductEditActionAttributeTabInventory
{
    protected function _toHtml()
    {
        $role = Mage::getSingleton('aitpermissions/role');

        if (!$role->isPermissionsEnabled() || $role->canEditGlobalAttributes())
        {
            return parent::_toHtml();
        }

        return parent::_toHtml() . '
            <script type="text/javascript">
            //<![CDATA[
            if (Prototype.Browser.IE)
            {
                if (window.addEventListener)
                {
                    window.addEventListener("load", disableInventoryInputs, false);
                }
                else
                {
                    window.attachEvent("onload", disableInventoryInputs);
                }
            }
            else
            {
                document.observe("dom:loaded", disableInventoryInputs);
            }

            function disableInventoryInputs()
            {
                var elements = $("table_cataloginventory").select(\'input[type="checkbox"],input[type="text"],select\');
                if (elements.size)
                {
                    elements.each(function(el) {
                       el.disabled = true;
                    });
                }
            }
            //]]>
            </script>';
    }
} }

