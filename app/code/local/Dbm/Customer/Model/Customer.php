<?php

class Dbm_Customer_Model_Customer extends Mage_Customer_Model_Customer
{
    public function follow(Mage_Customer_Model_Customer $customer)
    {
        return $this->_toggleFollow($customer, true);
    }
    
    public function unfollow(Mage_Customer_Model_Customer $customer)
    {
        return $this->_toggleFollow($customer, false);
    }
    
    /**
     * Returns all the followers of the current customer.
     * 
     * @return Mage_Core_Model_Mysql4_Collection
     */
    public function getFollowers()
    {
        $trans = Mage::helper('dbm_share');
        if($this->getId())
        {
            $linkTableName = $this->getResource()->getTable('dbm_customer/link');
            $collection = Mage::getModel('dbm_customer/customer')->getCollection()
                ->addAttributeToSelect(array('firstname', 'lastname'))
            ;
            $select = $collection->getSelect();
            
            $select
                //->reset(Zend_Db_Select::FROM)
                ->join(array('link' => $linkTableName), 
                    '`link`.id_customer = `e`.entity_id',
                    '*'
                )
                ->where('`link`.id_following = ?', $this->getId())
            ;
        }
        else
        {
            Mage::throwException($trans->__('Unable to get followers'));
        }
        
        return $collection;
    }
    
    /**
     * Return all the customers followed by the current customer
     * 
     * @return Mage_Core_Model_Mysql4_Collection
     */
    public function getFollowing()
    {
        $trans = Mage::helper('dbm_share');
        if($this->getId())
        {
            $linkTableName = $this->getResource()->getTable('dbm_customer/link');
            $collection = Mage::getModel('dbm_customer/customer')->getCollection()
                ->addAttributeToSelect('firstname', 'lastname')
            ;
            $select = $collection->getSelect();
            
            $select->join(array('link' => $linkTableName),
                '`link`.id_following = `e`.entity_id',
                '*')
            ->where('`link`.id_customer = ?', $this->getId());
            
        }
        else
        {
            Mage::throwException($trans->__('Unable to get followers'));
        }
        
        return $collection;
    }
    
    public function collectionToApiArray($collection)
    {
        $result = array();
        foreach($collection as $customer)
        {
            $tmpArray = $this->toApiArray($customer);
            
            $result[] = $tmpArray;
        }
        
        return $result;
    }
    
    public function toApiArray(Mage_Customer_Model_Customer $customer)
    {
        if(!($customer instanceof Dbm_Customer_Model_Customer))
        {
            $customer = Mage::getModel('dbm_customer/customer')->load($customer->getId());
        }
        
        $popupUrl = '';
        
        if(!Mage::helper('dbm_customer')->isValidProfile($customer))
        {
            $popupUrl = str_replace('index.php/', '', Mage::getUrl('dbm-customer/mobile/switch', array('_query' => array('mode' => 'incompleteProfile'))));
        }
        
        $sizes = Mage::helper('dbm_customer/image')->getSizes();
        $options = Mage::helper('dbm_customer/image')->getOptionsForProfile();
        $photoUrl = str_replace('index.php/', '', Mage::helper('dbm_customer/image')->getProfileImage($customer, $sizes['mobile_thumb'], $options));
        
        return array(
            'id' => $customer->getId(),
            //Url
            'popup' => $popupUrl,
            'nickname' => $customer->getProfileNickname(),
            'photo' => $photoUrl,
            'status' => $customer->getProfileStatus(),
            'url' => $customer->getProfileUrl(),
            'photo_count' => $customer->getElementCount(Dbm_Share_Model_Element::TYPE_PHOTO),
            'receipe_count' => $customer->getElementCount(Dbm_Share_Model_Element::TYPE_RECEIPE),
            'follower_count' => $customer->getFollowersCount(),
            'like_count' => $customer->getTotalLikes()
        );
    }

    public function getElementCount($type)
    {
        $result = 0;
        if(Mage::helper('dbm_share')->isTypeAllowed($type))
        {
            $collection = $this->_getElementCollection();
            $collection->addTypeFilter($type);
            
            $result = $collection->count();
        }

        return $result;
    }

    public function getTotalLikes()
    {
        $collection = $this->_getElementCollection()->addAll()->getLikeSum();
        return $collection->getFirstItem()->getData('sum_likes');
    }

    public function getFollowersCount()
    {
        return $this->getFollowers()->count();
    }

    public function searchByNickname($nickname, $collection = null)
    {
        if(!$collection)
        {
            $collection = Mage::getModel('dbm_customer/customer')->getCollection();
        }
        
        if(strlen($nickname))
        {
            $collection->addAttributeToSelect('*')
                ->addAttributeToFilter('profile_nickname', array('like' => '%'.$nickname.'%'));
        }

        return $collection;
    }

    public function updateNotifications(Mage_Customer_Model_Customer $customer)
    {
        if($customer->getId())
        {
            $now = Mage::app()->getLocale()->date();
            
            $customer->setNotificationDate($now->toString('yyyy-MM-dd HH:mm:ss'));
            $customer->save();
        }
    }
            
    
    protected function _toggleFollow(Mage_Customer_Model_Customer $customer, $follow)
    {
        $result = false;
        $trans = Mage::helper('dbm_share');
        
        if($this->getId() && $customer->getId() && $this->getId() != $customer->getId())
        {
            $resource = Mage::getModel('dbm_customer/link')->getResource();
            
            if($follow)
            {
                $resource->follow($this, $customer);
            }
            else
            {
                $resource->unfollow($this, $customer);
            }
            
            $result = true;
        }
        else
        {
            $this->_apiError($trans->__('You cannot follow this user'));
        }
        
        return $result;
    }

    protected function _getElementCollection()
    {
        return Mage::getModel('dbm_share/element')->getCollection()
            ->addCustomerFilter($this);
    }

    private function _apiError($message)
    {
        Mage::throwException($message);
    }
}