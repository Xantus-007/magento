<?php

class Monbento_Plandesite_Block_Catalog extends Mage_Core_Block_Template
{

    public function __construct() {
		    parent::__construct();
		    $collection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addIsActiveFilter()
            ->addAttributeToFilter('level', 3)
            ->addOrderField('position');
		    $this->setCollection($collection);
	  }

	  public function getProductsCollection($catId) {
				$products = Mage::getModel('catalog/category')->load($catId);
    		$collection = $products->getProductCollection()
    				->addAttributeToSelect('*')
    				->addAttributeToFilter('visibility', array('neq' => 1))
    				->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
    		return $collection;
	  }

}