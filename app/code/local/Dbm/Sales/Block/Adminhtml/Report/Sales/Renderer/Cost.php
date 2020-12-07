<?php

class Dbm_Sales_Block_Adminhtml_Report_Sales_Renderer_Cost extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
    public function render(Varien_Object $row)
    {
        $cost = $row->getData('total_base_cost');
        return Mage::helper('core')->currency($cost);
    }
}