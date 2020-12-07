<?php

require_once('abstract.php');

class Dbm_Shell_Invoice_GiftOrder extends Mage_Shell_Abstract
{

    public function run()
    {
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);


        $orderId = 184414;
        $order = Mage::getModel('sales/order')->load($orderId);
        try {
            if (!$order->canInvoice()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
            }

            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            if (!$invoice->getTotalQty()) {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
            }

            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();

            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, 'processing', Mage::helper('vads')->__('Invoice %s was created', $invoice->getIncrementId()));
            $order->save();
        } catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
            exit;
        }
        echo 'ORDER ' . $orderId . ' INVOICED';
    }

}

$shell = new Dbm_Shell_Invoice_GiftOrder();
$shell->run();
