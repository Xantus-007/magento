<?php

class Monbento_Site_Block_Rewrite_Page_Html_Topmenu extends Mage_Page_Block_Html_Topmenu
{

    /**
     * Retrieve cache key data
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $request = Mage::app()->getFrontController()->getRequest();

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        if($currentPage = Mage::getSingleton('cms/page')) $action .= $currentPage->getIdentifier();
        
        $shortCacheId = array(
            'TOPMENU',
            Mage::app()->getStore()->getId(),
            $module,
            $controller,
            $action,
            Mage::getDesign()->getPackageName()
        );
        $cacheId = $shortCacheId;

        return $cacheId;
    }
    
    public function getCmsMenu()
    {
        $pagesApropos = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addFieldToFilter('root_template', array('eq' => 'a-propos'))
                ->getAllIds();
        
        $menu = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addFieldToFilter('is_active', array('eq' => 1))
                ->addFieldToFilter('parent', array('in' => $pagesApropos));
        
        return $menu;
    }
}
