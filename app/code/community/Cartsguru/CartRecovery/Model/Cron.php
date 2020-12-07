<?php

/**
 * This class handle CartsGuru Cron Jobs
 * Class Cartsguru_CartRecovery_Model_Cron
 */
class Cartsguru_CartRecovery_Model_Cron
{
    /** Send queued requests up to batch limit set in config */
    public function sendData()
    {
        $startTime = date('o/m/d-H:i:s');

        // initialise request sent count
        $maxRequests = Mage::getStoreConfig('cartsguru/queue/batch_size_limit');
        $sentRequests = 0;
        /* @var Cartsguru_CartRecovery_Model_Webservice $webservice */
        $webservice = Mage::getModel('cartsguru_cartrecovery/webservice');

        //send quotes
        if ($sentRequests < $maxRequests) {
            $quotes = Mage::getModel('sales/quote')
                ->getCollection()
                ->addFieldToFilter('in_cartsguru_queue', '1')
                ->setPageSize($maxRequests - $sentRequests)
                ->setCurPage(1);

            foreach ($quotes as $quote) {
                $webservice->sendAbandonedCart($quote);
                if ($quote->getData('cartsguru_source') !== '') {
                    $webservice->sendSource($quote->getId(), unserialize($quote->getData('cartsguru_source')));
                    $quote->setData('cartsguru_source', '');
                }
                $quote->setData('cartsguru_cron_save', 1)
                    ->setData('in_cartsguru_queue', 0)
                    ->save();
                $sentRequests++;
            }
            $qs = $quotes->count();
        }

        //send orders
        if ($sentRequests < $maxRequests) {
            $orders = Mage::getModel('sales/order')
                ->getCollection()
                ->addFieldToFilter('in_cartsguru_queue', 1)
                ->setPageSize($maxRequests - $sentRequests)
                ->setCurPage(1);

            foreach ($orders as $order) {
                $webservice->sendOrder($order);
                $order->setData('cartsguru_cron_save', 1)
                    ->setData('in_cartsguru_queue', 0)
                    ->save();
                $sentRequests++;
            }
            $os = $orders->count();
        }

        //send customers
        if ($sentRequests < $maxRequests) {
            $customers = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToFilter('in_cartsguru_queue', 1)
                ->setPageSize($maxRequests - $sentRequests)
                ->setCurPage(1);

            foreach ($customers as $customer) {
                $webservice->sendAccount($customer);
                $customer->setData('in_cartsguru_queue', 0);
                $customer->getResource()->saveAttribute($customer, 'in_cartsguru_queue');
                $sentRequests++;
            }
            $cs = $customers->count();
        }

        // log
        if ($sentRequests > 0 && Mage::getStoreConfig('cartsguru/queue/log')) {
            Mage::log('=====CartsGuru data transfer started at ' . $startTime . '=====', null, Cartsguru_CartRecovery_Helper_Data::LOG_FILE);
            if (isset($qs) && $qs > 0) {
                Mage::log('Quotes sent : ' . $qs, null, Cartsguru_CartRecovery_Helper_Data::LOG_FILE);
            }
            if (isset($os) && $os > 0) {
                Mage::log('Orders sent : ' . $os, null, Cartsguru_CartRecovery_Helper_Data::LOG_FILE);
            }
            if (isset($cs) && $cs > 0) {
                Mage::log('Customers sent : ' . $cs, null, Cartsguru_CartRecovery_Helper_Data::LOG_FILE);
            }
            Mage::log('=====CartsGuru data transfer ended at ' . date('o/m/d-H:i:s') . '=====', null, Cartsguru_CartRecovery_Helper_Data::LOG_FILE);
        }

    }
}
