<?php

abstract class Dbm_Share_Model_Mysql4_Abuse_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    public function abuse($type, $model)
    {
        $wAdapter = Mage::getSingleton('core/resource')->getConnection('core_write');
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        $now = Mage::app()->getLocale()->date();
        $result = false;
        if(Mage::helper('dbm_share/abuse')->isTypeAllowed($type) && $model->getId() && $customer->getId())
        {
            $tableName = null;
            
            switch($type)
            {
                case Dbm_Share_Helper_Abuse::TYPE_COMMENT:
                    $tableName = $this->getTable('dbm_share/abuse_comment');
                    break;
                case Dbm_Share_Helper_Abuse::TYPE_ELEMENT:
                    $tableName = $this->getTable('dbm_share/abuse_element');
                    break;
            }
            
            if($tableName)
            {
                try {
                    //$sql = 'INSERT INTO '.$tableName.'(id_customer, id_'.$type.') VALUES()'
                    $wAdapter->insert($tableName, array(
                        'id_'.strtolower($type) => $model->getId(),
                        'id_customer' => $customer->getId(),
                        'created_at' => $now->toString('yyyy-MM-dd HH:mm:ss')
                    ));
                    
                    $result = true;
                } catch (Exception $err)
                {
                    $result = false;
                }
            }
        }
        
        return $result;
    }
}