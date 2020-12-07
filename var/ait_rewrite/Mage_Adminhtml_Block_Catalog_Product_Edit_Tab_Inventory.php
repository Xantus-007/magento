<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */

if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ rRDROoRaiDomZRBr('660df998d0376dc7a9d94e9624c2bc31');
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


class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogProductEditTabInventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    // override parent        
    public function __construct()
    {
        parent::__construct();
#        $this->setTemplate('catalog/product/tab/inventory.phtml');
        $this->setTemplate('aitcommonfiles/design--adminhtml--default--default--template--catalog--product--tab--inventory.phtml'); // aitoc code
    }

    
// start aitoc code
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
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminhtmlCatalogProductEditTabInventory.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ QqjZSDWgfgCNoIei('2d63a09ace03fcbf32415ddaaa12f8e6'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductEditTabInventory extends Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogProductEditTabInventory
{
    protected function _toHtml()
    {
        $role = Mage::getSingleton('aitpermissions/role');

        if (!$role->isPermissionsEnabled() || $role->canEditGlobalAttributes())
        {
            return parent::_toHtml();
        }

        return parent::_toHtml() . '
            <input id="aitpermissions_inventory_manage_stock_default" name="product[stock_data][use_config_manage_stock]" type="hidden" value="1" />
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

                if(typeof($("inventory_use_config_manage_stock")) != "undefined");
                {
                    if($("inventory_use_config_manage_stock").checked)
                    {
                        $("aitpermissions_inventory_manage_stock_default").value = 1;
                    }
                    else
                    {
                        $("aitpermissions_inventory_manage_stock_default").value = 0;
                    }
                }
            }
            //]]>
            </script>';
    }
} }

