<?php

class Dbm_TagManager_Model_Observer
{

    public function customerCreateHandler(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $sessionCustomer = Mage::getSingleton('customer/session');
        $sessionCustomer->setGtmTagCustomer(true);
        
        if($customer->getIsSubscribed()) 
        {
            $sessionCustomer->setGtmTagNewsletter(true);
        }
    }
    
    public function newsletterSubscribeHandler()
    {
        $sessionCustomer = Mage::getSingleton('customer/session');
        $sessionCustomer->setGtmTagNewsletter(true);
    }

}
