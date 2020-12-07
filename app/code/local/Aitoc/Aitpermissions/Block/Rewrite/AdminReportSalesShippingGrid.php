<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminReportSalesShippingGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ QqjZSDWgfgCNoIei('e40bb20dee8b33c71006fe053eda4157'); ?><?php

class Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesShippingGrid extends Mage_Adminhtml_Block_Report_Sales_Shipping_Grid
{
    /*
    * @return Varien_Object
    */
    public function getFilterData()
    {
        $filter = parent::getFilterData();
        $filter->setStoreIds(
            implode(',', Mage::helper('aitpermissions/access')
                    ->getFilteredStoreIds(
                    $filter->getStoreIds() ? explode(',', $filter->getStoreIds()) : array()
                )
            )
        );
        return $filter;
    }
} } 