<?php

class Dbm_Share_FbController extends Dbm_Share_Controller_Auth
{
    protected function _getPublicActions()
    {
        return array();
    }

    public function friendsAction()
    {
        Mage::helper('dbm_share')->setTopMenuAlias('club/index/index/');
        
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }
}