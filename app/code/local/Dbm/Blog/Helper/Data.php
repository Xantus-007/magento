<?php

class Dbm_Blog_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getBlogUrlForCurrentStore()
    {
        return Mage::getStoreConfig('dbm_share_config/blog/url');
    }
}