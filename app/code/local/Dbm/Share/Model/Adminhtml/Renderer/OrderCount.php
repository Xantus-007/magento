<?php

class Dbm_Share_Model_Adminhtml_Renderer_OrderCount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $order = Mage::getModel('sales/order')->load($row->getId());
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        
        return Mage::helper('dbm_share')->getCountOrderPositionByCustomerAndOrder($customer, $order);
    }
}
