<?php

class Dbm_Share_Model_Adminhtml_Renderer_OrderCountByCustomer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $customer = new Varien_Object();
        $customer->setId($row->getData('entity_id'));
        
        return (Mage::helper('dbm_share')->getCountOrderPositionByCustomerAndOrder($customer) ?: '0');
    }
}
