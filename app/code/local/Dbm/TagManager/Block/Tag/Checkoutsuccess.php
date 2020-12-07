<?php

class Dbm_TagManager_Block_Tag_CheckoutSuccess extends Dbm_TagManager_Block_Tag
{
    protected function _construct()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($orderId);
        
        $customerId = Mage::getSingleton('customer/session')->getId();
        
        $collection= Mage::getResourceModel('sales/order_invoice_collection');
        $select = $collection->getSelect();
        $select->joinLeft(array('order' => Mage::getModel('core/resource')->getTableName('sales/order')), 'order.entity_id=main_table.order_id', array('customer_id' => 'customer_id'));
        $collection->addFieldToFilter('customer_id', $customerId);
        
        $customer = ($customerId && $collection->getSize() > 0) ? 1 : 0;
        
        $this->ecommerceData = array(
            "customerPurchase" => $customer, 
            "event" => "purchase", 
            "ecommerce" => array(
                "purchase" => array(
                    "actionField" => array(
                        "id" => (string) $order->getIncrementId(),
                        "revenue" => (float) $order->getGrandTotal(),
                        "tax" => (float) $order->getTaxAmount(),
                        "shipping" => (float) $order->getShippingAmount(),
                        "coupon" => (string) $order->getCouponCode()
                    ), 
                    "products" => array()
                )
            )
        );
        
        $cartItems = $order->getAllVisibleItems();
        if(count($cartItems) > 0)
        {
            $productsData = array();
            foreach($cartItems as $item)
            {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $productCats = $product->getCategoryIds();
                $catName = Mage::getModel('catalog/category')->load(array_slice(array_reverse($productCats), 0, 1))->getName();

                $productsData[] = array(
                    "name" => (string) $product->getName(),
                    "id" => (string) $product->getId(),
                    "price" => (float) $product->getFinalPrice(),
                    "category" => (string) $catName,
                    "variant" => (string) "",
                    "quantity" => (int) $item->getQtyOrdered()
                );
            }
            
            $this->setLayerData($productsData);
        }
    }
}