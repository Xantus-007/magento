<?php

class Dbm_Sales_Model_Observer
{
    
    public function addCostToSalesOrderItem(Varien_Event_Observer $observer)
    {
        if($observer->getItem()->getProductType() == 'simple')
        {
            $product = $observer->getItem()->getProduct();
            
            if ($product->getId()) 
            {             
                $product = Mage::getModel('catalog/product')->load($product->getId());
                $orderItem = $observer->getOrderItem();
                $orderItem->setCost($product->getCost());
            }
        }
    }
}

