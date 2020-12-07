<?php

class Dbm_Customer_Model_Mysql4_Link_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('dbm_customer/link', 'dbm_customer/link_collection');
    }
}