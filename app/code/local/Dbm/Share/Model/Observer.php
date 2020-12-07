<?php

class Dbm_Share_Model_Observer extends Auguria_Sponsorship_Model_Observer
{
    const POINT_BIRTHDAY    = 50;
    
    public function elementAddHandler(Varien_Event_Observer $observer)
    {
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        $element = $observer->getEvent()->getElement();
        
        
        if($customer->getId() && $element->getId())
        {
            $pointsAttribute = 'points_'.$element->getType();
            
            $origPoints = $customer->getData($pointsAttribute);
            $addPoints = Mage::helper('dbm_share/customer')->getAllowedPointsForElement($customer, $element);

            $customer->setData($pointsAttribute, $origPoints + $addPoints);
            $customer->save();
            
            $this->_addFidelityPoints($customer, $addPoints, 'order');
            
            Mage::helper('dbm_customer')->updateCustomerStatus($customer);
        }
    }

    public function elementRemoveHandler(Varien_Event_Observer $observer)
    {
        
    }
    
    public function birthdayCronHandler()
    {
        Mage::log('start cron birthday');

        $customers = Mage::getModel('customer/customer')
                        ->getCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('dob',array('like'=>'%-'.date('m-d').' %'))
                ;
        
        $customerHelper = Mage::helper('dbm_customer');
        $shareHelper = Mage::helper('dbm_share');
        
        $storeId = Mage::app()->getStore()->getStoreId();
        
        foreach ($customers as $customer) {
            Mage::log('send mail for '.$customer->getEmail());
            $storeId = $customer->getStoreId() > 0 ? $customer->getStoreId() : 1;
            
            Mage::app()->setCurrentStore($storeId);
                       
            $customer = Mage::helper('auguria_sponsorship')->addFidelityPoints($customer, Dbm_Share_Model_Observer::POINT_BIRTHDAY);
            Mage::helper('dbm_customer')->addCustomerPoints($customer, Dbm_Customer_Helper_Data::ATTRIBUTE_POINTS_OTHER, Dbm_Share_Model_Observer::POINT_BIRTHDAY);
            $customer->save();
                          
            $customerHelper->updateCustomerStatus($customer);
            
            $customerEmail = $customer->getEmail();
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $vars = array(
                'name'=> $customerName, 
                'nbpoints' => Dbm_Share_Model_Observer::POINT_BIRTHDAY,
                'store' => $storeId
            );            
            $sender = array(
                'name' => Mage::getStoreConfig('trans_email/ident_general/name', $storeId),
                'email' => Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
            $templateId = 'dbm_share_config/email/birthday_template';
            
            $mailer = Mage::getModel('core/email_template_mailer');
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($customerEmail, $customerName);
            $mailer->addEmailInfo($emailInfo);
            $mailer->setSender($sender);
            $mailer->setStoreId($storeId);
            $mailer->setTemplateId(Mage::getStoreConfig($templateId, $storeId));
            $mailer->setTemplateParams($vars);
            $mailer->send();
        }

        Mage::log('end cron birthday');
    }
}
