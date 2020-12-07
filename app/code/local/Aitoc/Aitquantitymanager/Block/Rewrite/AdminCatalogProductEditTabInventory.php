<?php
if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ rRDROoRaiDomZRBr('660df998d0376dc7a9d94e9624c2bc31');
/**
 * Multi-Location Inventory
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitquantitymanager
 * @version      2.1.9
 * @license:     EBR5kWF9n2SX6a9ZiEug4hNJ2bkUly0f6aLFfKrYjH
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductEditTabInventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    // override parent        
    public function __construct()
    {
        parent::__construct();
#        $this->setTemplate('catalog/product/tab/inventory.phtml');
        $this->setTemplate('aitcommonfiles/design--adminhtml--default--default--template--catalog--product--tab--inventory.phtml'); // aitoc code
    }

    
// start aitoc code
    public function isDefaultWebsite()
    {
        $iWebsiteId = 0;
        
        if ($store = $this->getRequest()->getParam('store')) 
        {
            $iWebsiteId = Mage::app()->getStore($store)->getWebsiteId();
        }
        
        if (!$iWebsiteId) 
        {
            return true;
        }
        else 
        {
            return false;
        }
    }
// finish aitoc

} } 