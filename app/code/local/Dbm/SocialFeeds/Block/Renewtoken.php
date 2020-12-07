<?php

class Dbm_SocialFeeds_Block_Renewtoken extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $appUsername = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_app_username');
        $appPassword = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_app_password');
        $instagramClientId = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_client_id');
        $redirectUri = Mage::getUrl('dbm-social/index/getcode/');
        $redirectUri = str_replace('index.php/', '', $redirectUri);

        $urlRenew = 'http://instagram-renew.dbm-dev.com/exec_casper.php?client_id='.$instagramClientId.'&redirect_uri='.$redirectUri.'&account_username='.$appUsername.'&account_password='.$appPassword;

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Generer')
                    ->setOnClick("popWin('$urlRenew', 'Renew Access Token via DBM', 'width=320,height=180,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=no')")
                    ->toHtml();

        return $html;
    }

}
