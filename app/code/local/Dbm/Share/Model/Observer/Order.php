<?php

class Dbm_Share_Model_Observer_Order
{
    public function saveHandler(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        if(!$order->hasOrigin())
        {
            $session = Mage::getModel('dbm_customer/session');
            $agent = strtolower(Mage::helper('core/http')->getHttpUserAgent());
            $origin = Dbm_Share_Model_Order_Attribute_Origin::ORIGIN_DESKTOP;
            
            if(strstr($agent, 'iphone'))
            {
                $origin = $session->getIsMobile() 
                    ? Dbm_Share_Model_Order_Attribute_Origin::ORIGIN_IOS_APP
                    : Dbm_Share_Model_Order_Attribute_Origin::ORIGIN_IOS;
            }
            elseif(strstr($agent, 'android'))
            {
                $origin = $session->getIsMobile()
                    ? Dbm_Share_Model_Order_Attribute_Origin::ORIGIN_ANDROID_APP
                    : Dbm_Share_Model_Order_Attribute_Origin::ORIGIN_ANDROID;
            }
            
            $order->setOrigin($origin);
        }
    }
}