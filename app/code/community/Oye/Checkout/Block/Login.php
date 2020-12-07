<?php

class Oye_Checkout_Block_Login extends Mage_Customer_Block_Form_Login
{
    public function __construct()
    {
        parent::__construct();
        if(!$this->isCustomerLoggedIn()){
            Mage::getSingleton('customer/session')->getAfterAuthUrl(Mage::helper('core/url')->getCurrentUrl());
        }
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('customer/account/loginPost');
    }

    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getForgotPasswordActionUrl()
    {
        return $this->getUrl('customer/account/forgotPasswordPost');
    }

    protected function _prepareLayout()
    {
        return Mage_Core_Block_Template::_prepareLayout();
    }
}