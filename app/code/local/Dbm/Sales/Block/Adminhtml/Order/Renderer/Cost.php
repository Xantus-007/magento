<?php

class Dbm_Sales_Block_Adminhtml_Order_Renderer_Cost extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
    public function render(Varien_Object $row)
    {
		
        $cost = $productCost = 0;

		$incrementId = $row->getData('increment_id');
    	$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId); 

        foreach ($order->getAllItems() as $item) {
            $productCost = (float) $item->getProduct()->getCost() * 1;
            $cost += $productCost * ($item->getQtyOrdered() - $item->getQtyCanceled());
        }
        
        return Mage::helper('core')->currency($cost, true, false);
    }
}