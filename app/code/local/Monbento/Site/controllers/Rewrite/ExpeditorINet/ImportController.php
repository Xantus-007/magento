<?php

require_once(Mage::getModuleDir('controllers','LaPoste_ExpeditorINet').DS.'ImportController.php');
class Monbento_Site_Rewrite_ExpeditorINet_ImportController extends LaPoste_ExpeditorINet_ImportController
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/expeditorinet/import');  
    }
    
}