<?php

class Dbm_Share_Model_Adminhtml_Renderer_Origin extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $order = Mage::getModel('sales/order')->load($row->getId());
        return Mage::getModel('dbm_share/order_attribute_origin')->getOriginLabel($order->getOrigin());
    }
}