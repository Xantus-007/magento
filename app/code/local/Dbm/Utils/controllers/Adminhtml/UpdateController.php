<?php

class Dbm_Utils_Adminhtml_UpdateController extends Mage_Adminhtml_Controller_Action
{
    public function _construct()
    {
        parent::_construct();
        
        $this->_publicActions[] = 'updateOrderStatus';
    }
    
    public function updateOrderStatusAction()
    {
        $orderId = $this->getRequest()->getParam('id'); //order id
        $order = Mage::getModel('sales/order')->load($orderId);
        
        $canClose = true;
        foreach ($order->getAllItems() as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if($product->getTypeID() == 'ugiftcert' && $order->getState() == 'processing') {
                $options = $item->getProductOptions();
                //if($options['info_buyRequest']['delivery_type'] != 'physical') $canClose = false;
            } else {
                $canClose = false;
            }
        }
        
        if($canClose) {
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write->query("UPDATE `sales_flat_order` SET state='complete', status='complete' WHERE entity_id = ".$orderId."");
            $write->query("UPDATE `sales_flat_order_grid` SET status='complete' WHERE entity_id = ".$orderId."");
        }
        
        $this->_redirect('adminhtml/sales_order');
        return $this;
    }
}