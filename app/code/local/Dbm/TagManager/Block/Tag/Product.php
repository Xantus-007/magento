<?php

class Dbm_TagManager_Block_Tag_Product extends Dbm_TagManager_Block_Tag
{
    protected function _construct()
    {
        $this->ecommerceData = array("event" => "detail", "ecommerce" => array("detail" => array("products" => array())));
        
        if($product = Mage::registry('current_product'))
        {
            $productCats = $product->getCategoryIds();
            $catName = (Mage::registry('current_category')) ? Mage::registry('current_category')->getName() : Mage::getModel('catalog/category')->load(array_slice(array_reverse($productCats), 0, 1))->getName();
            
            $productsData = array(
                array(
                    "name" => (string) $product->getName(),
                    "id" => (string) $product->getId(),
                    "price" => (float) $product->getFinalPrice(),
                    "category" => (string) $catName
                )
            );
            $this->setLayerData($productsData);
        }
    }
}