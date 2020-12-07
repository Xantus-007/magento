<?php

class Dbm_TagManager_Block_Tag_Contact extends Dbm_TagManager_Block_Tag
{
    protected function _construct()
    {
        $customerSession = Mage::getSingleton('customer/session');
        
        $messageText = "";
        
        $messages = $customerSession->getMessages();
        foreach($messages->getItems() as $message)
        {
            $messageText = $message->getText();
        }
        
        if($messageText == Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'))
        {
            $this->ecommerceData = array("event" => "contact");
            $this->setLayerData("");
        }
    }
}