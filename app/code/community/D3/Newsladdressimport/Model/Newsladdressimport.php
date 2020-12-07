<?php

class D3_Newsladdressimport_Model_Newsladdressimport extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('newsladdressimport/newsladdressimport');
    }
}