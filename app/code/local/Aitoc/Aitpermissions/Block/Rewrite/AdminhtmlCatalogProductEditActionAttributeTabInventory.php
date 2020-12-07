<?php
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

class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogProductEditActionAttributeTabInventory
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Inventory
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