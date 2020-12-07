<?php

class Dbm_Country_Block_Rewrite_AdminStoreSwitcher extends Aitoc_Aitpermissions_Block_Rewrite_AdminStoreSwitcher
{
    public function getIsProductGrid()
    {
        return false;
        return $this->getRequest()->getControllerName() == 'catalog_product';
    }
}