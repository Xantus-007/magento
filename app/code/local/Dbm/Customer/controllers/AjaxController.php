<?php

class Dbm_Customer_AjaxController extends Dbm_Customer_Controller_Auth
{
    protected function _getPublicActions()
    {
        return array();
    }
    
    public function subscribeAction()
    {
        $result = array(
            'isValid' => true,
            'message' => '',
            'action' => 0
        );
        $trans = Mage::helper('dbm_share');
        $params = $this->getRequest()->getParams();
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        $followed = Mage::getModel('dbm_customer/customer')->load($params['id']);
        
        if($this->getRequest()->isXmlHttpRequest() && $customer->getId() && $followed->getId())
        {
            $link = Mage::getModel('dbm_customer/link')->getCollection()
                ->addFieldToFilter('id_customer', $customer->getId())
                ->addFieldToFilter('id_following', $followed->getId());
            $resource = Mage::getModel('dbm_customer/link')->getResource();
            $isFollowing = count($link) == 1;
            $result['action'] = intval(!$isFollowing);
            
            $result['label'] = $result['action'] == 1 ? $trans->__('Subscribed ') : $trans->__('Unsubscribed ');
            
            $isFollowing ? $resource->unfollow($customer, $followed) : $resource->follow($customer, $followed);
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}