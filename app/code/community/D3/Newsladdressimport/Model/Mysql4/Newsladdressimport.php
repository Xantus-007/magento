<?php

class D3_Newsladdressimport_Model_Mysql4_Newsladdressimport extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the newsladdressimport_id refers to the key field in your database table.
        $this->_init('newsladdressimport/newsladdressimport', 'newsladdressimport_id');
    }
}