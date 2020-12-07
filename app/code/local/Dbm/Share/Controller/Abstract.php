<?php

abstract class Dbm_Share_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    protected function _getDefaultLayoutHandle()
    {
        return Mage::helper('dbm_share')->getDefaultLayoutHandles($this);
    }
}