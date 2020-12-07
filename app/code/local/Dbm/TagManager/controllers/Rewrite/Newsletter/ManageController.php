<?php

require_once(Mage::getModuleDir('controllers','Mage_Newsletter').DS.'ManageController.php');
class Dbm_TagManager_Rewrite_Newsletter_ManageController extends Mage_Newsletter_ManageController
{
    public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {
            $customer =  Mage::getSingleton('customer/session')->getCustomer()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
            ->save();
            if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been saved.'));
                Mage::dispatchEvent('dbm_gtm_newsletter_subscribe');
   $helper = Mage::helper('monbentonewsletter/data');
        $listID = Mage::getStoreConfig('newsletter/mailjet/contactslist');
        $helper->addContactToList($customer->getEmail(), $listID);
            } else {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been removed.'));
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
