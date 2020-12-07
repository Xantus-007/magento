<?php

class Dbm_Share_Block_Page_Block extends Mage_Core_Block_Template
{
    public function getPageContent()
    {
        //$pageId = $this->getPageId();
        $pageId = intval(Mage::getStoreConfig('dbm_share_config/video/page_id'));
        $page = Mage::getModel('cms/page')->load($pageId);

        return $page;
    }
}