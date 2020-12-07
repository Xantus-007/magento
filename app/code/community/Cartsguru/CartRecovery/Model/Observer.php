<?php

/**
 * This class provides methods which calls on magento dispatch events
 * Class Cartsguru_CartRecovery_Model_Observer
 */
class Cartsguru_CartRecovery_Model_Observer
{
    /**
     * Add Customer to next CartsGuru cron job
     * @param $observer
     */
    public function customerSaveBefore($observer)
    {
        $customer = $observer->getCustomer();
        $customer->setData('in_cartsguru_queue', 1);
    }

    /**
     * Set token before quote is save and add quote to next CartsGuru cron job
     * @param $observer
     */
    public function quoteSaveBefore($observer)
    {
        $quote = $observer->getEvent()->getQuote();

        if (!$quote->getData('cartsguru_token')) {
            $tools = Mage::helper('cartsguru_cartrecovery/tools');
            $quote->setData('cartsguru_token', $tools::generateUUID());
        }
        if (!$quote->getData('cartsguru_cron_save')) {
            $quote->setData('in_cartsguru_queue', 1);
        }
    }

    /**
     * Handle order updated, and add it to next CartsGuru cron job
     * @param $observer
     */
    public function orderSaveBefore($observer)
    {
        /* @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();

        // Only trigger when order status change
        if (!$order->getData('cartsguru_cron_save') && $order->getStatus() !== $order->getOrigData('status')) {
            $order->setData('in_cartsguru_queue', 1);

            // Check for source cookie
            $source = Mage::getSingleton('core/cookie')->get('cartsguru-source');
            if (!empty($source)) {
                $order->setData('cartsguru_source', $source);
                Mage::getSingleton('core/cookie')->delete('cartsguru-source');
            }
        }
    }

    /**
     * Saves added product data to fire FB pixel
     * @param $observer
     */
    public function checkoutCartAdd($observer)
    {
        $helper = Mage::helper('cartsguru_cartrecovery');
        $facebook_enabled = $helper->getStoreConfig("feature_facebook");
        if ($facebook_enabled) {
            // Load product
            $product = Mage::getModel('catalog/product')
                ->load(Mage::app()->getRequest()->getParam('product', 0));

            if (!$product->getId()) {
                return;
            }
            $price = $product->getPrice();
            if (!$price) {
                $price = $product->getSpecialPrice();
            }
            // Save product data into session
            Mage::getSingleton('core/session')->setCartsGuruAddToCart(
                new Varien_Object(array(
                    'id' => $product->getId(),
                    'price' => number_format((double)$price, 2, '.', '')
                ))
            );
            // Send source data if present
            $cart = Mage::getSingleton('checkout/session');
            $quote_id = $cart->getQuoteId();

            // Check for source cookie
            $source = Mage::getSingleton('core/cookie')->get('cartsguru-source');

            if (!empty($source) && $quote_id) {
                $cart->getQuote()->setData('cartsguru_source', $source)->save();
            }
        }
    }

    /**
     * Check if we have source query param and set the cookie
     * @param $observer
     */
    public function checkSource($observer)
    {
        $utm_source = Mage::app()->getRequest()->getParam('utm_source');
        $utm_campaign = Mage::app()->getRequest()->getParam('utm_campaign');
        if ($utm_source && $utm_campaign) {
            if (!empty($utm_source) && $utm_source === 'cartsguru-fb' && !empty($utm_campaign)) {
                $cookie = Mage::getSingleton('core/cookie');
                $ttl = 60 * 60 * 24 * 30; // 1 month
                $cookie->set('cartsguru-source', serialize(array(
                    'type' => $utm_source,
                    'campaign' => $utm_campaign,
                    'timestamp' => time()
                )), $ttl, '/');
            }
        }
    }
}
