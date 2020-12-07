<?php

class Monbento_Site_Block_Home_Products extends Mage_Catalog_Block_Product_List 
{
    public function getProductsCollection()
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', array('eq' => '1'))
            ->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE))
            ->addAttributeToFilter('display_home', array('eq' => '1'));
        
        return $collection;
    }
    
}