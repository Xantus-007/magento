<?php

class Dbm_Sales_Block_Adminhtml_Order_Grid extends Dbm_Expedition_Block_Adminhtml_Order_Grid
{

    protected function _prepareColumns()
    {

        $this->addColumnAfter('cost', array(
            'header'    =>  Mage::helper('sales')->__('Cost'),
            'width'     =>  '100',
            'index'     =>  'cost',
            'type'      =>  'text',
            'renderer'  =>  new Dbm_Sales_Block_Adminhtml_Order_Renderer_Cost()
        ), 'grand_total');
 
        $this->addColumnsOrder('cost', 'grand_total');

        return parent::_prepareColumns();
    }
}