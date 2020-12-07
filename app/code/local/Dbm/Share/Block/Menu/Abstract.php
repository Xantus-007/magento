<?php

class Dbm_Share_Block_Menu_Abstract extends Mage_Core_Block_Template
{
    public function isCurrentUrl($url)
    {
        $module = Mage::app()->getRequest()->getModuleName();
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();
        
        $uri = $this->getUrl($module.'/'.$controller.'/'.$action);
        $uri = Mage::helper('core/url')->getCurrentUrl();
        $currentMenu = $this->getCurrentMenu();
        
        $result = $url == $uri;
        
        if(Mage::registry($currentMenu) && Mage::registry($currentMenu) == $url)
        {
            $result = true;
        }
        
        return $result;
    }
}