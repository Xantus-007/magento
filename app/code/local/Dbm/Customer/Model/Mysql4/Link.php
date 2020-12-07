<?php

class Dbm_Customer_Model_Mysql4_Link extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('dbm_customer/link', 'id_customer');
    }
    
    public function follow(Mage_Customer_Model_Customer $follower, Mage_Customer_Model_Customer $followed)
    {
        $result = false;
        $wAdapter = $this->_getWriteAdapter();
        $rAdapter = $this->_getReadAdapter();
        $now = Mage::app()->getLocale()->date()->toString('yyyy-MM-dd HH:mm:ss');
        
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable())
            ->where('id_customer=?', $follower->getId())
            ->where('id_following = ?', $followed->getId());
        
        $hasRow = $rAdapter->fetchOne($select);
        
        if(!$hasRow)
        {
            $result = $wAdapter->insert($this->getMainTable(), array(
                'id_customer' => $follower->getId(),
                'id_following' => $followed->getId(),
                'created_at' => $now
            ));
        }
        
        return $result;
    }
    
    public function unfollow(Mage_Customer_Model_Customer $follower, Mage_Customer_Model_Customer $followed)
    {
        $result = false;
        $wAdapter = $this->_getWriteAdapter();
        $rAdapter = $this->_getReadAdapter();
        
        if($follower->getId() && $followed->getId())
        {
            $select = $wAdapter->select()->from($this->getMainTable())
                ->where('id_customer = ?', $follower->getId())
                ->where('id_following = ?', $followed->getId())
            ;
            
            $wAdapter->query($select->deleteFromSelect($this->getMainTable()));
            $result = true;
        }
        
        return $result;
    }
}