<?php

class Dbm_Share_Model_Mysql4_Comment_Collection extends Dbm_Share_Model_Mysql4_Collection_Abstract
{
    protected $_elementTable;
    
    public function _construct()
    {
        $this->_init('dbm_share/comment', 'dbm_share/comment');
        
        $this->_elementTable = $this->getTable('dbm_share/element');
    }
    
    public function toApiArray()
    {
        $result = array();
        
        foreach($this as $comment)
        {
            $customer = Mage::getModel('dbm_customer/customer')->load($comment->getIdCustomer());
            
            if($customer->getId() > 0)
            {
                $result[] = array(
                    'id' => $comment->getId(),
                    'message' => $comment->getMessage(),
                    'created_at' => $comment->getCreatedAt(),
                    'customer' => $customer->toApiArray($customer)
                );
            }
        }
        
        return $result;
    }
    
    public function addCustomerFilter(Mage_Customer_Model_Customer $customer, $group = false)
    {
        if($customer->getId())
        {
            $select = $this->getSelect();
            $select->join(array('element' => $this->_elementTable), 
                'main_table.id_element = element.id',
                ''
            )
                ->where('element.id_customer = ?', $customer->getId())
            ;
            
            $select->columns(new Zend_Db_Expr('COUNT(main_table.id) as element_count'));
            $select->columns(new Zend_Db_Expr('MIN(main_table.created_at) as first_update_date'));
            
            if($group)
            {
                $select->group('main_table.id_element');
            }
        }
        
        return $this;
    }
}