<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Helper/Data.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ QqjZSDWgfgCNoIei('b45186f038b4424df9278c7e41c8e1e5'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isShowingAllProducts()
    {
        return Mage::getStoreConfig('admin/general/showallproducts');
    }

    public function isShowingAllCustomers()
    {
        return Mage::getStoreConfig('admin/general/showallcustomers');
    }

    public function isShowProductOwner()
    {
        return Mage::getStoreConfig('admin/general/show_admin_on_product_grid');
    }

    public function isAllowedDeletePerWebsite()
    {
        return Mage::getStoreConfig('admin/general/allowdelete_perwebsite');
    }

    public function isAllowedDeletePerStoreview()
    {
        return Mage::getStoreConfig('admin/general/allowdelete');
    }

    public function isShowingProductsWithoutCategories()
    {
        return Mage::getStoreConfig('admin/general/allow_null_category');
    }

    /**
     * backward compatibility with Shopping Assistant
     */
    public function getAllowedCategories()
    {
        return Mage::getSingleton('aitpermissions/role')->getAllowedCategoryIds();
    }
} } 