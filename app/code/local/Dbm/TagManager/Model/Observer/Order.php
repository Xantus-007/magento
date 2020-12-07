<?php

class Dbm_TagManager_Model_Observer_Order extends Mage_Core_Model_Observer
{

    public function orderHandler(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();
        $order->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        if (Mage::getSingleton('checkout/session')->getGaClientId()) {
            $order->setGaClientId(Mage::getSingleton('checkout/session')->getGaClientId());
        }
    }

    public function paymentHandler(Varien_Event_Observer $observer)
    {
        $order = $observer->getInvoice()->getOrder();
        $storeId = ($order && $order->getStoreId()) ? $order->getStoreId() : 0;
        $gaId = $this->getGoogleAnalyticsId($storeId);
        $typeSav = ($order && $order->getTypeSav()) ? $order->getTypeSav() : null;
        if ($order && $order->getIncrementId() > 0 && $gaId && is_null($typeSav)) {
            $cid = $this->gen_uuid();
            $shippingAmount = $order->getShippingAmount();
            $data = array(
                'v' => 1,
                'tid' => $gaId,
                'cid' => ($order->getGaClientId() ?: $cid),
                't' => 'transaction',
                'ti' => $order->getIncrementId(),
                'tr' => $order->getGrandTotal(),
                'ts' => $shippingAmount,
                'tt' => $order->getTaxAmount(),
                'cu' => $order->getBaseCurrency()->getCurrencyCode()
            );

            $url = 'https://www.google-analytics.com/collect';
            $content = http_build_query($data);
            $content = utf8_encode($content);
            $user_agent = $order->getUserAgent();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            curl_close($ch);

            foreach ($order->getAllItems() as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $category = '';
                $cats = $product->getCategoryIds();
                foreach ($cats as $category_id) {
                    $_cat = Mage::getModel('catalog/category')->load($category_id);
                    $category = $_cat->getName();
                }

                $dataItem = array(
                    'v' => 1,
                    'tid' => $gaId,
                    'cid' => ($order->getGaClientId() ?: $cid),
                    't' => 'item',
                    'ti' => $order->getIncrementId(),
                    'in' => $item->getName(),
                    'ip' => $item->getPrice(),
                    'iq' => $item->getQtyOrdered(),
                    'ic' => $item->getSku(),
                    'iv' => $category,
                    'cu' => $order->getBaseCurrency()->getCurrencyCode()
                );

                $content = http_build_query($dataItem);
                $content = utf8_encode($content);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $result = curl_exec($ch);
                curl_close($ch);
            }
        }
    }

    protected function gen_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0x0fff) | 0x4000, 
                mt_rand(0, 0x3fff) | 0x8000, 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff)
        );
    }

    protected function getGoogleAnalyticsId($storeId)
    {
        $accountId = Mage::getStoreConfig(Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACCOUNT, $storeId);
        if (!empty($accountId)) {
            return $accountId;
        }
        
        return null;
    }
}
