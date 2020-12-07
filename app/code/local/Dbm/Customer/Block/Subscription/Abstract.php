<?php

class Dbm_Customer_Block_Subscription_Abstract extends Dbm_Share_Block_List_Abstract
{
    public function isFollowing(Mage_Customer_Model_Customer $customer)
    {
        $me = $this->getCustomer();
        
        $link = Mage::getModel('dbm_customer/link')->getCollection()
            ->addFieldToFilter('id_customer', $me->getId())
            ->addFieldToFilter('id_following', $customer->getId());
        
        return count($link) == 1;
    }

    protected function _addQueryFilter($collection)
    {
        $q = Mage::app()->getRequest()->getParam('q', null);
        
        if($q)
        {
            $pageSize = $collection->getPageSize();
            $collection = Mage::getModel('customer/customer')->getCollection()
                ->setPageSize($pageSize)
                ->addAttributeToSelect('*');
            Mage::getModel('dbm_customer/customer')->searchByNickname($q, $collection);
        }
        
        $curPage = Mage::app()->getRequest()->getParam('p');
        if($curPage)
        {
            $collection->setCurPage($curPage);
        }
        
        return $collection;
    }
}