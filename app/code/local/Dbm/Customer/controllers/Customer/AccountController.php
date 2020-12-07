<?php

$uri =  Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AccountController.php';
require_once($uri);


class Dbm_Customer_Customer_AccountController extends Mage_Customer_AccountController
{
    /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction()
    {
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                $this->_mobileRedirect('*/*/forgotpassword', 'monbento://forgotFinished');
                return;
            }
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newPassword = $customer->generatePassword();
                    $customer->changePassword($newPassword, false);
                    $customer->setPasswordCreatedAt(time());
                    $customer->sendPasswordReminderEmail();

                    $this->_getSession()->addSuccess($this->__('A new password has been sent.'));
                    $this->_mobileRedirect('*/*', 'monbento://forgotFinished');

                    return;
                }
                catch (Exception $e){
                    $this->_getSession()->addError($e->getMessage());
                }
            } else {
                $this->_getSession()->addError($this->__('This email address was not found in our records.'));
                $this->_getSession()->setForgottenEmail($email);
            }
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->_mobileRedirect('*/*/forgotpassword', 'monbento://forgotFinished');
            return;
        }

        $this->_mobileRedirect('*/*/forgotpassword', 'monbento://forgotFinished');
    }
    
    /**
     * Recaptcha wrapper
     */
    public function createPostAction()
    {
        if (Mage::getStoreConfig("fontis_recaptcha/recaptcha/customer"))
        { // check that recaptcha is actually enabled

            $privatekey = Mage::getStoreConfig("fontis_recaptcha/setup/private_key");
            // check response
            $resp = Mage::helper("fontis_recaptcha")->recaptcha_check_answer(  $privatekey,
                $_SERVER["REMOTE_ADDR"],
                $_POST["recaptcha_challenge_field"],
                $_POST["recaptcha_response_field"]
            );

            if ($resp == true)
            { // if recaptcha response is correct, use core functionality
                $this->_createPost();
            }
            else
            {
                $this->_getSession()->addError($this->__('Your reCAPTCHA entry is incorrect. Please try again.'));
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                $this->_redirectReferer();
                return;
            }
        }
        else
        { // if recaptcha is not enabled, use core function
            $this->_createPost();
        }
    }
    
    protected function _createPost()
    {
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                ->setEntity($customer);
            
            $customerData = $customerForm->extractData($this->getRequest());

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                    ->setEntity($address);

                $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors  = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }

            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setPasswordConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->setPasswordCreatedAt(time());
                    $customer->save();
                    $this->_dispatchRegisterSuccess($customer);

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        
                        //PATCH DBM FOR APP REDIRECTION
                        $session = Mage::getSingleton('dbm_customer/session');
                        if($session->getIsMobile())
                        {
                            $url = 'monbento://loginSuccess?login='.$this->getRequest()->getParam('email').'&password='.$this->getRequest()->getPost('password');
                            $this->getResponse()->setRedirect($url);
                        }
                        else
                        {
                            $this->_redirectSuccess($url);
                        }
                        
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
    }
    
    protected function _mobileRedirect($url, $mobileUrl)
    {
        $session = Mage::getSingleton('dbm_customer/session');
        
        if($session->getIsMobile())
        {
            $url = $mobileUrl;
        }
        else
        {
            $url = Mage::getUrl($url);
        }
        
        $this->getResponse()->setRedirect($url);
    }
}