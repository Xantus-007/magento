<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */


class Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesSalesGrid extends Mage_Adminhtml_Block_Report_Sales_Sales_Grid
{

    protected function _prepareColumns()
    {
        
        parent::_prepareColumns();

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('total_base_cost', array(
            'header'    =>  Mage::helper('sales')->__('Cost'),
            'width'     =>  '100',
            'index'     =>  'total_base_cost',
            'type'      =>  'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
            //'renderer'  =>  new Dbm_Sales_Block_Adminhtml_Report_Sales_Renderer_Cost()
        ));
    }

}


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

class Dbm_Sales_Block_Adminhtml_Report_Sales_Sales_Grid extends Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesSalesGrid
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

