<?php

class Monbento_Kiosk_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_kioskStoreCode = 'fr';
    protected $_kioskLoginRoute = 'monbento-kiosk/index/login';
    protected $_kioskLoginPostRoute = 'monbento-kiosk/index/loginPost';

    public function getKioskStoreCode()
    {
        return $this->_kioskStoreCode;
    }

    public function getKioskLoginRoute()
    {
        return $this->_kioskLoginRoute;
    }

    public function getKioskLoginPostRoute()
    {
        return $this->_kioskLoginPostRoute;
    }

    public function getUsersMagasinList()
    {
        $magasinUsers = false;

        $magasinRole = Mage::getModel('admin/role')->getCollection()
            ->addFieldToFilter('role_name', array('eq' => 'Magasin'))
            ->getFirstItem();

        if($magasinRoleId = $magasinRole->getRoleId())
        {
            $magasins = Mage::getModel('admin/role')->getCollection()
                ->addFieldToFilter('parent_id', array('eq' => $magasinRoleId));

            $users = array();
            foreach($magasins as $magasin)
            {
                $users[] = $magasin->getUserId();
            }

            if(count($users))
            {
                $magasinUsers = Mage::getModel('admin/user')->getCollection()
                    ->addFieldToFilter('user_id', array('in' => $users));
            }
        }

        return $magasinUsers;
    }

    public function getCustomerGroupIdByCode($customerGroupCode)
    {
        return Mage::getModel('customer/group')->load($customerGroupCode, 'customer_group_code')->getCustomerGroupId();
    }
}