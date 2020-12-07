<?php

class Dbm_Sales_Block_Adminhtml_Report_Sales_Sales_Grid extends Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesSalesGrid
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