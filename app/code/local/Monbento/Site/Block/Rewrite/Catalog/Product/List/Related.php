<?php

class Monbento_Site_Block_Rewrite_Catalog_Product_List_Related extends Mage_Catalog_Block_Product_List_Related
{
    public function getLoadedProductCollection()
    {
        return $this->_itemCollection;
    }
}
