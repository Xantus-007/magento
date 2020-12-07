<?php

class Monbento_Newsletter_Model_Options{

	/**
   	* Provide available options as a value/label array
   	*
   	* @return array
   	*/
  	public function toOptionArray(){
  		$helper = Mage::helper('monbentonewsletter/data');
        $list = $helper->listContacts();
        
		return $list;
	}
}