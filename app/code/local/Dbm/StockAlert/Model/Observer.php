<?php

class Dbm_StockAlert_Model_Observer
{

    public function defaultNotifyStock($observer)
    {
        if ($actionInstance = Mage::app()->getFrontController()->getAction())
        {
            $action = $actionInstance->getFullActionName();
            if($action == 'adminhtml_catalog_product_save')
            {
                $stockItem = $observer->getEvent()->getItem();
                if($stockItem->getUseConfigNotifyStockQty() == 1)
                {
                    $defaultNotifyStockQty = (int) Mage::getStoreConfig('cataloginventory/item_options/notify_stock_qty');
                    $stockItem->setNotifyStockQty($defaultNotifyStockQty);
                }
            }
        }
    }

    public function lowStockReport($observer)
    {
        $this->_log('Debug stockalerts started');
        if ($actionInstance = Mage::app()->getFrontController()->getAction())
        {
            $action = $actionInstance->getFullActionName();
            if($action != 'adminhtml_catalog_product_save')
            {
                $stockItem = $observer->getEvent()->getItem();
                $currentWebsiteId = Mage::app()->getWebsite()->getId();

                if($stockItem->getWebsiteId() == $currentWebsiteId)
                {
                    $currentQty = $stockItem->getQty();
                    $notifyQty = $stockItem->getNotifyStockQty();
                    $lowStockDate = $stockItem->getLowStockDate();

                    if(is_null($notifyQty) || $notifyQty == 0) $notifyQty = (int) Mage::getStoreConfig('cataloginventory/item_options/notify_stock_qty');

                    $storeId = Mage::app()->getStore()->getStoreId();

                    $this->_log('Testing QTY for storeId : '.$storeId);
                    if($currentQty <= $notifyQty && $storeId > 0)
                    {
                        $product = Mage::getModel('catalog/product')->load($stockItem->getProductId());

                        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('dbm_stock_alert_template');
                        $emailTemplateVariables = array(
                            'productname' => $product->getName(),
                            'productsku' => $product->getSku(),
                            'currentqty' => $currentQty,
                            'stockdate' => date('d/m/Y')
                        );

                        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        $processedSubject = $emailTemplate->getProcessedTemplateSubject($emailTemplateVariables);

                        $emailsNotif = explode(',', Mage::getStoreConfig('stock_alert/stock_alert_general/to'));

                        foreach($emailsNotif as $email)
                        {
                            $this->_log('Sending stock alert mail ['.$processedSubject.'] to : '.$email);
                            $email = trim($email);
                            $mail = Mage::getModel('core/email')
                                ->setToName('Notification Stock Monbento')
                                ->setToEmail($email)
                                ->setBody(utf8_decode($processedTemplate))
                                ->setSubject($processedSubject)
                                ->setFromEmail(Mage::getStoreConfig('stock_alert/stock_alert_general/from'))
                                ->setFromName(Mage::getStoreConfig('stock_alert/stock_alert_general/name'))
                                ->setType('html');

                            try {
                                $mail->send();
                            } catch (Exception $e) {
                                Mage::log($e->getMessage());
                            }
                        }
                    }
                }
            }
        }
        $this->_log('Debug stockalerts ended');
    }

    protected function _log($string)
    {
        Mage::log($string, null, 'stockalert.log');
    }
}
