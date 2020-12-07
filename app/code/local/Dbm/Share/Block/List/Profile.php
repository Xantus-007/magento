<?php

class Dbm_Share_Block_List_Profile extends Dbm_Share_Block_List_Abstract
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $idCustomer = Mage::app()->getRequest()->getParam('id', null);
        $customer = Mage::getModel('dbm_customer/customer')->load($idCustomer);

        if($customer->getId())
        {
            $collection->addCustomerFilter($customer);
        }

        $collection->orderByLikes();

        return $collection;
    }
}