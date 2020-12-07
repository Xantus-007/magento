<?php

class Dbm_Share_Block_List_Liked extends Dbm_Share_Block_List_Abstract
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $customer = $this->getCustomer();

        $collection->addLikedByFilter($customer);
        $collection->orderByLikedDate($customer);
        return $collection;
    }
}