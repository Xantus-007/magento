<?php

class Monbento_Kiosk_Model_Observer extends Varien_Object 
{

    public function checkKioskSession(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('monbento_kiosk');

        $request = Mage::app()->getRequest();
        $currentRouteParams = array(
            $request->getModuleName(),
            $request->getControllerName(),
            $request->getActionName()
        );
        $currentRoute = implode('/', $currentRouteParams);

        if($currentRoute == 'monbento-kiosk/index/index' && !Mage::getSingleton('monbento_kiosk/magasin')->isLogin())
        {
            Mage::app()->getResponse()->setRedirect(Mage::getUrl($helper->getKioskLoginRoute()));
        }
        elseif(Mage::getSingleton('monbento_kiosk/magasin')->isLogin())
        {
            Mage::getDesign()->setTheme('kiosk');
        }
    }

    public function paymentMethodIsAvailable(Varien_Event_Observer $observer)
    {
        $paymentMethodInstance = $observer->getMethodInstance();
        /* @var $paymentMethodInstance Mage_Payment_Model_Method_Abstract */
        $result = $observer->getResult();

        if(Mage::getSingleton('monbento_kiosk/magasin')->isLogin())
        {
            if(!$paymentMethodInstance instanceof Mage_Payment_Model_Method_Cashondelivery && !$paymentMethodInstance instanceof Mage_Vads_Model_Standard) $result->isAvailable = false;
        }
        else
        {
            if($paymentMethodInstance instanceof Mage_Payment_Model_Method_Cashondelivery) $result->isAvailable = false;
        }
        return true;
    }

    public function placeOrderForCustomerGroup(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('monbento_kiosk');

        if($currentCustomerGroupCode = Mage::getSingleton('monbento_kiosk/magasin')->isLogin())
        {
            $currentCustomerGroupId = $helper->getCustomerGroupIdByCode($currentCustomerGroupCode);
            $order = $observer->getEvent()->getOrder();
            $order->setCustomerGroupId($currentCustomerGroupId);
        }
    }

    public function deleteOrderForPayLater(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();
        $paymentMethod = $order->getPayment()->getMethod();

        if($paymentMethod == 'cashondelivery' && Mage::getSingleton('monbento_kiosk/magasin')->isLogin())
        {
            $customerId = $order->getCustomerId();
            $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();

            try
            {
                $quote = Mage::getModel('sales/quote')->load($quoteId);
                $quote->setCustomerId($customerId);
                $quote->setIsActive(true);
                $quote->save();

                Mage::getSingleton('checkout/session')->setLastOrderId(null);

                $appEmulation = Mage::getSingleton('core/app_emulation');
                //Start environment emulation of the specified store
                $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(0);
                $order->cancel()->save();
                $order->delete();
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }
            catch(Mage_Exception $e) {
                Mage::log($e->getMessage());
            }
        }
    }

    public function reactiveQuote(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();
        $paymentMethod = $order->getPayment()->getMethod();

        if($paymentMethod == 'cashondelivery' && Mage::getSingleton('monbento_kiosk/magasin')->isLogin())
        {
            try
            {
                $quote = $observer->getQuote();
                $quote->setIsActive(true);
                $quote->save();
            }
            catch(Mage_Exception $e) {
                Mage::log($e->getMessage());
            }
        }
    }

    public function setCustomerGroup(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('monbento_kiosk');
        
        if($currentCustomerGroupCode = Mage::getSingleton('monbento_kiosk/magasin')->isLogin())
        {
            $customer = $observer->getCustomer();

            if($customer->getIsNewCustomer() && !$customer->getMyCustomKeyForIsAlreadyProcessed()) 
            {
                $customer->setMyCustomKeyForIsAlreadyProcessed(true);
                $currentCustomerGroupId = $helper->getCustomerGroupIdByCode($currentCustomerGroupCode);
                $customer->setGroupId($currentCustomerGroupId)
                    ->save();
            }
        }
    }
}
