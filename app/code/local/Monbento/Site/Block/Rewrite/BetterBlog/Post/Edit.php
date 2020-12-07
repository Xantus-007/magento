<?php

class Monbento_Site_Block_Rewrite_BetterBlog_Post_Edit extends Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit
{
    public function getBackUrl()
    {
        parent::getBackUrl();
        $url = Mage::helper('core/http')->getHttpReferer();
        return $url;
    }

}
