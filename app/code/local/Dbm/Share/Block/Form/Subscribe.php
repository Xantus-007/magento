<?php

class Dbm_Share_Block_Form_Subscribe extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        $idCustomer = Mage::app()->getRequest()->getParam('id', null);
        return Mage::getModel('dbm_customer/customer')->load($idCustomer);
    }

    public function getButtonClass()
    {
        return $this->_isFollowing() ? 'subscribed' : 'subscribe';
    }

    public function getLabel()
    {
        $trans = Mage::helper('dbm_share');
        return $this->_isFollowing() ? $trans->__('Following') : $trans->__('Follow');
    }

    protected function _isFollowing()
    {
        $customer = $this->getCustomer();
        $me = Mage::helper('dbm_customer')->getCurrentCustomer();

        $link = Mage::getModel('dbm_customer/link')->getCollection()
            ->addFieldToFilter('id_customer', $me->getId())
            ->addFieldToFilter('id_following', $customer->getId());

        return count($link) == 1;
    }
}