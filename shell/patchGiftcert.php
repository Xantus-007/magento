<?php

require_once('abstract.php');

class Dbm_Shell_Patch extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);
        $dateFormat = 'yyyy-MM-dd HH:mm:ss';
        $outputDateFormat = 'dd/MM/yyyy HH:MM:ss';
        $startDate = '2014-12-01 00:00:00';
        $endDate = '2014-12-15 23:59:00';
        
        $headers = array(
            'ID Commande'
        );
        
        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = 'lst-order-with-giftcert';
        $file = $path . DS . $name . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $io->streamWriteCsv($headers);
        
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $lstOrder = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('created_at', array('from' => $startDate))
            ->addFieldToFilter('created_at', array('to' => $endDate))
            //->setPage(1, 20)
        ;
        
        foreach($lstOrder as $order)
        {
            echo 'CMDE : '.$order->getIncrementId()."\n";

            $arouvrir = true;
            foreach ($order->getAllItems() as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                if($product->getTypeID() == 'ugiftcert' && $order->getState() == 'complete') {
                    $options = $item->getProductOptions();
                    echo 'CARTE CADEAU AVEC LIVRAISON : '.$options['info_buyRequest']['delivery_type']."\n";
                    if($options['info_buyRequest']['delivery_type'] != 'physical') $arouvrir = false;
                } else {
                    $arouvrir = false;
                }
            }
            
            if($arouvrir) {
                echo 'CMDE A ROUVRIR'."\n";
                
                $write->query("UPDATE `sales_flat_order` SET state='processing', status='processing' WHERE entity_id = ".$order->getId()."");
                $write->query("UPDATE `sales_flat_order_grid` SET status='processing' WHERE entity_id = ".$order->getId()."");
                
                $data = array(
                    'id_order' => $order->getIncrementId()
                );

                $this->_writeLine($io, $data);
            }
        }
        
        $io->close();
        echo 'END'."\n";
        exit();
    }
    
    protected function _writeLine(Varien_Io_File $io, $data)
    {
        $io->streamWriteCsv($data);
    }
}


$shell = new Dbm_Shell_Patch();
$shell->run();