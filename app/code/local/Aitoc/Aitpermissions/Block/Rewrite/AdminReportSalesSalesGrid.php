<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminReportSalesSalesGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ piakYrejhjpqhhZq('3115a780d2eb8988d5189ee844f57918'); ?><?php

class Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesSalesGrid extends Mage_Adminhtml_Block_Report_Sales_Sales_Grid
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