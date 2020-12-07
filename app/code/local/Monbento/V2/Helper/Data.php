<?php

class Monbento_V2_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getOrderAsAdwordsJson(Mage_Sales_Model_Order $order)
    {
        $result = array(
            'id' => $order->getId(),
            'store' => $order->getStore()->getName(),
            'revenue' => $order->getGrandTotal(),
            'currency' => $order->getBaseCurrency()->getCurrencyCode(),
            'shipping' => $order->getShippingAmount(),
            'tax' => $order->getTaxAmount(),
            'items' => array()
        );
        
        foreach($order->getAllItems() as $item)
        {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $category = '';
            $cats = $product->getCategoryIds();
            foreach ($cats as $category_id) {
                $_cat = Mage::getModel('catalog/category')->load($category_id) ;
                $category = $_cat->getName();
            } 
            
            $result['items'][] = array(
                'product_id' => $item->getProductId(),
                'name' => $item->getName(),
                'sku' => $item->getSku(),
                'category' => $category,
                'price' => $item->getPrice(),
                'quantity' => $item->getQtyOrdered()
            );
        }
        
        return Zend_Json::encode($result);
    }
}