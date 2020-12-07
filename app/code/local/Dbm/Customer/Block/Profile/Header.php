<?php

class Dbm_Customer_Block_Profile_Header extends Dbm_Customer_Block_Profile_Abstract
{
    public function getCustomer()
    {
        $customer = Mage::helper('customer')->getCustomer();
        
        if($customer && $customer->getId())
        {
            $customer = Mage::getModel('dbm_customer/customer')->load($customer->getId());
        }
        
        return $customer;
    }
}