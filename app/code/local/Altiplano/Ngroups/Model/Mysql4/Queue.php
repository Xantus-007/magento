<?php

class Altiplano_Ngroups_Model_Mysql4_Queue extends Mage_Newsletter_Model_Mysql4_Queue
{
    
    protected function _construct() 
    {
        $this->_init('newsletter/queue', 'queue_id');
    }
       
    public function addSubscribersToQueue(Mage_Newsletter_Model_Queue $queue, array $subscriberIds) 
    {


        $queueData = $queue->getTemplate()->getData();

        if (isset($queueData['user_group']) && $queueData['user_group'] != "" && $queueData['user_group'] > 0){

            $groupInfo = Mage::getModel('ngroups/ngroups')->getCollection()->addFieldToFilter('ngroups_id', $queueData['user_group'])->toArray();

        }

        if (isset($groupInfo['items'][0]['customers']) && $groupInfo['items'][0]['customers'] != ""){

            $subscriberIds = array();

            $subscribers = preg_split('[,]', $groupInfo['items'][0]['customers']);
            foreach ($subscribers as $subscriber){

                if ($subscriber != ""){

                    $subscriberIds[] = $subscriber;

                }

            }

            $subscriberIds = array_unique($subscriberIds);

        }
	

//        if (count($subscriberIds)==0) {
//            Mage::throwException(Mage::helper('newsletter')->__('No subscribers selected'));
//        }
//var_dump($subscriberIds);
//exit;
        
        if (!$queue->getId() && $queue->getQueueStatus()!=Mage_Newsletter_Model_Queue::STATUS_NEVER) {
            Mage::throwException(Mage::helper('newsletter')->__('Invalid queue selected'));
        }
        
        $select = $this->_getWriteAdapter()->select();
        $select->from($this->getTable('queue_link'),'subscriber_id')
            ->where('queue_id = ?', $queue->getId())
            ->where('subscriber_id in (?)', $subscriberIds);
        
        $usedIds = $this->_getWriteAdapter()->fetchCol($select);
        $this->_getWriteAdapter()->beginTransaction();
            foreach($subscriberIds as $subscriberId) {
		if (trim($subscriberId) == '0' || trim($subscriberId) == '')
			continue;
                if(in_array($subscriberId, $usedIds)) {
                    continue;
                }
                
                $data = array();
                $data['queue_id'] = $queue->getId();
                $data['subscriber_id'] = $subscriberId;
                $data['group_id'] = $queueData['user_group'];

                try {
                    $this->_getWriteAdapter()->insert($this->getTable('queue_link'), $data);
                }
                catch (Exception $e) {
//                    $this->_getWriteAdapter()->rollBack();
                }

                
            }
            $this->_getWriteAdapter()->commit();

//            var_dump($subscriberIds);
//            exit;
        
    }
    
    public function removeSubscribersFromQueue(Mage_Newsletter_Model_Queue $queue)
    {
        try {
            $this->_getWriteAdapter()->delete(
                $this->getTable('queue_link'), 
                array(
                    $this->_getWriteAdapter()->quoteInto('queue_id = ?', $queue->getId()),
                    'letter_sent_at IS NULL'
                )
            );
            
            $this->_getWriteAdapter()->commit();
        } 
        catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
        }
        
    }
    
    public function setStores(Mage_Newsletter_Model_Queue $queue) 
    {
        $this->_getWriteAdapter()
            ->delete(
                $this->getTable('queue_store_link'), 
                $this->_getWriteAdapter()->quoteInto('queue_id = ?', $queue->getId())
            );
        
        if (!is_array($queue->getStores())) { 
            $stores = array(); 
        } else {
            $stores = $queue->getStores();
        }
        
        foreach ($stores as $storeId) {
            $data = array();
            $data['store_id'] = $storeId;
            $data['queue_id'] = $queue->getId();
            $this->_getWriteAdapter()->insert($this->getTable('queue_store_link'), $data);
        }
         
        $this->removeSubscribersFromQueue($queue);

        if(count($stores)==0) {
            return $this;
        }
        $subscribers = Mage::getResourceSingleton('newsletter/subscriber_collection')
            ->addFieldToFilter('store_id', array('in'=>$stores))
            ->useOnlySubscribed()
            ->load();
         
        $subscriberIds = array();
        
        foreach ($subscribers as $subscriber) {
            $subscriberIds[] = $subscriber->getId();
        }
        
        if (count($subscriberIds) > 0) {
            $this->addSubscribersToQueue($queue, $subscriberIds);
        }
        
        return $this;
    }
    
    public function getStores(Mage_Newsletter_Model_Queue $queue) 
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('queue_store_link'), 'store_id')
            ->where('queue_id = ?', $queue->getId());
        
        if(!($result = $this->_getReadAdapter()->fetchCol($select))) {
            $result = array();
        }
        
        return $result;
    }
    
    /**
     * Saving template after saving queue action
     *
     * @param Mage_Core_Model_Abstract $queue
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $queue) 
    {
        if($queue->getSaveTemplateFlag()) {
            $queue->getTemplate()->save();
        }
        
        if($queue->getSaveStoresFlag()) {
            $this->setStores($queue);           
        }
        
        return $this;
    }
    
}
