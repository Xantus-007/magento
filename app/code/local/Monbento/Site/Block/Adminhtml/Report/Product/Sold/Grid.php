<?php

class Monbento_Site_Block_Adminhtml_Report_Product_Sold_Grid extends Mage_Adminhtml_Block_Report_Product_Sold_Grid
{

    protected $_defaultFilters = array(
        'order_statuses',
        'report_from' => '',
        'report_to' => '',
        'report_period' => 'day'
    );

    /**
     * Prepare collection object for grid
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareCollection()
    {

        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('reports/product_sold_collection');

        $orderStatuses = $this->getFilter('order_statuses');
        if (is_array($orderStatuses)) {
            if (count($orderStatuses) == 1 && strpos($orderStatuses[0],',')!== false) {
                $this->setFilter('order_statuses', explode(',',$orderStatuses[0]));
            }
        }
        
        Mage::getSingleton('adminhtml/session')->setData('sold_order_statuses', $this->getFilter('order_statuses'));

        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumnAfter('sku', array(
            'header'    =>Mage::helper('reports')->__('Sku'),
            'index'     =>'sku'
        ), 'name');

        $this->addColumnAfter('orderstatus', array(
            'header'    =>Mage::helper('reports')->__('Status'),
            'index'     =>'orderstatus'
        ), 'sku');

        parent::_prepareColumns();
    }

    /**
     * Get order statuses
     *
     * @return array
     */
    public function getOrderStatuses()
    {
        $statuses = Mage::getModel('sales/order_config')->getStatuses();

        return array_merge(array('complete_closed' => 'Terminée + Fermée'), $statuses);
    }
}
