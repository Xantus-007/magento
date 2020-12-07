<?php

class Dbm_Expedition_Block_Adminhtml_Order_Renderer_Sender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
    public function render(Varien_Object $row)
    {
		
		$incrementId = $row->getData('increment_id');
    	$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId); 

        $value = $order->getSenderAdminId();
        
        $admin = Mage::getModel('admin/user')->load($value);
        if($admin->getId())
        	return ucfirst($admin->getFirstname()) . ' ' . strtoupper($admin->getLastname());

        return '';
    }
}