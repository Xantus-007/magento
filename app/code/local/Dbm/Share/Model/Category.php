<?php

class Dbm_Share_Model_Category extends Dbm_Share_Model_Timelogged_Abstract
{
    const MEDIA_FOLDER = 'category';
    const POPULAR_ID = 3;
    public function _construct()
    {
        parent::_construct();
        $this->_setResourceModel('dbm_share/category', 'dbm_share/category_collection');
    }

    public function getListForType($type)
    {
        if(Mage::helper('dbm_share')->isTypeAllowed($type))
        {
            $collection = $this->getCollection();
            $collection->addTypeFilter($type);
        }

        return $collection;
    }
}