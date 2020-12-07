<?php

class Cartsguru_CartRecovery_Block_Adminhtml_Register extends Mage_Adminhtml_Block_Template
{
    // Get website URL
    public function getWebsiteURL()
    {
        return Mage::getBaseUrl();
    }

    // Get current user firstname
    public function getFirstName()
    {
        return Mage::getSingleton('admin/session')->getUser()->getFirstname();

    }

    // Get current user lastname
    public function getLastName()
    {
        return Mage::getSingleton('admin/session')->getUser()->getLastname();
    }

    // Get current user lastname
    public function getEmail()
    {
        return Mage::getSingleton('admin/session')->getUser()->getEmail();
    }
}
