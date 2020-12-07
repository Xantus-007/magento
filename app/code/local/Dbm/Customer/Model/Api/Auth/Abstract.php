<?php

abstract class Dbm_Customer_Model_Api_Auth_Abstract extends Mage_Api_Model_Resource_Abstract
{
    protected $_customerSession;
    protected $_publicMethods = array();

    /**
     * Check whether a customer has been authenticated in this session.
     *
     * @return void
     * @throws Mage_Core_Exception If customer is not authenticated.
     */
    protected function _checkCustomerAuthentication()
    {
        // get customer session object
        $session = $this->_getCustomerSession();
        
        $this->_getStore();
        
        // check whether customer is logged in
        if ( !$session->isLoggedIn() ) {
            // if customer is not logged in throw an exception
            Mage::throwException(Mage::helper('dbm_share')->__('Not logged in'));
        }
    }

    /**
     * Get authenticated customer object.
     *
     * @return Mage_Customer_Model_Customer Authenticated customer object.
     * @throws Mage_Core_Exception If customer is not authenticated or does not exist.
     */
    protected function _getAuthenticatedCustomer()
    {
        // retrieve authenticated customer ID
        $customerId = $this->_getAuthenticatedCustomerId();

        if ( $customerId )
        {
            // load customer
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = Mage::getModel('dbm_customer/customer')
                            ->load($customerId);
            if ( $customer->getId() ) {
                // if customer exists, return customer object
                return $customer;
            }
        }

        // customer not authenticated or does not exist, so throw exception
        Mage::throwException(Mage::helper('dbm_share')->__('Unknown customer'));
    }

    /**
     * Get authenticated customer ID.
     *
     * @return integer Authenticated customer ID, if any; null, otherwise.
     */
    protected function _getAuthenticatedCustomerId()
    {
        // get customer session object
        $session = $this->_getCustomerSession();

        // return authenticated customer ID, if any
        return $session->getCustomerId();
    }

    /**
     * Get store object from supplied website code or from register or session.
     *
     * @param string $code Code
     */
    protected function _getStore( $idStore = null )
    {
        /*
        // get customer session
        $session = $this->_getCustomerSession();

        // if website code not supplied, check for selected store in register or selected website in session
        if ( null === $code ) {
            // try to get selected store from register
            $store = Mage::registry('current_store');
            if ( $store ) {
                return $store;
            }

            // try to get selected website code from session
            $code = $session->getCurrentWebsiteCode();
            
            if ( !$code ) {
                // if no store in register or website code in session, throw an exception
                Mage::throwException(Mage::helper('dbm_share')->__('No Store set'));
            }
        }
        
        // load website from code
        /** @var Mage_Core_Model_Website $website *\/
        $website = Mage::getModel('core/website')
                        ->load($code, 'code');
        if ( !$website->getId() ) {
            // if unknown website, throw an exception
            Mage::throwException(Mage::helper('dbm_share')->__('Invalid Store') .': ' . $code);
        }
        
        // get the default store of the website
        $store = $website->getDefaultStore();
        
        // register the current store
        Mage::app()->setCurrentStore($store);
        Mage::register('current_store', $store, true);

        // set the current website code in the session
        $session->setCurrentWebsiteCode($website->getCode());
        $session->setCurrentStore($store->getId());

        // return store object
        return $store;
         */
        
        $session = $this->_getCustomerSession();
        
        if(is_null($idStore))
        {
            $store = Mage::registry('current_store');
            if ( $store ) {
                return $store;
            }

            // try to get selected website code from session
            $idStore = $session->getCurrentStore();
            if ( !$idStore ) {
                // if no store in register or website code in session, throw an exception
                Mage::throwException(Mage::helper('dbm_share')->__('No Store set'));
            }
        }
        
        Mage::app()->setCurrentStore($idStore);
        Mage::register('current_store', $idStore, true);
        
        return Mage::getModel('core/store')->load($idStore);
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        if ( !$this->_customerSession ) {
        }
        $this->_customerSession = Mage::getSingleton('customer/session');

        return $this->_customerSession;
    }
    
    protected function _setStoreId($storeId)
    {
        $store = Mage::getModel('core/store')->load($storeId);
        
        Mage::register('current_store', $storeId, true);
        Mage::app()->setCurrentStore($store);
        
        if($store->getId())
        {
            $this->_getCustomerSession()->setCurrentStore($store->getId());
        }
        
    }
    
    protected function _getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = ($this->_getSession()->hasData($this->_storeIdSessionField)
                        ? $this->_getSession()->getData($this->_storeIdSessionField) : 0);
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault(Mage::helper('dbm_share')->__('Store does not exist'));
        }

        return $storeId;
    }
}
