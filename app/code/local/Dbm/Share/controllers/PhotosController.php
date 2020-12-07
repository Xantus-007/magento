<?php

class Dbm_Share_PhotosController extends Dbm_Share_Controller_Auth
{
    protected function _getPublicActions() {
        return array('index');
    }

    public function indexAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/photos/index');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }
}