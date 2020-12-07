<?php 

class Dbm_Utils_Helper_Cms extends Mage_Core_Helper_Abstract
{
    public function getIsHomePage()
    {
        $page = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $homePage = false;

        if($page =='cms'){
            $cmsSingletonIdentifier = Mage::getSingleton('cms/page')->getIdentifier();
            $homeIdentifier = Mage::app()->getStore()->getConfig('web/default/cms_home_page');
            if($cmsSingletonIdentifier === $homeIdentifier){
                $homePage = true;
            }
        }

        return $homePage;
    }
    
    /**
     * Transform a cms string containing tags {{}} to html
     *
     * @param string $string
     * @return string 
     */
    public function filterCmsString($string)
    {
         return Mage::helper('cms')->getBlockTemplateProcessor()->filter($string);
    }
}