<?php

class Dbm_Share_Block_List_Latest extends Dbm_Share_Block_List_Abstract
{
    protected function _getCollection() {
        $collection = parent::_getCollection();

        if($this->getElementType())
        {
            $collection->addTypeFilter($this->getElementType());
        }

        $collection->addFollowedByFilter($this->getCustomer());

        return $collection;
    }
}