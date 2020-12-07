<?php

/**
 * Class Cartsguru_CartRecovery_Helper_Data
 */
class Cartsguru_CartRecovery_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOG_FILE = "cartsguru.log";

    protected $configBasePath = 'cartsguru/cartsguru_group/';

    // Get customer language from browser
    public function getBrowserLanguage()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            foreach (explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) as $accept) {
                if (preg_match("!([a-z-]+)(;q=([0-9\\.]+))?!", trim($accept), $found)) {
                    $langs[] = $found[1];
                    $quality[] = (isset($found[3]) ? (float)$found[3] : 1.0);
                }
            }
            // Order the codes by quality
            array_multisort($quality, SORT_NUMERIC, SORT_DESC, $langs);
            // get list of stores and use the store code for the key
            $stores = Mage::app()->getStores(false, true);
            // iterate through languages found in the accept-language header
            foreach ($langs as $lang) {
                $lang = substr($lang, 0, 2);
                return $lang;
            }
        }
        return null;
    }

    // Get customer group name
    public function getCustomerGroupName($customerId)
    {
        $groupName = 'not logged in';
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $groupName = Mage::getSingleton('customer/group')
                ->getCollection()
                ->addFieldToFilter('customer_group_id', $groupId)
                ->addFieldToSelect('customer_group_code')
                ->getFirstItem()
                ->getData('customer_group_code');
        } elseif ($customerId) {
            $customer = Mage::getModel("customer/customer")
                ->getCollection()
                ->addAttributeToFilter('entity_id', $customerId)
                ->getFirstItem();
            if ($customer->getId()) {
                $groupId = $customer->getGroupId();
                $groupName = Mage::getSingleton('customer/group')
                    ->getCollection()
                    ->addFieldToFilter('customer_group_id', $groupId)
                    ->addFieldToSelect('customer_group_code')
                    ->getFirstItem()
                    ->getData('customer_group_code');
            }
        }
        return strtolower($groupName);
    }

    // Check if customer has orders
    public function isNewCustomer($customerId)
    {
        if ($customerId) {
            $orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $customerId);
            return $orders->count() === 0;
        }
        return false;
    }

    // Get store from admin
    public function getStoreFromAdmin()
    {
        // Check admin base url is included into current url
        $isAdminRequest = $this->startsWith(Mage::helper('core/url')->getCurrentUrl(), Mage::getUrl('adminhtml'));
        if (!$isAdminRequest) {
            return Mage::app()->getStore();
        }

        $store_id = null;
        if (strlen($code = Mage::app()->getRequest()->getParam('store'))) {
            $store_id = $code;
        } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) { // store level
            $store_id = $code;
        } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) { // website level
            $website_id = $code;
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        } elseif (strlen($code = Mage::app()->getRequest()->getParam('website'))) {
            $website_id = $code;
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        }

        if ($store_id) {
            return Mage::app()->getStore($store_id);
        } else {
            return Mage::app()->getStore();
        }
    }

    //checks if haystack starts with needle
    protected function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    // Save config in store
    public function setStoreConfig($key, $value, $store = null)
    {
        if (!$store) {
            $store = $this->getStoreFromAdmin();
        }
        // If the store id is 0 should save into global context
        if ($store->getId() == Mage_Core_Model_App::ADMIN_STORE_ID) {
            Mage::getConfig()->saveConfig($this->configBasePath . $key, $value, 'default', Mage_Core_Model_App::ADMIN_STORE_ID);
            Mage::app()->reinitStores();
        } else {
            Mage::getConfig()->saveConfig($this->configBasePath . $key, $value, 'stores', $store->getStoreId());
            $store->resetConfig();
        }
        if ($key === 'authkey') {
            $this->updateApiUser();
        }
    }

    //create or update api user
    protected function updateApiUser()
    {

        if (!($apiUserStoreConfigId = $this->getStoreConfig('apiUserId'))){
            $userName = 'cartsguru';
            $cartsguruRoleId = Mage::getModel('api/role')->getCollection()->addFieldToFilter('role_name', $userName)->addFieldToFilter('role_type', 'G')->getFirstItem()->getId();

            $apiUserExisting = Mage::getModel('api/user')->load($userName, 'username');

            if(!$apiUserExisting->getId())
            {
                $apiUser = Mage::getModel('api/user')
                    ->setData(array(
                        'username' => $userName,
                        'firstname' => $userName,
                        'lastname' => $userName,
                        'email' => $admin = 'api.magento@carts.guru',
                        'api_key' => $this->getAuthKey(),
                        'api_key_confirmation' => $this->getAuthKey(),
                        'is_active' => 1,
                        'user_roles' => '',
                        'assigned_user_role' => '',
                        'role_name' => '',
                        'roles' => array($cartsguruRoleId)
                    ));
                $apiUser->save();

                $apiUser->setRoleIds(array($cartsguruRoleId))
                    ->setRoleUserId($apiUser->getUserId())
                    ->saveRelations();

                $apiUserId = $apiUser->getId();
            }
            else
            {
                $apiUserId = $apiUserExisting->getId();
            }

            $this->setStoreConfig('apiUserId', $apiUserId);
        }
        else
        {
            Mage::getModel('api/user')->getCollection()->addFieldToFilter('user_id', $apiUserStoreConfigId)->getFirstItem()->setApiKey($this->getAuthKey())->save();
        }
    }

    //delete store configuration
    public function deleteStoreConfig($key, $store = null)
    {
        if (!$store) {
            $store = $this->getStoreFromAdmin();
        }
        // If the store id is 0 should save into global context
        if ($store->getId() == Mage_Core_Model_App::ADMIN_STORE_ID) {
            Mage::getConfig()->deleteConfig($this->configBasePath . $key, 'default', Mage_Core_Model_App::ADMIN_STORE_ID);
            Mage::app()->reinitStores();
        } else {
            Mage::getConfig()->deleteConfig($this->configBasePath . $key, 'stores', $store->getStoreId());
            $store->resetConfig();
        }

        if ($key === 'authkey') {
            $this->deleteStoreConfig('auth', $store);
            $this->deleteApiUser();
        }
    }


    protected function deleteApiUser(){
        if ($apiUserId = $this->getStoreConfig('apiUserId')){
            Mage::getModel('api/user')->getCollection()->addFieldToFilter('user_id', $apiUserId)->getFirstItem()->delete();
            $this->deleteStoreConfig('apiUserId');
        }
    }

    // Get store config
    public function getStoreConfig($key, $store = null)
    {
        if (!$store) {
            $store = $this->getStoreFromAdmin();
        }

        return $store->getConfig($this->configBasePath . $key);
    }

    // Get auth key from store config
    public function getAuthKey($store = null)
    {
        $authKey = $this->getStoreConfig('authkey', $store);
        if (empty($authKey)) {
            $authKey = $this->getStoreConfig('auth', $store);
        }
        return $authKey;
    }

    // Get store config
    public function clearCache()
    {
        $cache = Mage::app()->getCacheInstance();
        $types = $cache->getTypes();
        foreach ($types as $type => $value) {
            $cache->cleanType($type);

            Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type));
        }
        // Enterprise clean cache
        if (class_exists(Enterprise_PageCache_Model_Cache)) {
            Enterprise_PageCache_Model_Cache::getCacheInstance()->clean(Enterprise_PageCache_Model_Processor::CACHE_TAG);
        }
    }

    //Get current cart information in correct format
    public function getCurrentCartInfo()
    {
        $totalItemsInCart = Mage::getSingleton('checkout/cart')->getItemsCount();

        if ($totalItemsInCart == 0) {
            return null;
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $abandonedCart = Mage::getModel('cartsguru_cartrecovery/webservice')->getCurrentAboundedCartData($quote);

        // Set cartId
        $abandonedCart['cartId'] = $abandonedCart['id'];
        return $abandonedCart;
    }

    // Get list of generated cart rules
    public function getCartRules()
    {
        $cartRules = array();
        $ruleColections = Mage::getResourceModel('salesrule/rule_collection')
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('uses_per_customer', 1)
            ->addFieldToFilter('discount_amount', array('neq' => 0));
        foreach ($ruleColections as $ruleColection) {
            $cartRule = $ruleColection->getData();
            $cartRules[] = array(
                'title' => $cartRule['name'],
                'code' => (string)$cartRule['code'],
                'sendingStartDate' => $cartRule['from_date'],
                'expirationDate' => $cartRule['to_date'],
                'freeShipping' => (boolean)$cartRule['simple_free_shipping'],
                'reductionPercent' => (float)$cartRule['discount_amount']
            );
        }
        return $cartRules;
    }

    // Generate cart rules
    public function createCartRules($data)
    {
        foreach ($data['coupons'] as $coupon) {
            $from = $coupon['sendingStartDate'];
            $to = $coupon['expirationDate'];
            $code = $coupon['code'];
            // Check if we have coupon already
            if ($this->getCartRuleByCode($code)) {
                continue;
            }
            $ruleData = array(
                'product_ids' => null,
                'name' => $data['title'],
                'description' => "Carts Guru generated rule",
                'is_active' => 1,
                'website_ids' => null,
                'customer_group_ids' => array(0, 1),
                'coupon_type' => 2,
                'coupon_code' => $code,
                'uses_per_coupon' => 100000,
                'uses_per_customer' => 1,
                'from_date' => $from,
                'to_date' => $to,
                'sort_order' => null,
                'is_rss' => 1,
                'simple_action' => 'by_percent',
                'discount_amount' => $data['freeShipping'] ? 0 : (float)($data['reductionPercent']),
                'discount_qty' => null,
                'discount_step' => null,
                'website_ids' => array(Mage::app()->getStore()->getWebsiteId()),
                'simple_free_shipping' => $data['freeShipping'] ? 1 : 0,
                'stop_rules_processing' => 1,
            );
            $ruleModel = Mage::getModel('salesrule/rule');
            $ruleModel->loadPost($ruleData);
            $ruleModel->save();
        }
        return json_encode(array('status' => true));
    }

    // Delete cart rule by coupon code
    public function deleteCartRules($data)
    {
        foreach ($data['couponCodes'] as $coupon) {
            $rulecolection = Mage::getResourceModel('salesrule/rule_collection')
                ->addFieldToFilter('code', $coupon);
            foreach ($rulecolection as $rule) {
                $rule->delete();
            }
        }
        return json_encode(array('status' => true));
    }

    // Get cart rule by coupone code
    public function getCartRuleByCode($code)
    {
        $rulecolection = Mage::getResourceModel('salesrule/rule_collection')
            ->addFieldToFilter('code', $code);
        foreach ($rulecolection as $rule) {
            return $rule;
        }
        return false;
    }

    /**
     * Checks if cartsguru is configured
     */
    public function isConfigured()
    {
        return $this->getStoreConfig('apiSuccess') == 1;
    }

    /**
     * get cartguru's id for this website
     */
    public function getSiteId()
    {
        return $this->getStoreConfig('siteid');
    }

    /**
     * Get FB pixel from config
     */
    public function getPixel()
    {
        return $this->getStoreConfig('facebook_pixel');
    }

    /**
     * Get facebook CatalogId from config
     */
    public function getCatalogId()
    {
        return $this->getStoreConfig('facebook_catalogId');
    }

    /**
     * Get facebook Messenger from config
     */
    public function getFbm()
    {
        return $this->getStoreConfig("feature_fbm");
    }

    /**
     * Get ci from config
     */
    public function getCi()
    {
        return $this->getStoreConfig("feature_ci");
    }

    /**
     * Get Facebook appId from config
     */
    public function getAppId()
    {
        return $this->getStoreConfig("facebook_appId");
    }

    /**
     * Get Facebook pageId from config
     */
    public function getPageId()
    {
        return $this->getStoreConfig("facebook_pageId");
    }

    /**
     * Get widgets from config
     */
    public function getWidgets()
    {
        return $this->getStoreConfig("feature_widgets");
    }

    /**
     * Get tracker url from config
     */
    public function getTrackerUrl()
    {
        return $this->getStoreConfig("tracker_url");
    }

    /**
     * Get current cart data
     */
    public function getCurrentCartData()
    {
        $cart = $this->getCurrentCartInfo();
        return isset($cart) ? json_encode($cart) : '{}';
    }

    /**
     * get Facebook Ads from config
     */
    public function getFacebook()
    {
        return $this->getStoreConfig("feature_facebook");
    }
}
