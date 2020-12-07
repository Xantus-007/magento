<?php

require_once('abstract.php');

class Dbm_Shell_PatchStock extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);
        
        $headers = array(
            'id_product' => 'ID Produit',
            'stock_us' => 'Impact sur stock US'
        );
        
        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = 'stock-us';
        $file = $path . DS . $name . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $io->streamWriteCsv($headers);
        
        $products = Mage::getModel('catalog/product')->getCollection();
        foreach($products as $product)
        {
            $productId = $product->getId();
            
            Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(1));
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            $defaultQty = $stockItem->getQty();
            $isInStock = $stockItem->getIsInStock();
            
            Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(6));
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            $qty = $stockItem->getQty();
            if($qty != null) {
                $data = array(
                    'id_product' => $productId,
                    'stock_us' => $stockItem->getQty()
                );

                $this->_writeLine($io, $data);

                if($stockItem->getUseDefaultWebsiteStock() == 0) {
                    $stockItem->setQty($defaultQty);
                    $stockItem->setIsInStock($isInStock);
                    $stockItem->save();

                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $write->query("UPDATE `aitoc_cataloginventory_stock_item` SET use_default_website_stock=1 WHERE website_id=3 AND product_id = ".$productId."");
                    echo 'STOCK US TO STOCK EU BY DEFAULT'."\n";
                }
            }
        }

        $io->close();
        echo 'END'."\n";
        exit();
    }
    
    /*public function run()
    {
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);
        
        $headers = array(
            'id_product' => 'ID Produit',
            'stock_us' => 'Impact sur stock US',
            'stock_hk' => 'Impact sur stock HK'
        );
        
        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = 'lst-produit-impact-stock';
        $file = $path . DS . $name . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $io->streamWriteCsv($headers);
        
        $products = Mage::getModel('catalog/product')->getCollection();
        
        foreach($products as $product)
        {
            $productId = $product->getId();
            $impactUS = 0;
            $impactHK = 0;
            
            Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(6));
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            if($stockItem->getUseDefaultWebsiteStock() == 1) $impactUS = 1;

            Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(9));
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            if($stockItem->getUseDefaultWebsiteStock() == 1) $impactHK = 1;
            
            if($impactUS == 0 && $impactHK == 0) {
                Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(1));
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                if($stockItem->getUseDefaultWebsiteStock() == 0) {
                    $newQty = $stockItem->getQty();
                    $isInStock = $stockItem->getIsInStock();
                    $stockItem->setUseDefaultWebsiteStock(1);
                    echo 'STOCK EU : '.$stockItem->getQty()."\n";
                    $stockItem->save();

                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                    $stockItem->setQty($newQty);
                    $stockItem->setIsInStock($isInStock);
                    echo 'QTY GENERAL SET TO '.$newQty."\n";
                    echo 'IS IN STOCK SET TO '.$isInStock."\n";
                    $stockItem->save();
                }
            } else {
                $data = array(
                    'id_product' => $productId,
                    'stock_us' => $impactUS,
                    'stock_hk' => $impactHK
                );

                $this->_writeLine($io, $data);
            }
        }
        
        $io->close();
        echo 'END'."\n";
        exit();
    }*/
    
    protected function _writeLine(Varien_Io_File $io, $data)
    {
        $io->streamWriteCsv($data);
    }
}


$shell = new Dbm_Shell_PatchStock();
$shell->run();