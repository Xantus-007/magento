<?php

class Monbento_Kiosk_IndexController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('monbento_kiosk/magasin');
    }

    public function indexAction()
    {
        if($this->getRequest()->getParam('mag_code')) {
            $this->_getSession()->setCustomerGroupCode($this->getRequest()->getParam('mag_code'));
        }

        if (!$this->_getSession()->isLogin()) {
            $this->_redirect('*/*/login');
            return;
        }
        
        $this->loadLayout();
        $this->renderLayout();
    }

    public function loginAction() 
    {
        Mage::getDesign()->setTheme('kiosk');
        
        $this->loadLayout();
        $this->renderLayout();
    }

    public function loginPostAction() 
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/login');
            return;
        }

        if ($this->_getSession()->isLogin()) {
            $this->_redirect('*/*/index');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $listUsersMagasin = Mage::helper('monbento_kiosk')->getUsersMagasinList();
                    if($listUsersMagasin)
                    {
                        foreach($listUsersMagasin as $userMagasin) 
                        {
                            if($userMagasin->getUsername() == $login['username'] && Mage::helper('core')->validateHash($login['password'], $userMagasin->getPassword()))
                            {
                                $session->setCustomerGroupCode($userMagasin->getUsername());
                                $this->_redirect('*/*/index');
                                return;
                            }
                        }
                    }
                } catch (Mage_Core_Exception $e) {
                    
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            }
        }

        $session->addError($message);
        $this->_redirect('*/*/login');
        return;
    }

    public function customerlogoutAction()
    {
        $sessionMag = $this->_getSession();
        $magCode = $sessionMag->getCustomerGroupCode();

        $session = Mage::getSingleton('customer/session');
        $session->logout();

        $sessionMag->setCustomerGroupCode($magCode);

        return;
    }
}