<?php

class Dbm_Utils_Adminhtml_TestController extends Mage_Adminhtml_Controller_Action
{
    public function _construct()
    {
        parent::_construct();
        
        //$this->_publicActions[] = 'updateOrderStatus';
    }
    
    public function updateOrderStatusAction()
    {
        $orderId = $this->getRequest()->getParam('id'); //order id
        $order = Mage::getModel('sales/order')->load($orderId); //load order             
        echo "Changing status to complete for order id ".$orderId;
        
        $order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true);
        $order->save();
        
        echo '<pre>END</pre>';
        exit();
    }
}