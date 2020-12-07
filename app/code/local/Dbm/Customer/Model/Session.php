<?php


class Dbm_Customer_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct() {
        $namespace = 'dbm_customer';
        
        $this->init($namespace);
        
        Mage::dispatchEvent('dbm_customer_session_init', array('dbm_customer_session' => $this));
    }
}