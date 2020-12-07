<?php
if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ eCMCrpCBoMpZgCke('6b59625dab2cf1c65fa62dd0dcdb9fb0');
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

class Aitoc_Aitquantitymanager_Model_Rewrite_FrontCatalogProductIndexerPrice extends Mage_Catalog_Model_Product_Indexer_Price
{
    // overide parent
    protected function _construct()
    {
//        $this->_init('catalog/product_indexer_price');
        $this->_init('aitquantitymanager/frontCatalogResourceEavMysql4ProductIndexerPrice');
    }
} } 