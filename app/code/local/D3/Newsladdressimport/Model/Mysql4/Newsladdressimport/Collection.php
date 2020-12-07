<?php

class D3_Newsladdressimport_Model_Mysql4_Newsladdressimport_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('newsladdressimport/newsladdressimport');
    }
}