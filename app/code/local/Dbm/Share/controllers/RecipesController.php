<?php

class Dbm_Share_RecipesController extends Dbm_Share_Controller_Auth 
{
    protected function _getPublicActions()
    {
        return array('index');
    }

    public function indexAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/recipes/index/');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());

        $element = Mage::getModel('dbm_share/category');
        $category = $this->getRequest()->getParam('category', null);
        if($category && $element->load($category))
        {
            $title = (substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2) == "fr") ? $element->getTitleFrFr() : $element->getTitleEnGb();
            if(substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2) == "fr") {
                $description = $element->getMetaDescriptionFrFr();
            } elseif(substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2) == "es") {
                $description = $element->getMetaDescriptionEsEs();
            } else {
                $description = $element->getMetaDescriptionEnGb();
            }
            $this->getLayout()->getBlock('head')->setTitle(ucwords(Mage::getStoreConfig('general/store_information/name')).' | '.$this->__('Our recipes :').' '.$title);
            $this->getLayout()->getBlock('head')->setDescription($description);
        }

        $this->renderLayout();
    }
}