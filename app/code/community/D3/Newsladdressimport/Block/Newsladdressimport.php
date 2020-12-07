<?php
class D3_Newsladdressimport_Block_Newsladdressimport extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getNewsladdressimport()     
     { 
        if (!$this->hasData('newsladdressimport')) {
            $this->setData('newsladdressimport', Mage::registry('newsladdressimport'));
        }
        return $this->getData('newsladdressimport');
        
    }
}