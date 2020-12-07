<?php

class Monbento_Plandesite_Block_Cms extends Mage_Core_Block_Template
{

    public function __construct() {
		    parent::__construct();
		    $collection = Mage::getModel('cms/page')
	  	    	->getCollection()
		        ->addStoreFilter(Mage::app()->getStore()->getId())
		        ->addFieldToFilter('identifier', array('nlike' => 'no-route%'))
		        ->addFieldToFilter('identifier', array('nlike' => 'enable-cookies'))
		        ->addFieldToFilter('identifier', array('nlike' => 'collections'))
		        ->addFieldToFilter('identifier', array('nlike' => 'home-page-v2'))
		    	  ->addFieldToFilter('parent', array(
		    	  		array('neq' => 77),
		    	  		array('null' => 1)
		    	  ));
		    $this->setCollection($collection);
	  }

}