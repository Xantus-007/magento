<?php

require_once(Mage::getModuleDir('controllers','DPDFrance_Export').DS.'Adminhtml'.DS.'Dpdfrance'.DS.'ExportController.php');
class Monbento_Site_Adminhtml_DPDFrance_ExportController extends DPDFrance_Export_Adminhtml_DPDFrance_ExportController
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/dpdfrexport');  
    }
    
}


