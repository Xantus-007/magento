<?php

class Cartsguru_CartRecovery_Adminhtml_CartsguruadminController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/quote');
    }

    /**
     * Redirect to specified or current url
     */
    protected function redirectToUrl($url = null)
    {
        if (empty($url)) {
            $url = Mage::helper('core/url')->getCurrentUrl();
        }
        return Mage::app()->getResponse()->setRedirect($url);
    }


    public function indexAction()
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        $store = $helper->getStoreFromAdmin();

        if ($this->getRequest()->isPost()) {
            $authkey = $this->getRequest()->getParam('authkey');
            $siteid = $this->getRequest()->getParam('siteid');
            $reset = $this->getRequest()->getParam('reset');
            // Check if we have post data
            if ($authkey && $siteid) {
                $this->setupConfiguration($helper, $store, $authkey, $siteid);
            } else if ($reset) {
                $this->resetConfiguration($helper, $store);
            }

            return $this->redirectToUrl();
        } else {
            $this->loadLayout();
            $this->renderLayout();

            if (!$helper->getStoreConfig('installEventSent')) {
                $webservice = Mage::getModel('cartsguru_cartrecovery/webservice');
                $webservice->followProgress('installed');
                $helper->setStoreConfig('installEventSent', true, $store);
            }
        }
    }

    /**
     * Save CartsGuru credentials and send quote and orders history if a connection can be established
     */
    protected function setupConfiguration($helper, $store, $authkey, $siteid)
    {
        $webservice = Mage::getModel('cartsguru_cartrecovery/webservice');
        $helper->setStoreConfig('authkey', $authkey, $store);
        $helper->setStoreConfig('siteid', $siteid, $store);
        $result = $webservice->checkAddress($siteid, $store);
        if ($result && $result->status === 'success') {
            $helper->setStoreConfig('apiSuccess', 1, $store);
            $webservice->followProgress('subscribed');
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Successfully connected'));
            if ($result->isNew) {
                $webservice->sendHistory($store);
            }
        } else {
            $helper->setStoreConfig('apiSuccess', 0, $store);
            Mage::getSingleton('adminhtml/session')->addError(__('Connection error'));
        }
    }

    /**
     * delete CartsGuru credentials
     */
    protected function resetConfiguration($helper, $store)
    {
        $helper->deleteStoreConfig('apiSuccess', $store);
        $helper->deleteStoreConfig('authkey', $store);
        $helper->deleteStoreConfig('siteid', $store);
    }

    public function registerAction()
    {
        $state_id;
        $this->loadLayout();
        $this->renderLayout();
        $helper = Mage::helper('cartsguru_cartrecovery');
        $store = $helper->getStoreFromAdmin();
        $webservice = Mage::getModel('cartsguru_cartrecovery/webservice');
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            // Check if US is selected and check number of region to convert it in iso code
            if ($data['country_id'] === 'US') {
                if ($data['region']) {
                    $state = Mage::getModel('directory/region')->load($data['region']);
                    $state_id = $state->getCode();
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(__('Please select state'));
                    return $this->redirectToUrl();
                }
            }

            $fields = array(
                'country' => $data['country_id'],
                'website' => $data['website'],
                'phoneNumber' => $data['phonenumber'],
                //User creation
                'email' => $data['email'],
                'lastname' => $data['lastname'],
                'firstname' => $data['firstname'],
                'password' => $data['password'],
                'plugin' => 'magento',
                'pluginVersion' => $webservice::_CARTSGURU_VERSION_
            );
            // Check availability of region
            if ($state_id) {
                $fields['state'] = $state_id;
            }
            $result = $webservice->registerNewCustomer($fields, $store);

            if ($result->status === 'error') {
                if ($result->error == 'Country not supported') {
                    $webservice->followProgress('subscribe-other-country', $fields);
                    Mage::getSingleton('adminhtml/session')->addError(__('Your country is not supported for now. Please contact us on https://carts.guru?platform=magento'));
                } elseif ($result->error == 'Account already exists') {
                    Mage::getSingleton('adminhtml/session')->addError(__('This email is already associated with existing account'));
                } elseif ($result->error == 'This domain name is already registered') {
                    Mage::getSingleton('adminhtml/session')->addError(__('This domain name is already associated with existing account'));
                } else {
                    $webservice->followProgress('subscribe-error', $fields);
                    Mage::getSingleton('adminhtml/session')->addError(__('An error occurs during the registration. Please contact us on https://carts.guru?platform=magento'));
                }
                return $this->redirectToUrl();
            } elseif ($result->status === 'success') {
                $helper->setStoreConfig('authkey', $result->apiToken, $store);
                $helper->setStoreConfig('siteid', $result->siteId, $store);
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Successfully connected'));
                $webservice->followProgress('registered', $fields);
                $webservice->sendHistory($store);
                if (property_exists($result, "redirectUrl")) {
                    return $this->redirectToUrl($result->redirectUrl);
                } else {
                    return $this->redirectToUrl();
                }
            }
        }
    }

}
