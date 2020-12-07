<?php

class Monbento_Site_Block_Rewrite_Page_Html_Header extends Mage_Page_Block_Html_Header
{
    public function _construct()
    {
        parent::_construct();
        $storeId = Mage::app()->getStore()->getStoreId();
        $uri = base64_encode(Mage::helper('core/url')->getCurrentUrl());
        $cacheKey = 'header-'.$storeId.'-'.$uri;

        $this->setData('cache_key', $cacheKey);
    }
}
