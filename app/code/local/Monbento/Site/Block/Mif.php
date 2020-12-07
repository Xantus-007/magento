<?php

class Monbento_Site_Block_Mif extends Mage_Catalog_Block_Product_List 
{

    public function getCollectionCount()
    {
        return count($this->_getProductCollection());
    }


    protected function _getProductCollection() 
    {
        $storeId    = Mage::app()->getStore()->getId();  
        $product    = Mage::getModel('catalog/product');  

        $visibility = array(  
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
        );  

        $products   = $product->setStoreId($storeId)  
                              ->getCollection() 
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addAttributeToFilter('status', 1)  
            ->addAttributeToFilter('visibility', $visibility)
            ->addAttributeToFilter('made_in_france', array('eq' => '1'))  
            //->addAttributeToSelect(array('name'), 'inner') //you need to select “name” or you won't be able to call getName() on the product 
            //->addAttributeToSelect('orig_price')
            ->setOrder('name', 'asc')
            ->setCurPage(1)
            ->setPageSize(4)
        ;  

        $this->_collection = $products;  
        return $this->_collection; 
    }


    public function getEncloseHtml()
    {
        return true;
    }
}
