<?php

require_once('abstract.php');

class Dbm_Shell_Product extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);
        $dateFormat = 'yyyy-MM-dd HH:mm:ss';
        $outputDateFormat = 'dd/MM/yyyy HH:MM:ss';
        $startDate = '2009-01-01 00:00:00';
        $endDate = '2014-09-10 23:59:00';
        
        $headers = array(
            'Nombre de produits'
        );
        
        $invoices = Mage::getModel('sales/order_invoice')->getCollection()
            ->addFieldToFilter('created_at', array('from' => $startDate))
            ->addFieldToFilter('created_at', array('to' => $endDate))
            //->setPage(1, 20)
        ;
        
        $createdAt = clone Mage::app()->getLocale()->date();
        
        
        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'products-export' . DS;
        $name = Mage::app()->getLocale()->date()->toString('yyyy-MM-dd-HH-mm-ss');
        $file = $path . DS . $name . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $io->streamWriteCsv($headers);
        
        $totalNB = 0;
        
        foreach($invoices as $invoice)
        {
            $storeId = substr($invoice->getIncrementId(), 0, 1);
            Mage::app()->setCurrentStore(intval($storeId));
            
            $order = Mage::getModel('sales/order')->load($invoice->getOrderId());
            $nb = 0;
            foreach ($order->getItemsCollection(null, true) as $item) {
                $nb += $item->getQtyOrdered();
            }
            
            $data = array(
                'nb_items' => $nb
            );
            
            $totalNB += $nb;
            
            $this->_writeLine($io, $data);
        }
        
        $io->close();
        echo 'Total produits vendus = '.$totalNB.''."\n";
        exit();
    }
    
    protected function _writeLine(Varien_Io_File $io, $data)
    {
        $io->streamWriteCsv($data);
    }
}


$shell = new Dbm_Shell_Product();
$shell->run();