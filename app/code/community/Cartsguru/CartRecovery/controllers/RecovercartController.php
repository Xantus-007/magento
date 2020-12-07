<?php

class Cartsguru_CartRecovery_RecovercartController extends Mage_Core_Controller_Front_Action
{
    /**
     *
     */
    protected function redirectToCart()
    {
        $url = Mage::helper('checkout/cart')->getCartUrl();

        //Keep params except cart_id & cart_token
        $queryParams = array();
        $params = $this->getRequest()->getParams();
        foreach ($params as $key => $value) {
            if ($key === 'cart_token' || $key === 'cart_id') {
                continue;
            }
            $queryParams[] = $key . '=' . $value;
        }

        //Concats query
        if (!empty($queryParams)) {
            $url .= strpos($url, '?') !== false ? '&' : '?';
            $url .= implode('&', $queryParams);
        }

        $this->getResponse()->setRedirect($url)->sendResponse();
    }

    public function indexAction()
    {
        // Get request params
        $params = $this->getRequest()->getParams();

        // Stop if no enoguth params
        if (!isset($params['cart_id']) || !isset($params['cart_token'])) {
            return $this->redirectToCart();
        }

        // Load quote by id
        $quote = Mage::getModel('sales/quote')->load($params['cart_id']);

        // Stop if quote does not exist
        if (!$quote->getId()) {
            return $this->redirectToCart();
        }

        // Check quote token
        $token = $quote->getData('cartsguru_token');
        if (!$token || $token != $params['cart_token']) {
            return $this->redirectToCart();
        }

        // Auto log customer if we can
        if ($quote->getCustomerId()) {
            //Gest customer
            $customer = Mage::getModel('customer/customer')->load($quote->getCustomerId());
            Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
        }
        // Get current cart
        $cart = Mage::getSingleton('checkout/cart');

        foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
            $found = false;
            foreach ($quote->getAllItems() as $quoteItem) {
                if ($quoteItem->compare($item)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $newItem = clone $item;
                $quote->addItem($newItem);
                if ($quote->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $newChild = clone $child;
                        $newChild->setParentItem($newItem);
                        $quote->addItem($newChild);
                    }
                }
            }
        }

        try {
            // Process discounts
            if (isset($params['cart_discount'])) {
                $cartDiscount = $params['cart_discount'];
                $cartRule = Mage::helper('cartsguru_cartrecovery')->getCartRuleByCode($cartDiscount);
                if ($cartRule) {
                    $quote->getShippingAddress()->setCollectShippingRates(true);
                    $quote->setCouponCode($cartRule->getCode());
                    $quote->collectTotals();
                }
            }

            $quote->save();
            $cart->setQuote($quote);
            $cart->init();
            $cart->save();
        } catch (Mage_Core_Exception $e) {
            Mage::log('RecoverCart: Error recovering cart ' . $params['cart_id'] . ', ' . $e->getMessage(), null, Cartsguru_CartRecovery_Helper_Data::LOG_FILE);
        } catch (Exception $e) {
            Mage::log('RecoverCart: Error recovering cart ' . $params['cart_id'] . ', ' . $e->getMessage(), null, Cartsguru_CartRecovery_Helper_Data::LOG_FILE);
        }

        // Redirect to checkout
        return $this->redirectToCart();
    }
}
