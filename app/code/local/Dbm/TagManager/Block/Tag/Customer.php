<?php

class Dbm_TagManager_Block_Tag_Customer extends Dbm_TagManager_Block_Tag
{
    protected function _construct()
    {
        $sessionCustomer = Mage::getSingleton('customer/session');
        
        if($sessionCustomer->getGtmTagCustomer() || $this->hasCreateCustomer())
        {
            $this->ecommerceData = array("event" => "account");
            $this->setLayerData(" ");
            $sessionCustomer->setGtmTagCustomer(false);
        }
    }
}