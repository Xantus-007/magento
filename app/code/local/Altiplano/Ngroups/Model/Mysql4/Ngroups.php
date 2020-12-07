<?php

class Altiplano_Ngroups_Model_Mysql4_Ngroups extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the ngroups_id refers to the key field in your database table.
        $this->_init('ngroups/ngroups', 'ngroups_id');
    }
    public function setSubscribers($groupId, $customers = array())
    {

        $select = $this->_getWriteAdapter()->select();
        $select->from($this->getTable('newsletter/queue_link'),'queue_id')
            ->where('group_id = ?', $groupId);
            
        $queueIds = $this->_getWriteAdapter()->fetchCol($select);
        $queueIds = array_unique($queueIds);

        $groupInfo = Mage::getModel('ngroups/ngroups')->getCollection()->addFieldToFilter('ngroups_id', $groupId)->toArray();

        foreach ($queueIds as $queueId){

            if (isset($groupInfo['items'][0]['customers']) && $groupInfo['items'][0]['customers'] != ""){

                $subscriberIds = array();

                $subscribers = preg_split('[,]', $customers.",".$groupInfo['items'][0]['customers']);
                foreach ($subscribers as $subscriber){

                    if ($subscriber != ""){

                        $subscriberIds[] = $subscriber;

                    }

                }

                $subscriberIds = array_unique($subscriberIds);

            }
            if (isset($subscriberIds)){}
            else{
                $subscriberIds = preg_split('[,]',$customers);
                $subscriberIds = array_unique($subscriberIds);
            }
//            var_dump($subscriberIds);
//            exit;
            $select = $this->_getWriteAdapter()->select();
            $select->from($this->getTable('newsletter/queue_link'),'subscriber_id')
                ->where('queue_id = ?', $queueId)
                ->where('subscriber_id in (?)', $subscriberIds);

            $usedIds = $this->_getWriteAdapter()->fetchCol($select);
            $this->_getWriteAdapter()->beginTransaction();

            try {
                foreach($subscriberIds as $subscriberId) {
                    if(in_array($subscriberId, $usedIds)) {
                        continue;
                    }
                    $data = array();
                    $data['queue_id'] = $queueId;
                    $data['subscriber_id'] = $subscriberId;
                    $data['group_id'] = $groupId;

                    $this->_getWriteAdapter()->insert($this->getTable('newsletter/queue_link'), $data);
                }
                $this->_getWriteAdapter()->commit();
            }
            catch (Exception $e) {
                $this->_getWriteAdapter()->rollBack();
            }
            
            
        }

    }
    public function unsetSubscribers($groupId, $subscriberIds = array())
    {


        $where = '';
        $where = $this->_getReadAdapter()->quoteInto('subscriber_id in (?)', $subscriberIds);
        $where .= " and group_id=".$groupId;

        $this->_getWriteAdapter()->delete($this->getTable('newsletter/queue_link'),$where);

        return true;

    }
}