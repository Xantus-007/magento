<?php

abstract class Dbm_Map_Block_Map_Abstract extends Mage_Core_Block_Template
{
    abstract protected function _getCollection(Dbm_Map_Model_Bounds $coordBox);
    abstract protected function _getPointData($item);
    
    public function getPointsAsJson(Dbm_Map_Model_Bounds $coordBox)
    {
        foreach($this->_getCollection() as $item)
        {
            $result[] = $this->getPointData($item);
        }
        
        return Zend_Json::encode($result);
    }
}