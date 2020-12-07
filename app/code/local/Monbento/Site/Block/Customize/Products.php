<?php

class Monbento_Site_Block_Customize_Products extends Mage_Core_Block_Template 
{
    public function getProductsByCategory()
    {
        $category = Mage::registry('current_category');
        $_categories = $category->getChildrenCategories();

        $productsByCat = array();
        foreach ($_categories as $cat)
        {
            $_category = Mage::getModel('catalog/category')->load($cat->getId());
            if($_category->getIsActive())
            {
                $products = $_category->getProductCollection()
                    ->addAttributeToSelect('*');

                $productsByCat[] = array(
                    "name" => $_category->getName(),
                    "products" => $products
                );
            }
        }

        return $productsByCat;
    }

}