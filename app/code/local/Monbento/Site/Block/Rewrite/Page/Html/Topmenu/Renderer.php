<?php

class Monbento_Site_Block_Rewrite_Page_Html_Topmenu_Renderer extends Mage_Page_Block_Html_Topmenu_Renderer
{

    public function _construct()
    {
        $request = Mage::app()->getFrontController()->getRequest();

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        if($currentPage = Mage::getSingleton('cms/page')) $action .= $currentPage->getIdentifier();
        
        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags'     => array(Mage_Catalog_Model_Product::CACHE_TAG),
            'cache_key'      => Mage::app()->getStore()->getId().'-'.$module.'-'.$controller.'-'.$action,
        ));
    }
}
