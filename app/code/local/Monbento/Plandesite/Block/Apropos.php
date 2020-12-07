<?php

class Monbento_Plandesite_Block_Apropos extends Mage_Core_Block_Template
{

    public function __construct() {
		    parent::__construct();
		    $collection = Mage::getModel('cms/page')
	  	    	->getCollection()
		        ->addStoreFilter(Mage::app()->getStore()->getId())
		    	  ->addFieldToFilter('parent', array('eq' => 77));
		    $this->setCollection($collection);
	  }

}