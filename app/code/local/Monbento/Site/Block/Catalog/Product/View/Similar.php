<?php

class Monbento_Site_Block_Catalog_Product_View_Similar extends Mage_Catalog_Block_Product_Abstract
{
    public function getLoadedProductCollection()
    {
        $_product = Mage::registry('current_product');
        $categories = $_product->getCategoryCollection()->addAttributeToSelect('select_colori_for_product');
        foreach($categories as $category)
        {
            if($category->getSelectColoriForProduct())
            {
                $catId = $category->getEntityId();
                break;
            }
        }
        
        $_category = Mage::getModel('catalog/category')->load($catId);
            
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            //->addAttributeToFilter('entity_id', array('neq' => $_product->getId()))
            ->addAttributeToFilter('status', array('eq' => '1'))
            ->addAttributeToFilter('visibility', array('neq' => '1'))
            ->addCategoryFilter($_category);
        
        return $collection;
    }
    
    public function showPersoButton()
    {
        $_product = Mage::registry('current_product');
        $cats = $_product->getCategoryIds();
        return (in_array(72, $cats)) ? true : false;
    }
}
