<?php

class Monbento_Site_Block_Rewrite_GiftMessage_Message_Inline extends Mage_GiftMessage_Block_Message_Inline
{

    protected function _construct()
    {
        parent::_construct();
        if(Mage::helper('monbento_site')->isCartPage()) $this->setTemplate('giftmessage/inline-cart.phtml');
    }
}
