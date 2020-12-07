<?php

class Dbm_Share_IndexController extends Dbm_Share_Controller_Auth
{
    protected function _getPublicActions()
    {
        return array(
            'detail',
            'map'
        );
    }

    public function indexAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/index/index/');
        Mage::helper('dbm_share')->setMainMenuAlias('club/index/index/');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }

   

    public function subscriptionsAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/index/index/');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }

    public function subscribersAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/index/index/');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }

    public function likedAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/index/index/');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }

    public function photosAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/index/index/');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }

    public function recipesAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/index/index/');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }

    public function detailAction()
    {
        $handles = $this->_getDefaultLayoutHandle();
        $element = Mage::getModel('dbm_share/element');
        $id = $this->getRequest()->getParam('id', null);

        if($id && $element->load($id))
        {
            switch($element->getType())
            {
                case Dbm_Share_Model_Element::TYPE_PHOTO:
                    Mage::helper('dbm_share')->setTopMenuAlias('club/photos/index');
                    break;
                case Dbm_Share_Model_Element::TYPE_RECEIPE:
                    Mage::helper('dbm_share')->setTopMenuAlias('club/recipes/index/');
                    if(substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2) == "fr") $description = strip_tags($element->getDescriptionFrFr());
                    if(substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2) == "en") $description = strip_tags($element->getDescriptionEnGb());
                    if(substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2) == "es") $description = strip_tags($element->getDescriptionEsEs());
                    break;
            }
            
            Mage::register('dbm_share_current_element', $element);
            $handles[] = 'dbm_share_detail_'.$element->getType();

            $this->loadLayout($handles);

            $this->getLayout()->getBlock('head')->setTitle(ucwords(Mage::getStoreConfig('general/store_information/name')).' | '.$element->getTitle());
            if($description and !empty($description)) $this->getLayout()->getBlock('head')->setDescription('monbento | '.$this->__('Recipe').' : '.substr($description, 0, strrpos(substr($description, 0, 135), " ")).' ...');
            /*$this->getLayout()->getBlock('head')->setDescription("DESCRIPTION");
            $this->getLayout()->getBlock('head')->setKeywords("KEYWORDS");*/

            $this->renderLayout();
        }
        else
        {
            $this->_redirect('*/*/index');
        }
    }

    public function profileAction()
    {
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }

    public function pepitesAction()
    {
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }
    
    public function mapAction()
    {
        $type = $this->getRequest()->getParam('type', null);
        
        if(Mage::helper('dbm_share')->isTypeAllowed($type))
        {
            $handles = $this->_getDefaultLayoutHandle();
            $handles[] = $this->getFullActionName().'_'.$type;

            $this->loadLayout($handles);
            $this->renderLayout();
        }
    }

    protected function _getDefaultLayoutHandle()
    {
        $handles = parent::_getDefaultLayoutHandle();
        $handles[] = 'dbm_share_public_index_default';
        
        return $handles;
    }
}