<?php

class Dbm_Share_Block_Comments extends Mage_Core_Block_Template
{
    public function getElement()
    {
        return Mage::registry('dbm_share_current_element');
    }

    public function getComments()
    {
        $comments = null;
        
        $currentElement = $this->getElement();
        if($currentElement->getId())
        {
            $comments = $currentElement->getComments();
            $comments->addOrder('created_at', 'DESC');
        }

        return $comments;
    }
}