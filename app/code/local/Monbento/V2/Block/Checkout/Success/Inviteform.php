<?php

class Monbento_V2_Block_Checkout_Success_Inviteform extends Mage_Core_Block_Template
{
    public function _beforeToHtml() {
        $this->_prepareLastOrder();
        return parent::_beforeToHtml();
    }
    
    protected function _prepareLastOrder()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
                $isVisible = !in_array($order->getState(),
                    Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates());
                $this->addData(array(
                    'is_order_visible' => $isVisible,
                    'view_order_id' => $this->getUrl('sales/order/view/', array('order_id' => $orderId)),
                    'print_url' => $this->getUrl('sales/order/print', array('order_id'=> $orderId)),
                    'can_print_order' => $isVisible,
                    'can_view_order'  => Mage::getSingleton('customer/session')->isLoggedIn() && $isVisible,
                    'order_id'  => $order->getIncrementId(),
                ));
            }
        }
    }
    
    public function getOrder()
    {
        return Mage::getModel('sales/order')->load($this->getOrderId());
    }
    
    /*
    public function getMessagesBlock()
    {
        $session = Mage::getSingleton('core/session');
        $session->addError('ERROR');
        
        $block = parent::getMessagesBlock()->getGroupedHtml();
    }
    */
    
    public function getInviteRegisterUrl()
    {
        return $this->getUrl('v2/invite/register');
    }
    
    public function isAnonOrder(Mage_Sales_Model_Order $order){        
        $isAnon = false;
        if($order->getCustomerName() == Mage::helper('sales')->__('Guest')){
            $isAnon = true;
        }
        
        return $isAnon;
    }
    
    protected function _toHtml() {
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        
        if(!$customer->getId())
        {
            return parent::_toHtml();
        }
        else
        {
            return '';
        }
    }
}