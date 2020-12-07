<?php

class Dbm_Utils_PdfController extends Mage_Core_Controller_Front_Action 
{
    public function invoicesAction() 
    {
        $orderId = (int) $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $customerId = $order->getCustomerId();

            if ($this->_canViewOrder($order)) {
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                        ->setOrderFilter($order->getId())
                        ->load();
                if ($invoices->getSize() > 0) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    $pdfFile = Mage::getStoreConfig('general/store_information/name') . '-Facture-Cde-' . $order->getIncrementId() . '-' . $customer->getLastname() . '-' . $customer->getFirstname() . '.pdf';

                    return $this->_prepareDownloadResponse(
                                    str_replace(array("'", " "), array("", ""), $pdfFile), $pdf->render(), 'application/pdf'
                    );
                }
            }
        }
    }

    protected function _canViewOrder($order) 
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId) && in_array($order->getState(), $availableStates, $strict = true)) {
            return true;
        }
        return false;
    }

}
