<?php

class Dbm_Share_Helper_Order extends Mage_Core_Helper_Abstract
{
    public function getOriginFromId($id)
    {
        return Mage::getModel('dbm_share/order_attribute_origin')->getOriginLabel($id);
    }
    
    public function getOrigin(Mage_Sales_Model_Order $order)
    {
        return Mage::getModel('dbm_share/order_attribute_origin')->getOriginLabel($order->getOrigin());
    }
    
    public function getOriginsForAdmin()
    {
        $labels = Mage::getModel('dbm_share/order_attribute_origin')->getLabels();
        return $labels;
    }
    
    public function getTotalQty(Mage_Sales_Model_Order $order)
    {
        return $order->getData('total_qty_ordered');
    }
}