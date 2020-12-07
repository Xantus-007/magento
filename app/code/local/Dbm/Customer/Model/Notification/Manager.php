<?php

class Dbm_Customer_Model_Notification_Manager extends Varien_Object
{
    protected $_notifications = array();
    
    public function addNotification(Dbm_Customer_Model_Notification $notification)
    {
        $this->_notifications[$notification->getDate()->toString(Zend_Date::TIMESTAMP).(count($this->_notifications)+1)] = $notification;
    }
    
    public function toApiArray()
    {
        $result = array();
        ksort($this->_notifications);
        
        foreach($this->_notifications as $notification)
        {
            $tmpData = array(
                'date' => $notification->getDate()->toString('yyyy-MM-dd HH:mm:ss'),
                'label' => $notification->getLabel(),
                'url' => $notification->getUrl()
            );
            
            switch(get_class($notification->getSource()))
            {
                case 'Dbm_Share_Model_Element':
                    
                    $collection = Mage::getModel('dbm_share/element')->getCollection()
                        ->addAll()
                        ->addFieldToFilter('id', $notification->getSource()->getId());
                    
                    $source = $collection->getFirstItem();
                    
                    $tmpData['link_element'] = $source->toApiArray();
                    break;
                case 'Dbm_Customer_Model_Customer':
                case 'Mage_Customer_Model_Customer':
                    $tmpCustomer = Mage::getModel('dbm_customer/customer')->load($notification->getSource()->getId());
                    $tmpData['link_customer'] = $tmpCustomer->toApiArray($tmpCustomer);
                    break;
            }
            
            $result[] = $tmpData;
        }
        
        return $result;
    }
}