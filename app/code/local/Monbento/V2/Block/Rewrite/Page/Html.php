<?php

class Monbento_V2_Block_Rewrite_Page_Html extends Mage_Page_Block_Html
{
    public function _construct()
    {
        parent::_construct();
        $websiteCode = 'website-'.Mage::app()->getWebsite()->getCode();
        $this->addBodyClass($websiteCode);
    }
}