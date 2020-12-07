<?php

require_once Mage::getModuleDir('controllers', 'AW_Blog').DS.'IndexController.php';;

class Dbm_Share_NewsController extends AW_Blog_IndexController
{
    protected function _getPublicActions() {
        return array();
    }

    public function indexAction()
    {
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }

    protected function _getDefaultLayoutHandle() {
        $handles = Mage::helper('dbm_share')->getDefaultLayoutHandles($this);
        $handles[] = 'dbm_share_public_news_default';

        return $handles;
    }
}