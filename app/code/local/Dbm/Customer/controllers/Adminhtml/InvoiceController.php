<?php

class Dbm_Customer_Adminhtml_InvoiceController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct() {
        parent::_construct();
        $this->_publicActions[] = 'print';
    }
    
    public function printAction()
    {
        $idInvoice = $this->getRequest()->getParam('invoice_id');
        $invoice = Mage::getModel('sales/order_invoice')->load($idInvoice);
        
        if($invoice->getId())
        {
            $createdAt = Mage::app()->getLocale()->date();
            $createdAt->setDate($invoice->getCreatedAt(), 'yyyy-MM-dd HH:mm:ss');
            
            $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
            return $this->_prepareDownloadResponse(
                'facture-'.$createdAt->toString('yyyy-MM-dd').'.pdf', $pdf->render(),
                'application/pdf'
            );
        }
    }
}