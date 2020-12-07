<?php

class Dbm_Share_Block_List_Myelements extends Dbm_Share_Block_List_Abstract
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();

        $customer = $this->getCustomer();
        $collection->addCustomerFilter($customer);
        
        return $collection;
    }
}