<?php

require_once('abstract.php');

class Dbm_Shell_Invoice extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);
        $dateFormat = 'yyyy-MM-dd HH:mm:ss';
        $outputDateFormat = 'dd/MM/yyyy HH:MM:ss';
        $startDate = '2009-01-01 00:00:00';
        $endDate = '2014-06-30 23:59:00';
        
        $headers = array(
            'ID Commande',
            'ID Facture',
            'Date de création',
            'Client',
            'Adresse de facturation',
            'Pays de facturation',
            'Adresse de livraison',
            'Pays de livraison',
            'Total commande HT',
            'Total commande TTC',
            'Nombre de produits',
            'Mode de paiement',
            'Lien PDF'
        );
        
        $invoices = Mage::getModel('sales/order_invoice')->getCollection()
            ->addFieldToFilter('created_at', array('from' => $startDate))
            ->addFieldToFilter('created_at', array('to' => $endDate))
            //->setPage(1, 20)
        ;
        
        $createdAt = clone Mage::app()->getLocale()->date();
        
        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'invoices-export' . DS;
        $name = Mage::app()->getLocale()->date()->toString('yyyy-MM-dd-HH-mm-ss');
        $file = $path . DS . $name . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $io->streamWriteCsv($headers);
        
        foreach($invoices as $invoice)
        {
            $storeId = substr($invoice->getIncrementId(), 0, 1);
            Mage::app()->setCurrentStore(intval($storeId));
            
            $order = Mage::getModel('sales/order')->load($invoice->getOrderId());
            $customer = $order->getCustomer();
            
            $createdAt->set($invoice->getCreatedAt(), $dateFormat);
            
            $billingAddress = $invoice->getBillingAddress();
            $billingCountry_name = Mage::getModel('directory/country')->load($billingAddress->getCountryId())->getName();
            $shippingAddress = $invoice->getShippingAddress();
            $shippingCountry_name = Mage::getModel('directory/country')->load($shippingAddress->getCountryId())->getName();
            
            $TotalTTC = $invoice->getGrandTotal();
            $TotalHT = $TotalTTC - $invoice->getTaxAmount();
            
            $data = array(
                'id_order' => $order->getData('increment_id'),
                'id_invoice' => $invoice->getData('increment_id'),
                'created_at' => $createdAt->toString($outputDateFormat),
                'customer' => ucwords($order->getCustomerName()),
                'billing_address' => $this->_cleanAddress($billingAddress->format('text')),
                'billing_country' => $billingCountry_name,
                'shipping_address' => $this->_cleanAddress($shippingAddress->format('text')),
                'shipping_country' => $shippingCountry_name,
                'total_ht' => Mage::helper('core')->currency($TotalHT, true, false),
                'total_ttc' => Mage::helper('core')->currency($TotalTTC, true, false),
                'nb_items' => $order->getTotalItemCount(),
                'payment_mode' => $order->getPayment()->getMethodInstance()->getTitle(),
                'shipping_mode' => strip_tags(html_entity_decode($order->getShippingDescription())),
                'pdf_link' => 'http://www.monbento.com/dbm-customer-admin/invoice/print/invoice_id/'.$invoice->getId()
            );
            
            $this->_writeLine($io, $data);
        }
        
        $io->close();
        echo 'END'."\n";
        exit();
    }
    
    protected function _writeLine(Varien_Io_File $io, $data)
    {
        $io->streamWriteCsv($data);
    }
    
    protected function _cleanAddress($string)
    {
        return str_replace('\r\n', '\\r\\n', $string);
    }
}


$shell = new Dbm_Shell_Invoice();
$shell->run();
