<?php

class Dbm_Customer_Model_Link extends Dbm_Share_Model_Timelogged_Abstract
{
    public function  _construct()
    {
        parent::_construct();
        $this->_setResourceModel('dbm_customer/link', 'dbm_customer/link_collection');
    }
}