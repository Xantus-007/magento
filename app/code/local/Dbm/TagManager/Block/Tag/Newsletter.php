<?php

class Dbm_TagManager_Block_Tag_Newsletter extends Dbm_TagManager_Block_Tag
{
    protected function _construct()
    {
        $sessionCustomer = Mage::getSingleton('customer/session');

        if($sessionCustomer->getGtmTagNewsletter())
        {
            $this->ecommerceData = array("event" => "newsletter");
            $this->setLayerData("");
            $sessionCustomer->setGtmTagNewsletter(false);
        }
    }
}