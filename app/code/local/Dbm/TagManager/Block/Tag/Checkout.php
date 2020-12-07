<?php

class Dbm_TagManager_Block_Tag_Checkout extends Dbm_TagManager_Block_Tag
{
    protected function _construct()
    {
        $step = ($this->hasStep()) ? $this->getStep() : 1;
        $option = ($this->hasOption()) ? $this->getOption() : 'Panier';
        $push = ($this->hasNotPush()) ? false : true;
        $this->ecommerceData = array("event" => "checkout", "ecommerce" => array("checkout" => array("actionField" => array("step" => $step, "option" => $option), "products" => array())));
        
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $cartItems = $quote->getAllVisibleItems();
        if(count($cartItems) > 0)
        {
            $productsData = array();
            foreach($cartItems as $item)
            {
                $product = $item->getProduct();
                $productCats = $product->getCategoryIds();
                $catName = (Mage::registry('current_category')) ? Mage::registry('current_category')->getName() : Mage::getModel('catalog/category')->load(array_slice(array_reverse($productCats), 0, 1))->getName();

                $productsData[] = array(
                    "name" => (string) $product->getName(),
                    "id" => (string) $product->getId(),
                    "price" => (float) $product->getFinalPrice(),
                    "category" => (string) $catName,
                    "variant" => (string) "",
                    "quantity" => (int) $item->getQty()
                );
            }
            
            $this->setLayerData($productsData);
        }

        if(!$push) $this->setNotPush(true);
    }
}