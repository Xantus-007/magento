<?php

/**
 * This class using to get api answers
 * Class Cartsguru_CartRecovery_Model_Webservice
 */
class Cartsguru_CartRecovery_Model_Webservice
{
    protected $apiBaseUrl = 'https://api.carts.guru';
    protected $historySize = 50;


    /* Main cache tag constant */
    const CACHE_TAG = 'cartsguru_cartrecovery';
    const PRODUCT_CACHE_TAG = 'cartsguru_products';
    const PRODUCT_CACHE_TTL = 7200; // 2h in seconds
    const QUOTES_CACHE_TAG = 'cartsguru_carts';
    const QUOTES_CACHE_TTL = 1800; // 30min in seconds

    const _CARTSGURU_VERSION_ = '1.4.0';

    /**
     * Check if cartsguru is configured for $store
     */
    public function isStoreConfigured($store = null)
    {
        if (!$store) {
            $store = Mage::app()->getStore();
        }
        $helper = Mage::helper('cartsguru_cartrecovery');
        return $helper->getStoreConfig('siteid', $store) && $helper->getAuthKey($store);
    }

    /**
     * If value is empty return ''
     * @param $value
     * @return string
     */
    protected function notEmpty($value)
    {
        return ($value) ? $value : '';
    }

    /**
     * This method format date in json format
     * @param $date
     * @return bool|string
     */
    protected function formatDate($date)
    {
        return date('Y-m-d\TH:i:sP', strtotime($date));
    }

    /**
     * Get category names
     * @param $item
     * @return array
     */
    public function getCatNames($product)
    {
        $categoryNames = array();
        $categoryIds = $product->getCategoryIds();
        foreach ($categoryIds as $categoryId) {
            $category = Mage::getModel('catalog/category')->getCollection()->addFieldToFilter('entity_id', $categoryId)->getFirstItem();
            $ids = explode('/', $category->getPath());
            foreach ($ids as $id) {
                $category = Mage::getModel('catalog/category')->getCollection()->addFieldToFilter('entity_id', $id)->addAttributeToSelect('name')->getFirstItem();
                $categoryNames[] = $category->getName();
            }
        }

        if (empty($categoryNames)) {
            $categoryNames = array(
                0 => $this->notEmpty(null),
                1 => $this->notEmpty(null)
            );
        }

        return $categoryNames;
    }

    /**
     * This method calculate total taxes included, shipping excluded
     * @param $obj order or quote
     * @return float
     */
    public function getTotalATI($items)
    {
        $totalATI = (float)0;

        foreach ($items as $item) {
            $totalATI += $item['totalATI'];
        }

        return $totalATI;
    }
    /**
     * get image Url path if set
     */
    public function getProductImageUrl($product)
    {
        if ($product->getImage() == 'no_selection' || !$product->getImage()) {
            return $imageUrl = $this->notEmpty(null);
        }

        $image = null;

        $smallImageSizePath = property_exists('Mage_Catalog_Helper_Image', 'XML_NODE_PRODUCT_SMALL_IMAGE_WIDTH') ?
            Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_SMALL_IMAGE_WIDTH :
            'catalog/product_image/small_width';

        if (Mage::getStoreConfig($smallImageSizePath) >= 120) {
            $image = Mage::helper('catalog/image')->init($product, 'small_image');
        } else {
            // Need resize image
            $image = Mage::helper('catalog/image')->init($product, 'image')->resize(120, 120);
        }

        //Get the url
        $image = (string)$image;

        //Work with the normal image if no small image available
        if (empty($image)) {
            $image = Mage::helper('catalog/image')->init($product, 'image');
            $image = (string)$image;
        }

        return $image;
    }

    /**
     * This method build items from order or quote
     * @param $obj order or quote
     * @return array
     */
    public function getItemsData($obj)
    {
        $cache = Mage::app()->getCache();
        $items = array();
        foreach ($obj->getAllVisibleItems() as $item) {

            //Check not already sent
            $cacheId = 'cg-product-' . $item->getStoreId() . '-' . $item->getProductId();
            $productData = $cache->load($cacheId);
            if (!$productData) {
                $product = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->setStoreId($item->getStoreId())
                    ->addFieldToFilter('entity_id', $item->getProductId())
                    ->addAttributeToSelect(['url_key', 'image', 'small_image'])
                    ->getFirstItem();

                $categoryNames = $this->getCatNames($product);
                $productData = array(
                    'url' => $product->getProductUrl(),           // URL of product sheet
                    'imageUrl' => $this->getProductImageUrl($product), // URL of product image
                    'universe' => $this->notEmpty($categoryNames[1]),  // Main category
                    'category' => $this->notEmpty(end($categoryNames)) // Child category
                );

                $tags = array(Cartsguru_CartRecovery_Model_Webservice::CACHE_TAG, Cartsguru_CartRecovery_Model_Webservice::PRODUCT_CACHE_TAG);
                $cache->save(json_encode($productData), $cacheId, $tags, Cartsguru_CartRecovery_Model_Webservice::PRODUCT_CACHE_TTL);
            } else {
                $productData = json_decode($productData, true);
                if ($productData['imageUrl'] == '') {
                    $product = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->setStoreId($item->getStoreId())
                        ->addFieldToFilter('entity_id', $item->getProductId())
                        ->addAttributeToSelect(['url_key', 'image', 'small_image'])
                        ->getFirstItem();
                    $productData['imageUrl'] = $this->getProductImageUrl($product);
                }
            }

            $quantity = (int)$item->getQtyOrdered() > 0 ? (int)$item->getQtyOrdered() : (int)$item->getQty();

            $items[] = array(
                'id' => $item->getId(),                          // SKU or product id
                'label' => $item->getName(),                        // Designation
                'quantity' => $quantity,                               // Count
                'totalET' => (float)$item->getPrice() * $quantity,         // Subtotal of item, taxe excluded
                'totalATI' => (float)$item->getPriceInclTax() * $quantity, // Subtotal of item, taxe included
                'url' => $productData['url'],
                'imageUrl' => $productData['imageUrl'],
                'universe' => $productData['universe'],
                'category' => $productData['category']
            );
        }
        return $items;
    }

    /**
     * This method return order data in cartsguru format
     * @param $order
     * @return array
     */
    public function getOrderData($order, $store = null)
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        //Order must have a status
        if (!$order->getStatus()) {
            return null;
        }

        //Customer data
        $gender = $this->genderMapping($order->getCustomerGender());
        $customerId = $order->getCustomerId();

        //Address
        $address = $order->getBillingAddress();

        //Items details
        $items = $this->getItemsData($order);

        // Custom fields
        $custom = array(
            'language' => $helper->getBrowserLanguage(),
            'customerGroup' => $helper->getCustomerGroupName($customerId),
            'isNewCustomer' => $helper->isNewCustomer($customerId)
        );
        // We do this to include the discounts in the totalET
        $totalET = number_format((float)($order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount()), 2, '.', '');

        return array(
            'siteId' => $helper->getStoreConfig('siteid', $store),                         // SiteId is part of plugin configuration
            'id' => $order->getIncrementId(),                                        // Order reference, the same display to the buyer
            'creationDate' => $this->formatDate($order->getCreatedAt()),                       // Date of the order as string in json format
            'cartId' => $order->getQuoteId(),                                            // Cart identifier, source of the order
            'totalET' => $totalET,                                                        // Amount excluded taxes and excluded shipping
            'totalATI' => (float)$order->getGrandTotal(),                                  // Paid amount
            'currency' => $order->getOrderCurrencyCode(),                                  // Currency as USD, EUR
            'paymentMethod' => $order->getPayment()->getMethodInstance()->getTitle(),           // Payment method label
            'state' => $order->getStatus(),                                             // raw order status
            'accountId' => $order->getCustomerEmail(),                                                          // Account id of the buyer
            'ip' => $order->getRemoteIp(),                                           // User IP
            'civility' => $this->notEmpty($gender),                                        // Use string in this list : 'mister','madam','miss'
            'lastname' => $this->notEmpty($address->getLastname()),                        // Lastname of the buyer
            'firstname' => $this->notEmpty($address->getFirstname()),                       // Firstname of the buyer
            'email' => $this->notEmpty($order->getCustomerEmail()),                                         // Email of the buyer
            'phoneNumber' => $this->notEmpty($address->getTelephone()),                       // Landline phone number of buyer (internationnal format)
            'countryCode' => $this->notEmpty($address->getCountryId()),                       // Country code of buyer
            'items' => $items,                                                          // Details
            'custom' => $custom                                                          // Custom fields array
        );
    }

    /**
     * This method send order data by api
     * @param $order
     */
    public function sendOrder($order)
    {
        $store = Mage::app()->getStore($order->getStoreId());

        //Check is well configured
        if (!$this->isStoreConfigured($store)) {
            return;
        }

        //Get data, stop if none
        $orderData = $this->getOrderData($order, $store);
        if (empty($orderData)) {
            return;
        }

        // Check for source cookie
        if ($order->getData('cartsguru_source') !== '') {
            $orderData['source'] = unserialize($order->getData('cartsguru_source'));
            $order->setData('cartsguru_source', '');
        }

        //Push data to api
        $this->doPostRequest('/orders', $orderData, $store);
    }

    /**
     * This method sends source data to API
     * @param $source
     */
    public function sendSource($cartId, $source)
    {
        $store = Mage::app()->getStore();
        $helper = Mage::helper('cartsguru_cartrecovery');
        //Check is well configured
        if (!$this->isStoreConfigured($store)) {
            return;
        }
        $payload = array(
            "siteId" => $helper->getStoreConfig('siteid', $store),
            "cartId" => $cartId,
            "source" => $source
        );
        //Push data to api
        $this->doPostRequest('/source', $payload, $store);
    }

    /**
     * Map int of gender to string
     * @param $gender
     * @return string
     */
    public function genderMapping($gender)
    {
        switch ((int)$gender) {
            case 1:
                return 'mister';
            case 2:
                return 'madam';
            default:
                return '';
        }
    }

    /**
     * This method return abounded cart data in cartsguru api format
     * @param $quote
     * @return array|void
     */
    public function getAbandonedCartData($quote, $store = null)
    {
        //Check required info
        $email = $quote->getCustomerEmail();
        if (!$email || sizeof($quote->getAllVisibleItems()) == 0) {
            return;
        }

        $helper = Mage::helper('cartsguru_cartrecovery');

        //Customer data
        $gender = $this->genderMapping($quote->getCustomerGender());
        $lastname = $quote->getCustomerLastname();
        $firstname = $quote->getCustomerFirstname();

        //Lookup for phone & country, on request params
        $request = Mage::app()->getRequest()->getParams();
        $phone = '';
        $country = '';

        if (isset($request['billing'])) {
            if (isset($request['billing']['telephone'])) {
                $phone = $request['billing']['telephone'];
            }

            if (isset($request['billing']['country_id'])) {
                $country = $request['billing']['country_id'];
            }

        }

        //Continue lookup for phone & country, on  quote billing address
        if (!$phone || !$country) {
            $address = $quote->getBillingAddress();
            if ($address && !$phone) {
                $phone = $address->getTelephone();
            }
            if ($address && !$country) {
                $country = $address->getCountryId();
            }
        }

        //Finish lookup for phone & country, on customer billing address
        if (!$phone || !$country) {
            $customer = $quote->getCustomer();
            $customerAddress = $customer ? $customer->getDefaultBillingAddress() : null;
            if ($customerAddress && !$phone) {
                $phone = $customerAddress->getTelephone();
            }
            if ($customerAddress && !$country) {
                $country = $customerAddress->getCountryId();
            }
        }

        // Custom fields
        $custom = array(
            'language' => $helper->getBrowserLanguage(),
            'customerGroup' => $helper->getCustomerGroupName($quote->getCustomerId()),
            'isNewCustomer' => $helper->isNewCustomer($quote->getCustomerId())
        );

        //Recover link
        $recoverUrl = ($quote->getData('cartsguru_token')) ?
            Mage::getBaseUrl() . 'cartsguru/recovercart?cart_id=' . $quote->getId() . '&cart_token=' . $quote->getData('cartsguru_token') :
            '';

        //Items details
        $items = $this->getItemsData($quote);

        return array(
            'siteId' => $helper->getStoreConfig('siteid', $store),         // SiteId is part of plugin configuration
            'id' => $quote->getId(),                                 // Order reference, the same display to the buyer
            'creationDate' => $this->formatDate($quote->getCreatedAt()),       // Date of the order as string in json format
            'totalET' => (float)$quote->getSubtotal(),                    // Amount excluded taxes and excluded shipping
            'totalATI' => (float)$this->getTotalATI($items),               // Amount included taxes and excluded shipping
            'currency' => $quote->getQuoteCurrencyCode(),                  // Currency as USD, EUR
            'ip' => $quote->getRemoteIp(),                           // User IP
            'accountId' => $email,                                          // Account id of the buyer
            'civility' => $gender,                                         // Use string in this list : 'mister','madam','miss'
            'lastname' => $this->notEmpty($lastname),                      // Lastname of the buyer
            'firstname' => $this->notEmpty($firstname),                     // Firstname of the buyer
            'email' => $this->notEmpty($email),                         // Email of the buyer
            'phoneNumber' => $this->notEmpty($phone),                         // Landline phone number of buyer (internationnal format)
            'countryCode' => $this->notEmpty($country),                       // Country code of the buyer
            'recoverUrl' => $recoverUrl,                                     // Direct link to recover the cart
            'items' => $items,                                          // Details
            'custom' => $custom                                          // Custom fields array
        );
    }

    /**
     * This method get current abounded cart data without customer info
     * @param $quote
     */
    public function getCurrentAboundedCartData($quote, $store = null)
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        $recoverUrl = ($quote->getData('cartsguru_token')) ?
            Mage::getBaseUrl() . 'cartsguru/recovercart?cart_id=' . $quote->getId() . '&cart_token=' . $quote->getData('cartsguru_token') :
            '';

        //Items details
        $items = $this->getItemsData($quote);

        return array(
            'siteId' => $helper->getStoreConfig('siteid', $store),       // SiteId is part of plugin configuration
            'id' => $quote->getId(),                                 // Order reference, the same display to the buyer
            'creationDate' => $this->formatDate($quote->getCreatedAt()),       // Date of the order as string in json format
            'totalET' => (float)$quote->getSubtotal(),                    // Amount excluded taxes and excluded shipping
            'totalATI' => (float)$this->getTotalATI($items),               // Amount included taxes and excluded shipping
            'currency' => $quote->getQuoteCurrencyCode(),                  // Currency as USD, EUR
            'ip' => $quote->getRemoteIp(),                           // User IP
            'recoverUrl' => $recoverUrl,                                     // Direct link to recover the cart
            'items' => $items,                                          // Details
        );
    }


    /**
     * This method send abounded cart data
     * @param $quote
     */
    public function sendAbandonedCart($quote)
    {
        $store = Mage::app()->getStore($quote->getStoreId());

        //Check is well configured
        if (!$this->isStoreConfigured($store)) {
            return;
        }

        //Get data and continue only if exist
        $cartData = $this->getAbandonedCartData($quote, $store);
        if (!$cartData) {
            return;
        }

        //Check not already sent
        $cache = Mage::app()->getCache();
        $cacheId = 'cg-quote-' . $quote->getId();
        $cachedMd5 = $cache->load($cacheId);
        $dataMd5 = md5(json_encode($cartData));

        if ($dataMd5 == $cachedMd5) {
            return;
        }

        $tags = array(Cartsguru_CartRecovery_Model_Webservice::CACHE_TAG, Cartsguru_CartRecovery_Model_Webservice::QUOTES_CACHE_TAG);
        $cache->save($dataMd5, $cacheId, $tags, Cartsguru_CartRecovery_Model_Webservice::QUOTES_CACHE_TTL);


        $this->doPostRequest('/carts', $cartData, $store);
    }

    /**
     * Get customer Firstname
     * @param $customer
     * @return string
     */
    public function getFirstname($customer, $address)
    {
        $firstname = $customer->getFirstname();
        if (!$firstname && $address) {
            $firstname = $address->getFirstname();
        }

        return $firstname;
    }

    /**
     * Get customer Lastname
     * @param $customer
     * @return string
     */
    public function getLastname($customer, $address)
    {
        $lastname = $customer->getLastname();
        if (!$lastname && $address) {
            $lastname = $address->getLastname();
        }

        return $lastname;
    }

    /**
     * This method get customer data in cartsguru api format
     * @param $customer
     * @return array
     */
    public function getCustomerData($customer, $store = null)
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        $address = $customer->getDefaultBillingAddress();

        $gender = $this->genderMapping($customer->getGender());
        $lastname = $this->getLastname($customer, $address);
        $firstname = $this->getFirstname($customer, $address);
        $phone = '';
        $country = '';
        if ($address) {
            $phone = $address->getTelephone();
            $country = $address->getCountryId();
        }

        return array(
            'siteId' => $helper->getStoreConfig('siteid', $store),     // SiteId is part of plugin configuration
            'accountId' => $customer->getEmail(),                          // Account id of the customer
            'civility' => $gender,                                     // Use string in this list : 'mister','madam','miss'
            'lastname' => $this->notEmpty($lastname),                  // Lastname of the buyer
            'firstname' => $this->notEmpty($firstname),                 // Firstname of the buyer
            'email' => $this->notEmpty($customer->getEmail()),      // Email of the customer
            'phoneNumber' => $this->notEmpty($phone),                     // Landline phone number of buyer (internationnal format)
            'countryCode' => $this->notEmpty($country)
        );
    }

    /**
     * This method send customer data on api
     * @param $customer
     */
    public function sendAccount($customer)
    {
        //Check is well configured
        if (!$this->isStoreConfigured()) {
            return;
        }

        // prepare and send customer Data to CartsGuru api
        $customerData = $this->getCustomerData($customer);
        $this->doPostRequest('/accounts', $customerData);
    }


    /** This method return true if connect to server is ok
     * @return bool
     */
    public function checkAddress($siteid, $store)
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        $requestUrl = '/sites/' . $siteid . '/register-plugin';
        $fields = array(
            'plugin' => 'magento',
            'pluginVersion' => Cartsguru_CartRecovery_Model_Webservice::_CARTSGURU_VERSION_,
            'adminUrl' => Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'cartsguru/admin?cartsguru_admin_action=',
            'storeVersion' => method_exists('Mage', 'getEdition') ? Mage::getVersion() . ' ' . Mage::getEdition() : Mage::getVersion()
        );
        $response = $this->doPostRequest($requestUrl, $fields, $store, true);
        if (!$response) {
            return false;
        }
        return json_decode($response);
    }


    /* Send quote and order history
    *
    * @param object $store
    * @return Mage_Sales_Model_Quote
    */
    public function sendHistory($store)
    {
        $lastOrder = $this->sendLastOrders($store);
        if ($lastOrder) {
            $this->sendLastQuotes($store, $lastOrder->getCreatedAt());
        }
    }

    /**
     *Send last order maxed by historySize
     */
    protected function sendLastOrders($store)
    {

        $orders = array();
        $lastOrder = null;
        $orderCollection = Mage::getModel('sales/order')
            ->getCollection()
            ->setOrder('created_at', 'desc')
            ->setPageSize($this->historySize);
        if ($store->getId() != Mage_Core_Model_App::ADMIN_STORE_ID) {
            $orderCollection->addFieldToFilter('store_id', $store->getId());
        }

        foreach ($orderCollection as $order) {
            $lastOrder = $order;

            //Get order data
            $orderData = $this->getOrderData($order, $store);

            //Append only if we get it
            if (!empty($orderData)) {
                $orders[] = $orderData;
            }
        }

        //Push orders to api
        if (!empty($orders)) {
            $this->doPostRequest('/import/orders', $orders, $store);
        }

        return $lastOrder;
    }

    /**
     * Send all quotes created after $since
     */
    protected function sendLastQuotes($store, $since)
    {
        $quotes = array();
        $last = null;
        $quoteCollection = Mage::getModel('sales/quote')
            ->getCollection()
            ->setOrder('created_at', 'asc')
            ->addFieldToFilter('created_at', array('gt' => $since));
        if ($store->getId() != Mage_Core_Model_App::ADMIN_STORE_ID) {
            $quoteCollection->addFieldToFilter('store_id', $store->getId());
        }

        foreach ($quoteCollection as $quote) {
            $last = $quote;

            if ($quote) {
                //Get quote data
                $quoteData = $this->getAbandonedCartData($quote, $store);

                //Append only if we get it
                if ($quoteData) {
                    $quotes[] = $quoteData;
                }
            }
        }

        //Push quotes to api
        if (!empty($quotes)) {
            $this->doPostRequest('/import/carts', $quotes, $store);
        }

        return $last;
    }

    // Get start information about shop
    public function followProgress($step, $data = null, $store = null)
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        $configs = $helper->getStoreConfig('siteid', $store);
        $fields = array(
            // 'email'  => Mage::app()->getStore()->getConfig('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => Mage::getStoreConfig('contacts/email/recipient_email'),
            'siteId' => $configs ? $configs : '',
            'pluginVersion' => self::_CARTSGURU_VERSION_,
            'storeVersion' => Mage::getVersion(),
            'editionVersion' => method_exists('Mage', 'getEdition') ? Mage::getEdition() : '',
            'step' => $step
        );
        if ($step === 'installed') {
            $fields = array_merge($fields, $this->getStoreInformation());
        }

        if ($step === 'subscribed' || $step === 'registered') {
            $fields = array_merge($fields, $this->getOrderStatistics());
        }

        if ($data) {
            $fields = array_merge($fields, $data);
        }

        $this->doPostRequest('/prestashop/setup-progress', $fields);
    }

    // All information about store owner
    public function getStoreInformation()
    {
        $phoneNumber = Mage::getStoreConfig('general/store_information/phone');

        return array(
            'country' => Mage::getStoreConfig('general/country/default'),
            'phoneNumber' => $phoneNumber ? $phoneNumber : '',
            'website' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),

            'email' => Mage::getSingleton('admin/session')->getUser()->getEmail(),
            'lastname' => Mage::getSingleton('admin/session')->getUser()->getLastname(),
            'firstname' => Mage::getSingleton('admin/session')->getUser()->getFirstname(),
            'language' => Mage::app()->getLocale()->getLocaleCode()
        );
    }

    //Get statistic about all orders
    public function getOrderStatistics($sinceDays = 31)
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        $sinceDays = $sinceDays * -1;
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();

        $result = array(
            'orderCount' => 0,
            'orderTotal' => 0,
            'discountCount' => 0,
            'discountAverage' => 0,
            'currency' => (string)$currency,
        );
        $tablename = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');

        $id_store = Mage::app()
            ->getWebsite(true)
            ->getDefaultGroup()
            ->getDefaultStoreId();
        $orderSql = 'SELECT count(entity_id) as `order_count` , sum(grand_total) AS `order_total`
    FROM `' . $tablename . '` WHERE `created_at` >= DATE_ADD(SYSDATE(), INTERVAL ' . (int)$sinceDays . ' DAY)
    AND `status` = \'complete\'' . ' AND `store_id` = ' . (int)$id_store;
        $discountSql = 'SELECT count(entity_id) as `discount_count` , sum(base_discount_amount) / count(entity_id) AS `discount_avg`
    FROM `' . $tablename . '`
    WHERE `created_at` >= DATE_ADD(SYSDATE(), INTERVAL ' . (int)$sinceDays . ' DAY)
    AND `status` = \'complete\' AND `discount_amount` != 0' . ' AND `store_id` = ' . (int)$id_store;

        if ($results = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($orderSql)) {

            $row = $results[0];
            $result['orderCount'] = round($row['order_count'], 0);
            $result['orderTotal'] = round($row['order_total'], 2);
        }

        if ($results = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($discountSql)) {
            $row = $results[0];
            $result['discountCount'] = round($row['discount_count'], 0);
            if ($result['discountCount'] > 0) {
                $result['discountAverage'] = round($row['discount_avg'], 2);
            }
        }
        return $result;
    }

    // Register user
    public function registerNewCustomer($fields, $store)
    {
        $fields['adminUrl'] = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'cartsguru/admin?cartsguru_admin_action=';
        $response = $this->doPostRequest('/customers', $fields, null, true);
        return $response ? json_decode($response) : false;
    }

    /**
     * This method send data on api path
     * @param $apiPath
     * @param $fields
     * @return string
     */
    protected function doPostRequest($apiPath, $fields, $store = null, $isSync = false)
    {

        $response = null;
        $helper = Mage::helper('cartsguru_cartrecovery');
        $data_string = json_encode($fields);
        $timeout = $isSync ? 30000 : 1000;
        try {

            $url = $this->apiBaseUrl . $apiPath;
            $client = curl_init($url);
            $header = array(
                'x-auth-key: ' . $helper->getAuthKey($store),
                'x-plugin-version' . Cartsguru_CartRecovery_Model_Webservice::_CARTSGURU_VERSION_,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            );
            curl_setopt($client, CURLOPT_HTTPHEADER, $header);
            curl_setopt($client, CURLOPT_POST, 1);
            curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($client, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($client, CURLOPT_NOSIGNAL, 1);
            curl_setopt($client, CURLOPT_TIMEOUT_MS, $timeout);
            $response = curl_exec($client);
            curl_close($client);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $isSync ? $response : '';
    }
}
