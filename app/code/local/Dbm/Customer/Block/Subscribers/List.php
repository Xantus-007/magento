<?php

class Dbm_Customer_Block_Subscribers_List extends Dbm_Customer_Block_Subscription_Abstract
{
    protected function _getCollection()
    {
        $pageSize = Dbm_Share_Helper_Data::API_LIST_PAGE_SIZE;
        
        $customer = $this->getCustomer();
        
        $collection = Mage::getModel('dbm_customer/customer')->load($customer->getId())->getFollowers()
            ->setPageSize($pageSize)
            ->addAttributeToSelect('*')
        ;

        $collection = $this->_addQueryFilter($collection);

        return $collection;
    }
}