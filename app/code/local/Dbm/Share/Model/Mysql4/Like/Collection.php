<?php

class Dbm_Share_Model_Mysql4_Like_Collection extends Dbm_Share_Model_Mysql4_Collection_Abstract
{
    protected $_elementsTable;
    protected $_likeTableName;

    public function _construct()
    {
        $this->_init('dbm_share/like', 'dbm_share/like');
        $this->_elementsTable = Mage::getSingleton('core/resource')->getTableName('dbm_share/element');
        $this->_likeTableName = Mage::getSingleton('core/resource')->getTableName('dbm_share/like');
        //$this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    public function addCustomerFilter(Mage_Customer_Model_Customer $customer)
    {
        $select = $this->getSelect();
        $select->joinLeft(array('liked_element' => $this->_elementsTable),
            'main_table.id_element = liked_element.id',
            ''
        )
            ->where('liked_element.id_customer = ?', $customer->getId())
            ->WHERE('main_table.id_customer != ?', $customer->getId())
        ;
    }
}