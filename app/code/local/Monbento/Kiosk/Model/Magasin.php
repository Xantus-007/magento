<?php

class Monbento_Kiosk_Model_Magasin extends Mage_Core_Model_Session_Abstract 
{
    public function __construct()
    {
        $this->init('monbento_kiosk');
    }

    public function isLogin()
    {
        return $this->_getData('customer_group_code');
    }
}
