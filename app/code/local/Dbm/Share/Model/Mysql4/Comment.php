<?php

class Dbm_Share_Model_Mysql4_Comment extends Dbm_Share_Model_Mysql4_Abuse_Abstract
{
    public function _construct()
    {
        $this->_init('dbm_share/comment', 'id');
    }
}