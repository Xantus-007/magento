<?php

class Monbento_Site_Model_Rewrite_OutofStockSubscription_Observer extends BusinessKing_OutofStockSubscription_Model_Observer {

    public function sendEmailToOutofStockSubscription($observer) {
        $product = $observer->getEvent()->getProduct();

        if ($product) {
            if ($product->getStockItem()) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());

                //$isInStock = $product->getStockItem()->getIsInStock();
                $isInStock = $stockItem->getIsInStock();

                if ($isInStock >= 1) {
                    $subscriptions = Mage::getResourceModel('outofstocksubscription/info')->getSubscriptions($product->getId());
                    if (count($subscriptions) > 0) {

                        //$prodUrl = $product->getProductUrl();
                        $prodUrl = Mage::getBaseUrl();
                        $prodUrl = str_replace("/index.php", "/", $prodUrl);
                        $prodUrl = $prodUrl . 'catalog/product/view/id/' . $product->getId();

                        $storeId = Mage::app()->getStore()->getId();                        

                        $translate = Mage::getSingleton('core/translate');

                        foreach ($subscriptions as $subscription) {

                            $storeId = $subscription['store_id'];

                            // get email template    
                            $emailTemplate = Mage::getStoreConfig('outofstocksubscription/mail/template', $storeId);
                            if (!is_numeric($emailTemplate)) {
                                $emailTemplate = self::OUTOFSTOCKSUBSCRIPTION_MAIL_TEMPLATE;
                            }

                            $translate->setTranslateInline(false);
                            Mage::getModel('core/email_template')
                                    ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                                    ->sendTransactional(
                                            $emailTemplate, 'support', $subscription['email'], '', array(
                                        'product' => $product->getName(),
                                        'product_url' => $prodUrl,
                            ));
                            $translate->setTranslateInline(true);

                            Mage::getResourceModel('outofstocksubscription/info')->deleteSubscription($subscription['id']);
                        }
                    }
                }
            }
        }
        //return $this;
    }

}
