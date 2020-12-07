<?php

class Monbento_Plandesite_Block_Blog extends Mage_Core_Block_Template
{

    public function __construct() {
		    parent::__construct();
		    $collection = Mage::getModel('blog/blog')
		    		->getCollection()
		        ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addPresentFilter()
		        ->setOrder('created_time ', 'desc');
		    $this->setCollection($collection);
	  }

}