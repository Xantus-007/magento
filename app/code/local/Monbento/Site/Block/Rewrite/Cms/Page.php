<?php

class Monbento_Site_Block_Rewrite_Cms_Page extends Dbm_Seo_Block_Rewrite_Cms_Page
{

    /**
     * Prepare HTML content
     *
     * @return string
     */
    protected function _toHtml()
    {
        /* @var $helper Mage_Cms_Helper_Data */
        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($this->getPage()->getContent());
        //$html = $this->getMessagesBlock()->toHtml() . $html;
        return $html;
    }
}
