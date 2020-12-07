<?php

class Dbm_Share_Model_Mysql4_Like extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('dbm_share/like', 'id');
    }
}