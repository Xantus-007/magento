<?php

require_once('abstract.php');

class Dbm_Order_Patch extends Mage_Shell_Abstract
{
    public function run()
    {
        $stores = array(
            1, //FR
            2, //EN
            3, //IT
            4, //ES
            5  //DE
        );
        
        $invoices = Mage::getModel('sales/order_invoice')->getCollection()
            //->addFieldToFilter('created_at', array('gt' => new Zend_Db_Expr('2014-09-01')));
            ->addFieldToFilter('created_at', array('gt' => '2014-07-01'))
            ->addFieldToFilter('store_id', array('in' => $stores))
        ;
        
        foreach($invoices as $invoice)
        {
            $items = $invoice->getItemsCollection();
            
            $hasZero = false;
            
            foreach($items as $item)
            {
                //$item->setData('tax_amount', '');
                
                if(intval($item->getData('tax_amount')) == 0)
                {
                    $hasZero = true;
                }
            }
            
            $hasZero = true;
            
            if($hasZero)
            {
                foreach($items as $item)
                {
                    $this->calculateItem($item);
                }
                
                echo 'OK';
                exit();
            }
        }
        
        exit();
    }
    
    public function calculateItem($item)
    {
        /*
        print_r($item->getData());
        exit();
        */
        $this->log('ORIG DATA : -----------');
        $this->log('row total : '.$item->getRowTotal());
        $this->log('base item with tax : '.$item->getData('base_price_incl_tax'));
        $this->log('TAX VALUE : '.$item->getTaxAmount());
        $this->log('CALC DATA : -----------');
        
        $woTax = $item->getBaseRowTotalInclTax()*(0.8333333);
        $tax = $item->getBaseRowTotalInclTax() - $woTax;
        $this->log('TAX VALUE : '.$woTax);
        
        
        print_r($item->getData());
        exit();
    }
    
    
    
    public function log($data)
    {
        echo  $data."\r\n";
    }
}

$shell = new Dbm_Order_Patch();
error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '2G');
$shell->run();