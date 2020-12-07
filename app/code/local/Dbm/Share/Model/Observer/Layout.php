<?php

class Dbm_Share_Model_Observer_Layout
{
    public function layoutRenderHandler(Varien_Event_Observer $observer)
    {
        $layout = $observer->getLayout();

        $moduleName = $controllerName = Mage::app()->getRequest()->getModuleName();

        if($moduleName == 'blog')
        {
            //@TODO: reactivate when club is on line
            //$layout->getUpdate()->addHandle('share_default');
        }
    }
}