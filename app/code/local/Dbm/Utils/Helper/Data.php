<?php 


class Dbm_Utils_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getHeadLinkToken()
    {
        return Mage::getStoreConfig(Dbm_Utils_Model_Observer::LINK_TOKEN_CONFIG_PATH);
    }
    
    public function isHomePage()
    {
        $currentIdent = Mage::getSingleton('cms/page')->getIdentifier();
        $result = false;
        $pageId = Mage::getSingleton('cms/page')->getIdentifier();
        $homePageIdent = Mage::getStoreConfig('web/default/cms_home_page');
        
        
        if(strlen($homePageIdent) && strlen($currentIdent))
        {
            $homePageIdent = current(explode('|', $homePageIdent));
            $currentIdent =  current(explode('|', $currentIdent));
            
            $result = $homePageIdent == $currentIdent;
        }
        
        return $result;
    }
}