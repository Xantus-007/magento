<?php

class Dbm_Share_Model_Comment extends Dbm_Share_Model_Timelogged_Abstract
{
    const STATUS_BANNED = -1;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    public function _construct()
    {
        parent::_construct();
        $this->_setResourceModel('dbm_share/comment', 'dbm_share/comment_collection');
    }

    public function getCustomer()
    {
        if($this->getId())
        {
            $model = Mage::getModel('dbm_customer/customer')->load($this->getIdCustomer());
        }

        return $model;
    }
    
    public function saveCommentForCurrentCustomer($idElement, $message)
    {
        $result = null;
        $trans = Mage::helper('dbm_share');
        $element = Mage::getModel('dbm_share/element')->load($idElement);
        $cleanComment = Mage::helper('dbm_share')->cleanCommentString($message);
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        
        if(!Mage::helper('dbm_customer')->isValidProfile($customer))
        {
            Mage::throwException($trans->__('Please complete your profile to comment'));
        }
        
        if($element->getId() > 0 && strlen($cleanComment) > 0 && $customer->getId() > 0)
        {
            $data = array(
                'id_customer' => $customer->getId(),
                'id_element' => $element->getId(),
                'status' => Dbm_Share_Model_Comment::STATUS_ACTIVE,
                'message' => $cleanComment
            );
            
            $model = Mage::getModel('dbm_share/comment')->setData($data)->save();
            $this->load($model->getId());
            $result = $this;
        }
        
        return $result;
    }
    
    public function abuse()
    {
        return $this->getResource()->abuse(Dbm_Share_Helper_Abuse::TYPE_COMMENT, $this);//Mage::helper('dbm_share/abuse')->abuseComment($this);
    }
}
