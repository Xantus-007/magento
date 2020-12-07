<?php

require_once(Mage::getModuleDir('controllers','LaPoste_ExpeditorINet').DS.'ExportController.php');
class Monbento_Site_Rewrite_ExpeditorINet_ExportController extends LaPoste_ExpeditorINet_ExportController
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/expeditorinet/export');  
    }

}
