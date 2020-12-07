<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitquantitymanager_Model_Rewrite_FrontCoreWebsite extends Mage_Core_Model_Website
{
    
    protected function _construct()
    {
        Mage::getModel('aitquantitymanager/moduleObserver')->onAitocModuleLoad();
        
#        $this->_init('core/website');
        $this->_init('aitquantitymanager/core_website');
    }
    
    
    // override parent
    public function load($id, $field = null)
    {
        
// start aitoc code
        if ($id AND is_numeric($id) AND $id == Mage::helper('aitquantitymanager')->getHiddenWebsiteId()) 
        {
            $id = 0; 
        }
// finish aitoc code

        return parent::load($id, $field);
    }
    
}
