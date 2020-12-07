<?php

class Dbm_Seo_Block_Rewrite_Cms_Page extends Mage_Cms_Block_Page
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $page = $this->getPage();
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($page->getMetaTitle() ? $page->getMetaTitle() : $page->getTitle());
            $head->setPageTitle($page->getTitle());
        }
        
        return $this;
    }

}
