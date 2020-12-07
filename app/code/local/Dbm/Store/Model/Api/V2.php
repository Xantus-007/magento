<?php

class Dbm_Store_Model_Api_V2 extends Mage_Api_Model_Resource_Abstract
{
    public function getStores()
    {
        return $this->_getStore(Dbm_Store_Model_Type::TYPE_STORE);
    }

    public function getSpots()
    {
        return $this->_getStore(Dbm_Store_Model_Type::TYPE_SPOT);
    }

    public function _getStore($type)
    {
        $collection = Mage::getModel('ustorelocator/location')->getCollection();
        $collection->addFieldToFilter('type', $type);

        foreach($collection as $item)
        {
            $model = Mage::getModel('dbm_store/location')->load($item->getId());
            $result[] = $model->toApiArray();
        }

        return $result;
    }
}
