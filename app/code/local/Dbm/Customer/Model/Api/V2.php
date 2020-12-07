<?php

class Dbm_Customer_Model_Api_V2 extends Dbm_Customer_Model_Api_Auth_Abstract
{

    protected $_isCartInited = false;

    public function login($storeView, $login, $password)
    {

        Mage::log('CALLING CUSTOMER LOGIN', null, 'api.xml');

        //Change store view
        $this->_getCustomerSession()->setCurrentStore($storeView);

        $this->_setStoreId($storeView);
        $session = $this->_getCustomerSession();

        $this->_getCustomerSession()->setCurrentStore($storeView);

        // authenticate customer
        $authenticated = $session->login($login, $password);

        if($authenticated)
        {
            $customer = $this->_getAuthenticatedCustomer();
            $_SESSION['customer_monbento']['id'] = $customer->getId();
            $dCustomer = Mage::getModel('dbm_customer/customer')->load($customer->getId());
            Mage::helper('dbm_customer')->updateCustomerStatus($dCustomer);
            $result = $dCustomer->toApiArray($dCustomer);

            $result['notifications'] = count(Mage::helper('dbm_customer')->getNotifications($customer)->toApiArray());
        }

        Mage::log('LOGGING IN :'.session_id(), null, 'api.xml');

        // return authentication result
        return $result;
    }

    public function facebookLogin($storeView, $fbId, $accessToken)
    {
        Mage::log('ACCESS TOKEN : '.$accessToken, null, 'api.xml');
        if($accessToken == '(null)')
	{
		$helper = Mage::helper('dbm_share');
		Mage::throwException($helper->__('An error occured connecting to Facebook'));
	}

        $this->_getCustomerSession()->setCurrentStore($storeView);
        $this->_setStoreId($storeView);
        $session = $this->_getCustomerSession();
        $session->setCurrentStore($storeView);

        $model = Mage::getModel('dbm_fb/api');
        $model->customerLogin($fbId, $accessToken);

        $customer = $this->_getAuthenticatedCustomer();

        Mage::helper('dbm_customer')->updateCustomerStatus($customer);

        if($customer->getId())
        {
            $_SESSION['customer_monbento']['id'] = $customer->getId();
            $dCustomer = Mage::getModel('dbm_customer/customer')->load($customer->getId());
            Mage::helper('dbm_customer')->updateCustomerStatus($dCustomer);
            $result = $dCustomer->toApiArray($dCustomer);
        }

        return $result;
    }

    public function logout($storeView)
    {
Mage::log('LOGGING OUT : '.$storeView, null, 'api.xml');
        $this->_setStoreId($storeView);
        $this->_getCustomerSession()->logout();
        Mage::getSingleton('customer/session')->logout();

        return true;
    }

    public function test()
    {
        $result = false;

        // check whether customer is actually authenticated
        $this->_checkCustomerAuthentication();

        // retrieve customer object
        $customer = $this->_getAuthenticatedCustomer();
        // do something for authenticated customer

        if($customer)
        {
            $result = true;
        }

        return $result;
    }

    public function addToCart($storeView, $productId, $qty = 1, $params = null, $replace = true, $displayCurrency)
    {
        //$this->_checkCustomerAuthentication();
        $this->_setStoreId($storeView);
        $isValid = true;
        $result = array(
            'messages' => array(),
            'qty' => 0
        );
        $trans = Mage::helper('dbm_share');

        $product = Mage::getModel('catalog/product');
        $cart = $this->_getCart();
        $cart->init();

        if($product->load($productId) && $product->getId() > 0)
        {
            if(is_object($params))
            {
                $params = $this->_formatProductParams($params);

                $params['product'] = $product->getId();
                $params['qty'] = $qty;
                $params['related_product'] = null;
            }
            else
            {
                $params = array('qty' => $qty);
            }

            //Update existing products
            /*
            $items = $cart->getItems();
            $updated = false;

            if($items)
            {
                $updateData = null;
                foreach($items as $item)
                {
                    if(isset($params['super_attribute']))
                    {
                        $confSearchProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($params['super_attribute'], $product);
                    }

                    if($item->getProduct()->getId() == $product->getId() && !isset($params['super_attribute']))
                    {

                        if(!is_array($updateData))
                        {
                            $updateData = array();
                        }

                        if(!$replace)
                        {
                            $qty += $item->getQty();
                        }

                        //$updateData[$item->getId()] = array('qty' => $qty);
                    }
                    elseif(isset($params['super_attribute']))
                    {
                        if($item->getProductId() == $confSearchProduct->getId())
                        {
                            if(!is_array($updateData))
                            {
                                $updateData = array();
                            }

                            if(!$replace)
                            {
                                $qty += $item->getQty();
                            }
                            echo 'UPDATE  :'.$qty;
                            exit();
                            $updateData[$item->getId()] = array('qty' => $qty);
                        }
                    }
                }

                if(is_array($updateData))
                {
                    //$cart->updateItems($updateData);
                    //$cart->save();

                    //$updated = true;
                }
            }
            if($qty > 0 && !$updated)
            {
            */
                try {
                    $add = true;
                    foreach($product->getCategoryIds() as $category) {
                        $cat = Mage::getModel('catalog/category')->load($category);
                        if(!Mage::helper('dbm_customer')->isCategoryAllowedForCurrentCustomer($cat)){
                            Mage::log('AJOUT PANIER RESTRICTION GOURMETS : '.$cat->getName());
                            $add = false;
                        }
                    }

                    if($add) {
                        $cart->addProduct($productId, $params);
                        $cart->save();
                    } else {
                        $message = $trans->__('This product cannot be added to your cart. Clubento member only can.', $product->getName());
                        $result['messages'][] = $message;
                        Mage::throwException($trans->__($message));
                    }
                }
                catch (Exception $err)
                {
                    $testProduct = Mage::getModel('catalog/product')->load($productId);

                    if($testProduct->getId() && $testProduct->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE)
                    {
                        $optionIds = array();
                        foreach($params['bundle_option'] as $optionId)
                        {
                            $optionIds[] = $optionId;
                        }

                        $selections = $testProduct->getTypeInstance()->getSelectionsByIds($optionIds, $testProduct);

                        foreach($selections as $selection)
                        {
                            if($selection->getStockItem()->getQty() == 0 || !$selection->getIsInStock())
                            {
                                $message = $trans->__('%s product is not in stock', $selection->getName());
                            }
                        }

                    }
                    $isValid = true;

                    if(!$message)
                    {
			$tempMessages = explode("\n", $err->getMessage());
			$tempMessages = array_unique($tempMessages);
			//Mage::log('ERROR MESSAGE : '.print_r($temp, true), null, 'api.xml');
                        foreach($tempMessages as &$tmpMessage)
			{
				$tmpMessage = $trans->__($tmpMessage);
			}

			$message = implode("\n", $tempMessages);//$trans->__($err->getMessage());
                    }

                    $result['messages'][] = $message;
                    Mage::throwException($trans->__($message));
                }
            //}

            if($isValid)
            {
                Mage::log('IS VALID');
                $result['qty'] = $this->_getDistinctCartProducts();
                $result['messages'][] = $trans->__('Product has been added to cart');
            }
        }

        return $this->getCartItems($storeView,$displayCurrency);
    }

    public function setItemQty($storeView, $itemId, $qty, $replace = true, $displayCurrency)
    {
        $this->_setStoreId($storeView);

        $cart = $this->_getCart();
        $currentItem = $cart->getQuote()->getItemById($itemId);

        if($currentItem && $currentItem->getId())
        {
            if(!$replace)
            {
                //adding to existing qty
                $qty += $currentItem->getQty();
            }

            $updateData = array($itemId => array('qty' => $qty));
            $cart->updateItems($updateData);
            $cart->save();
        }

        return $this->getCartItems($storeView,$displayCurrency);
    }

    public function removeFromCart($storeView, $itemId, $displayCurrency)
    {
        //$this->_checkCustomerAuthentication();
        $this->_setStoreId($storeView);
        $cart = $this->_getCart();

        if($cart && $cart->getItems())
        {
            $cart->removeItem($itemId)->save();
        }

        $this->_cartWasUpdated();

        return $this->getCartItems($storeView,$displayCurrency);
    }

    public function emptyCart($storeView, $displayCurrency)
    {
        $this->_setStoreId($storeView);
        $cart = $this->_getCart();

        if($cart)
        {
            $cart->truncate();
            $cart->save();
            $this->_cartWasUpdated();
        }

        return $this->getCartItems($storeView,$displayCurrency);
    }

    public function getCartItems($storeView, $displayCurrency)
    {
        $this->_setStoreId($storeView);
        $cart = $this->_getCart();

        $result = array();
        $model = Mage::getModel('dbm_catalog/catalog_product_api_v2');
        $result['items'] = array();

        $remove = array();
        $currentClubRule = '';

        if($cart && $cart->getItems())
        {
            foreach($cart->getItems() as $item)
            {
                if(!$item->isDeleted())
                {
                    if($item->hasData('parent_item_id') && $item->getData('parent_item_id') > 0)
                    {
                        $remove[] = $item->getId();
                    }

                    $product = $item->getProduct();
                    if(!isset($result['items'][$product->getId()]))
                    {
                        $tmpProduct = $model->getProductInfo($product->getId(), $storeView, null, 'id', $item->getId());

                        $tmpProduct['price'] = $item->getPriceInclTax();

                        if(isset($tmpProduct['bundle_children']) && count($tmpProduct['bundle_children']))
                        {
                            $imageUrlParams = array();

                            foreach($tmpProduct['bundle_children'] as $option)
                            {
                                $imageUrlParams[] = $option['option_id'].'_'.$option['selection_id'];
                            }

                            //Setting custom image for product
                            $url = Mage::getUrl('dbm-catalog/bundle/generate_image', array('_query' => array(
                                'store_view' => $storeView,
                                'product_id' => $tmpProduct['product_id'],
                                'items' => $imageUrlParams
                            )));

                            $tmpProduct['image'] = $url;
                            $tmpProduct['bundle_thumb'] = $url;
                        }

                        if($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
                        {
                            $myProduct = Mage::getModel('catalog/product')->load($item->getProduct()->getId());

                            $col = $myProduct->getUsedProductCollection(); //->addAttributeToSelect('*')->addFilterByRequiredOptions();

                            $declis = Mage::helper('dbm_utils/product')->getDeclinaisons($myProduct);

                            foreach($declis as $simple_product){
                                $remove[] = $simple_product->getId();
                            }
                        }

                        $result['items'][$item->getId()] = array(
                            'product' => $tmpProduct,
                            'item_id' => $item->getId(),
                            'qty' => $item->getQty()
                        );
                    }
                    else
                    {
                        $result['items'][$item->getId()]['qty'] += $item->getQty();
                    }

                    $appliedRules = explode(',', $item->getAppliedRuleIds());

                    if(in_array(Dbm_Customer_Helper_Data::DISCOUNT_ID_CHEF, $appliedRules))
                    {
                        $currentClubRule = $this->_getRuleLabel(Dbm_Customer_Helper_Data::DISCOUNT_ID_CHEF);
                    }

                    if(in_array(Dbm_Customer_Helper_Data::DISCOUNT_ID_SECOND, $appliedRules))
                    {
                        $currentClubRule = $this->_getRuleLabel(Dbm_Customer_Helper_Data::DISCOUNT_ID_SECOND);
                    }
                }
            }
        }

        for($i = 0; $i < count($remove); $i++)
        {
            unset($result['items'][$remove[$i]]);
        }
        $quote = $cart->getQuote();
        //$quote->collectTotals();

        $result['coupon_code'] = $quote->getCouponCode();

        $addresses = $quote->getAddressesCollection();
        $discount = 0;


        foreach ($quote->getAllItems() as $item){
            $discount += $item->getDiscountAmount();
        }

        $total = number_format(($quote->getGrandTotal() +  $discount), 2);
        $totalPromo = number_format($quote->getGrandTotal(), 2);

        if(!$displayCurrency){
            $session = Mage::getSingleton('core/session');
            $country = explode('_',$session->getLocale());
            $devise = Mage::helper('dbm_country')->getDeviseByCountry(strtoupper($country[1]));
            $currency = Mage::app()->getLocale()->currency($devise);
            $result['total'] = $currency->toCurrency($total, array('locale' => $session->getLocale()));
            $result['total_promo'] = $currency->toCurrency($totalPromo, array('locale' => $session->getLocale()));
        } else {
            $result['total'] = $total;
            $result['total_promo'] = $totalPromo;
        }

        $result['club_rule'] = $currentClubRule;
        //Searching if right is ok for promo code :

        return $result;
    }

    protected function _getRuleLabel($ruleId)
    {
        $rule = Mage::getModel('salesrule/rule')->load($ruleId);
        $result = null;

        if($rule->getId())
        {
            $result = $rule->getName();
        }

        return $result;
    }

    public function getFollowers($idCustomer)
    {
        $this->_checkCustomerAuthentication();
        $customer = $this->_getAuthenticatedCustomer();

        $search = Mage::getModel('dbm_customer/customer')->load($idCustomer);

        $collection = $search->getFollowers();
        return $customer->collectionToApiArray($collection);
    }

    public function getFollowing($idCustomer)
    {
        $this->_checkCustomerAuthentication();
        $customer = $this->_getAuthenticatedCustomer();

        $search = Mage::getModel('dbm_customer/customer')->load($idCustomer);

        $collection = $search->getFollowing();
        return $customer->collectionToApiArray($collection);
    }

    public function follow($idCustomer)
    {
        $this->_checkCustomerAuthentication();
        $currentCustomer = $this->_getAuthenticatedCustomer();
        $following = Mage::getModel('dbm_customer/customer');
        $following->load($idCustomer);

        return $currentCustomer->follow($following);
    }

    public function unFollow($idCustomer)
    {
        $this->_checkCustomerAuthentication();
        $currentCustomer = $this->_getAuthenticatedCustomer();
        $following = Mage::getModel('dbm_customer/customer');
        $following->load($idCustomer);

        return $currentCustomer->unfollow($following);
    }

    public function search($nickname, $page = 0)
    {
        $this->_checkCustomerAuthentication();
        $customer= Mage::getModel('dbm_customer/customer');
        $collection = $customer->searchByNickname($nickname);
        /*
            ->setPageSize(Mage::helper('dbm_share')->getApiListPageSize())
            ->setCurPage($page)
        ;
         */
        $collection->getSelect()->limitPage($page, Mage::helper('dbm_share')->getApiListPageSize());

        return $customer->collectionToApiArray($collection);
    }

    public function applyCoupon($couponCode, $storeView, $displayCurrency)
    {
        $result = false;
        $this->_setStoreId($storeView);
        $trans = Mage::helper('dbm_share');

        $cart = $this->_getCart();
        $quote = $cart->getQuote();
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

        if($couponCode)
        {
            if ($couponCode == $quote->getCouponCode())
            {
                $result = true;
            }
            elseif(count($cart->getItems()))
            {
                $result = false;
            }
            else
            {
                Mage::throwException($trans->__('You cannot apply a coupon on an empty cart'));
            }
        }

        return $this->getCartItems($storeView,$displayCurrency);
    }

    public function getSocialFriends($plateform, $friends)
    {
        $result = array();
        $this->_checkCustomerAuthentication();
        switch($plateform)
        {
            case Dbm_Customer_Helper_Data::SOCIAL_PLATEFORM_FACEBOOK:
                $collection = Mage::getModel('dbm_fb/api')->findFbFriendsFromArray($friends);
                $result = Mage::getModel('dbm_customer/customer')->collectionToApiArray($collection);

                break;
        }

        return $result;
    }

    public function getSponsorPoints()
    {
        $this->_checkCustomerAuthentication();
        $customer = $this->_getAuthenticatedCustomer();
        $points = Mage::helper('auguria_sponsorship')->getPoints($customer);

        return $points['accumulated'];
    }

    public function getNotifications()
    {
        $result = array();
        $this->_checkCustomerAuthentication();
        $customer = $this->_getAuthenticatedCustomer();

        $manager = Mage::helper('dbm_customer')->getNotifications($customer);

        return $manager->toApiArray();
    }

    protected function _getCart()
    {
        $cart = Mage::getSingleton('checkout/cart');
        if(!$this->_isCartInited)
        {
            $cart->init();
            $this->_isCartInited = true;
        }

        return $cart;
    }

    protected function _getDistinctCartProducts()
    {
        $cart = $this->_getCart();
        $result = array();

        foreach($cart->getItems() as $item)
        {
            $productId = $item->getProduct()->getId();
            $result[$productId] = 1;
        }

        return array_sum($result);
    }

    protected function _formatProductParams($params)
    {
        $result = array();
        $keys = array(
            'options'=> 'bundle_option',
            'qtys' => 'bundle_option_qty'
        );

        if(is_array($params->options))
        {
            foreach($keys as $paramKey => $tableName)
            {
                foreach($params->{$paramKey} as $option)
                {
                    $result[$tableName][$option->key] = $option->value;
                }
            }
        }

        if(isset($params->super_attribute)
            && is_array($params->super_attribute)
            && count($params->super_attribute)
        )
        {
            $result['super_attribute'] = array();

            foreach($params->super_attribute as $confData)
            {
                $result['super_attribute'][$confData->key] = $confData->value;
            }
        }

        return $result;
    }

    protected function _cartWasUpdated()
    {
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    }
}
