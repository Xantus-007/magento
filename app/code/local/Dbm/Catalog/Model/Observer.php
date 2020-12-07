<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Observer
 *
 * @author dlote
 */
class Dbm_Catalog_Model_Observer {
    
    public function productPredispatchHandler($observer)
    {
        $product = $observer->getData('product');
        foreach($product->getCategoryIds() as $category) {
            $cat = Mage::getModel('catalog/category')->load($category);
            if(!Mage::helper('dbm_customer')->isCategoryAllowedForCurrentCustomer($cat)){
                Mage::log('CATEGORIE RESTRICTION GOURMETS : '.$cat->getName());
                $catUrl = $cat->getUrl();
            }
        }
        if(isset($catUrl)){
            Mage::app()->getResponse()->setRedirect($catUrl);
        }
    }
}
