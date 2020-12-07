<?php

class Dbm_Customer_Block_Profile_Abstract extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        return Mage::helper('dbm_customer')->getCurrentCustomer();
    }
}