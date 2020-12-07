<?php

require_once(Mage::getModuleDir('controllers','SLandsbek_SimpleOrderExport').DS.'Export'.DS.'OrderController.php');
class Monbento_Site_Rewrite_Export_OrderController extends SLandsbek_SimpleOrderExport_Export_OrderController
{
    protected function _isAllowed()
    {
        return true;  
    }
}
?>