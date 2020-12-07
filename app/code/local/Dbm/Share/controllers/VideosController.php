<?php

class Dbm_Share_VideosController extends Dbm_Share_Controller_Auth
{
    protected function _getPublicActions() {
        return array('index');
    }

    public function indexAction()
    {
        $this->loadLayout($this->_getDefaultLayoutHandle());
        $this->renderLayout();
    }
}