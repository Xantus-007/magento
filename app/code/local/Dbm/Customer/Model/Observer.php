<?php

class Dbm_Customer_Model_Observer
{

    public function predispatchHandler(Varien_Event_Observer $observer)
    {
        $session = Mage::getModel('dbm_customer/session');
        if ($session->getIsMobile()) {
            $helper = Mage::helper('dbm_country');
            $conf = $helper->getAutoRedirectData();
            $storeView = $helper->getStoreViewByLocale($conf['country'], $conf['lang']);
            Mage::log('GETTING COUNTRY FROM REDIRECT : ' . $storeView->getCode(), null, 'debug.log');

            $store = Mage::getModel('core/store')->load($storeView->getId());

            Mage::register('current_store', $storeView->getId(), true);
            Mage::app()->setCurrentStore($store);

            Mage::getSingleton('customer/session')->setCurrentStore($storeView->getId());
            Mage::log('CURRENT STORE : ' . Mage::app()->getStore()->getId(), null, 'debug.log');
            //Mage::getDesign()->setArea('frontend') //Area (frontend|adminhtml)
            //        ->setPackageName('default') //Name of Package
            //        ->setTheme('iphone'); // Name of theme
        }
    }

    public function layoutLoadHandler(Varien_Event_Observer $observer)
    {
        $session = Mage::getModel('dbm_customer/session');

        if ($session->getIsMobile()) {
            $layout = $observer->getEvent()->getLayout();

            $layout->getUpdate()->addHandle('dbm_customer_is_mobile');
        }
    }

    public function orderSuccessHandler(Varien_Event_Observer $observer)
    {
        $session = Mage::getModel('dbm_customer/session');
        $mobileOptim = $session->getIsMobileOptim();
        if ($session->getIsMobile() && $mobileOptim !== true) {
            Mage::app()->getFrontController()->getResponse()->setRedirect('monbento://orderSuccess');
        }
    }

    public function orderCancelHandler(Varien_Event_Observer $observer)
    {
        $session = Mage::getModel('dbm_customer/session');
        $mobileOptim = $session->getIsMobileOptim();
        if ($session->getIsMobile() && $mobileOptim !== true) {
            Mage::app()->getFrontController()->getResponse()->setRedirect('monbento://orderCancel');
        }
    }

    public function orderErrorHandler(Varien_Event_Observer $observer)
    {
        $session = Mage::getModel('dbm_customer/session');
        $mobileOptim = $session->getIsMobileOptim();
        if ($session->getIsMobile() && $mobileOptim !== true) {
            Mage::app()->getFrontController()->getResponse()->setRedirect('monbento://orderError');
        }
    }

    //DEPRECATED
    /*
      public function updatePrice(Varien_Event_Observer $observer)
      {
      $item = $observer->getEvent()->getQuoteItem();

      $currentCustomer = Mage::helper('dbm_customer')->getCurrentCustomer();

      if($currentCustomer->getId())
      {
      $coef = null;
      switch($currentCustomer->getProfileStatus())
      {
      case 2:
      $coef = 5;
      break;
      case 3:
      $coef = 10;
      break;
      }

      if($coef > 0)
      {
      $origPrice = $item->getProduct()->getFinalPrice();

      $newPrice = ceil($origPrice * (1 - $coef/100));
      $item->setOriginalCustomPrice($newPrice);
      $item->save();
      }
      }
      }
     */

    public function getCustomerConfig()
    {
        
    }

    public function addConditionToSalesRule(Varien_Event_Observer $observer)
    {
        //@TODO: ADD global switch
        //get additional conditions and add >Tweeted about< condition
        $additional = $observer->getAdditional();
        $conditions = (array) $additional->getConditions();

        $conditions = array_merge_recursive($conditions, array(
            array(
                'label' => 'Statut client',
                'value' => 'dbm_customer/condition_status'),
        ));

        $additional->setConditions($conditions);
        $observer->setAdditional($additional);

        return $observer;
    }

    public function categoryCollectionLoadHandler(Varien_Event_Observer $observer)
    {
        $currentStore = Mage::app()->getStore();
        $collection = $observer->getEvent()->getCategoryCollection();
        $helper = Mage::helper('dbm_customer');

        if ($currentStore->getCode() != Mage_Core_Model_Store::ADMIN_CODE) {
            foreach ($collection as $item) {
                $model = Mage::getModel('catalog/category')->load($item->getId());
                if (!$helper->isCategoryAllowedForCurrentCustomer($model)) {
                    //$collection->removeItemByKey($item->getId());
                }
            }
        }
    }

    public function categoryPredispatchHandler(Varien_Event_Observer $observer)
    {
        $currentCategory = $observer->getCategory();
        $store = Mage::app()->getStore();

        if ($currentCategory && $currentCategory->getId() && $store->getCode() != Mage_Core_Model_Store::ADMIN_CODE) {
            if (!Mage::helper('dbm_customer')->isCategoryAllowedForCurrentCustomer($currentCategory)) {
                //Mage::app()->getResponse()->setRedirect(Mage::getUrl());
            }
        }
    }

    public function payHandler(Varien_Event_Observer $observer)
    {
        $order = $observer->getInvoice()->getOrder();
        $cId = $order->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($cId);

        if ($customer->getId()) {
            $cPoints = $customer->getData('points_other');
            $points = intval($order->getGrandTotal());
            $customer->setData('points_other', $points + $cPoints);
            $customer->save();
            Mage::helper('dbm_customer')->updateCustomerStatus($customer);
        }
    }

    public function customerCreateHandler(Varien_Event_Observer $observer)
    {
        $customer = $observer->getCustomer();
        $fiscalId = Mage::app()->getRequest()->getParam('fiscalId') ? Mage::app()->getRequest()->getParam('fiscalId') : Mage::getSingleton('checkout/session')->getData('fiscal_id'); 
  
        if (Mage::getSingleton('checkout/session')->getData('fiscal_id')) {
            Mage::getSingleton('checkout/session')->unsetData('fiscal_id');
        }
        
        if (!empty($fiscalId)) {
            $customer->setData('fiscal_id', $fiscalId);
        }

        if (!$customer->getId()) {
            $data = Mage::helper('dbm_customer')->generateCustomerCreateProfileData($customer);
            if ($data) {
                foreach ($data as $key => $val) {
                    $customer->setData($key, $val);
                }
            }
        }
    }

    public function loginHandler(Varien_Event_Observer $observer)
    {
        $dateFormat = 'yyyy-MM-dd HH:mm:ss';
        $customer = $observer->getCustomer();
        $helper = Mage::helper('dbm_customer');

        $createdAt = Mage::app()->getLocale()->date();
        $to = Mage::app()->getLocale()->date();
        $to->set('2017-03-03 23:59:59', $dateFormat);

        $createdAt->set($customer->getCreatedAt(), $dateFormat);

        /*
          echo $createdAt;
          echo '<pre>IS VALID :</pre>';
          var_dump($helper->isValidProfile($customer));
          echo '<pre>IS LATER : </pre>';
          var_dump($to->isLater($createdAt));
          exit();
         */

        if (!$helper->isValidProfile($customer) && $to->isLater($createdAt)) {
            $data = $helper->generateCustomerProfileData($customer);

            foreach ($data as $key => $val) {
                $customer->setData($key, $val);
            }

            $customer->save();

            $helper->updateCustomerStatus($customer);
        }
    }

    public function registerSuccessHandler(Varien_Event_Observer $observer)
    {
        $isNewCustomer = Mage::getSingleton('customer/session')->getIsNew();
        if ($isNewCustomer) {
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addHandle('dbm_customer_register_tracker');
            Mage::getSingleton('customer/session')->setIsNew(false);
        }
    }

    public function beforeAddressSave(Varien_Event_Observer $observer)
    {

        try {
            $customerAddress = $observer->getCustomerAddress();
            $customer = $customerAddress->getCustomer();

            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode(1, 'fiscal_id');
            $attributeId = $attributeModel->getId();
            
            $fiscal_id = Mage::app()->getRequest()->getPost('fiscal_id');

            if (!empty($fiscal_id)) 
            {
                $read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $connection = Mage::getSingleton('core/resource');
                $customerEntityTable = $connection->getTableName('customer_entity_varchar');
                $up = $write->update($customerEntityTable, array("value" => $fiscal_id),
                    array(
                        'entity_id = ?' => $customer->getId(),
                        'attribute_id = ?' => $attributeId
                    )
                ); 

                Mage::getSingleton('customer/session')->getCustomer()->setFiscalId($fiscal_id);    
            }
            
        } catch (Exception $e) {
            //$e->getMessage();
        }
    }
}
