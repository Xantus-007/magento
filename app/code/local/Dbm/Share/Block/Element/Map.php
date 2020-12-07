<?php


class Dbm_Share_Block_Element_Map extends Dbm_Map_Block_Map_Abstract
{
    protected function _getCollection(Dbm_Map_Model_Bounds $coordBox)
    {
        $collection = Mage::getModel('dbm_share/element');
        
        if($this->getType() && Mage::helper('dbm_share')->isTypeAllowed($this->getType()))
        {
            $collection->addTypeFilter($this->getType());
        }
        
        $collection->addCoordBoxFilter($coordBox);
    }
    
    protected function _getPointData($item) 
    {
        
    }
}