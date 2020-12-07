<?php

class Dbm_Share_Model_Mysql4_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function setApiDefaults()
    {
        $this->setPageSize(Mage::helper('dbm_share')->getApiListPageSize());
        return $this;
    }
}