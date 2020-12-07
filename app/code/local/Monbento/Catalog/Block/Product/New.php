<?php
class Monbento_Catalog_Block_Product_New extends Mage_Catalog_Block_Product_Abstract
{
    public function getNewProducts() 
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('new')
            ->addAttributeToFilter('new', array('in' => '1'))
            ->addStoreFilter()
        ;
        
        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));

        return $collection;
    }
}
