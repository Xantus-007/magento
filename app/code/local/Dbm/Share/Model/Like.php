<?php

class Dbm_Share_Model_Like extends Dbm_Share_Model_Timelogged_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_setResourceModel('dbm_share/like', 'dbm_share/like_collection');
    }
}