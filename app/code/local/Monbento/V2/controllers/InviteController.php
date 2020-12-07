<?php

class Monbento_V2_InviteController extends Mage_Core_Controller_Front_Action
{
    
    public function _getMessageSession()
    {
        return Mage::getSingleton('core/session');
    }
    public function registerAction()
    {
        $request = $this->getRequest();
        
        if($request->isPost())
        {
            $session = Mage::getSingleton('customer/session');
            $currentCustomer = Mage::helper('dbm_customer')->getCurrentCustomer();
            $store = Mage::app()->getStore();
            
            $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $params = $this->getRequest()->getParams();
            
            if(!($currentCustomer->getId()) && $orderId && $order = Mage::getModel('sales/order')->load($orderId))
            {
                $email = $order->getCustomerEmail();
                $address = $order->getAddressesCollection()->getFirstItem();
                try {
                    if($email
                        && $params['password1'] 
                        && $params['password2']
                        && $params['password1'] == $params['password2']
                        /*&& strlen($params['password1']) >= 8*/
                        && $address->getFirstname())
                    {
                        $customerData = array(
                            'email' => $email,
                            'password' => $params['password1'],
                            'firstname' => $address->getFirstname(),
                            'lastname' => $address->getLastname(),
                            'website_id' => $store->getWebsiteId(),
                            'created_in' => $store->getCode()
                        );

                        $customer = Mage::getModel('customer/customer');
                        $customer->addData($customerData);
                        $customer->save();

                        if($customer->getId())
                        {
                            $tempData = Mage::helper('dbm_customer')->generateCustomerProfileData($customer);
                            if($tempData)
                            {
                                foreach($tempData as $key => $val)
                                {
                                    $customer->setData($key, $val);
                                }
                            }
                        }

                        $customer->save();

                        if($customer->getId())
                        {
                            $order->setCustomerId($customer->getId())->save();
                            Mage::helper('dbm_customer')->updateCustomerStatus($customer);
                            $session->login($email, $params['password1']);
                            $this->_redirect('customer/account');
                        }
                    }
                } catch (Exception $ex) {
                    $this->_getMessageSession()->addError($ex->getMessage());
                    $this->_redirect('*/*/message');
                }
            }
        }
    }
    
    public function testAction()
    {
        $this->_forward('message');
    }
    
    public function messageAction()
    {
        $msg = $this->_getMessageSession()->getMessages(true);
        $this->loadLayout(array('default', 'checkout_onepage_success'));
        $this->getLayout()->getMessagesBlock()->addMessages($msg);   
        $this->_initLayoutMessages('core/session');
        $this->renderLayout();
    }
}