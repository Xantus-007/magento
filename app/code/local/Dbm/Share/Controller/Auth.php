<?php

abstract class Dbm_Share_Controller_Auth extends Dbm_Share_Controller_Abstract
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    public function preDispatch()
    {
        // a brute-force protection here would be nice
        $trans = Mage::helper('dbm_share');
        parent::preDispatch();
        
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $publicActions = $this->_getPublicActions();
        
        if (!in_array($action, $publicActions)) {
            
            if (!$this->_authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
            else
            {
                //Authenticated
                $currentCustomer = Mage::helper('dbm_customer')->getCurrentCustomer();
            
            
                if(!strlen($currentCustomer->getProfileNickname()))
                {
                    $session = Mage::getSingleton('customer/session');
                    $session->addError($trans->__('Please complete your profile before accessing the club'));
                    
                    $this->_redirect('club-customer/account/edit', array('goto-club'=>1));
                }
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    abstract protected function _getPublicActions();
    
    protected function _authenticate(Mage_Core_Controller_Varien_Action $action, $loginUrl = null)
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_getSession()->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current'=>true)));
            if (is_null($loginUrl)) {
                $loginUrl = Mage::helper('customer')->getLoginUrl();
            }
            $action->getResponse()->setRedirect($loginUrl.'?club=1');
            
            return false;
        }
        return true;
    }
}