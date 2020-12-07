<?php

class Dbm_Country_Block_Topbar extends Dbm_Country_Block_Switch
{
    protected function _construct() {}

    public function getTopbarClass()
    {
        return Mage::helper('dbm_utils')->isHomePage() ? 'is-open' : '' ;
    }

    public function getTopBarCountryTextContent(){
        $lang =  substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);

        $default = $this->__('Your delivery country : <strong>%s</strong>', $this->getCountryName());

        $content = Mage::getStoreConfig('dbm_country_popup/dbm_country_popup_' . $lang . '/dbm_country_popup_' . $lang . '_topbar_country');
        $content = str_replace("%country%", $this->getCountryName(), $content);

        if($content == ''){
            $content = $default;
        }
        return $content;
    }

    public function getTopBarLanguageTextContent(){
        $lang =  substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);

        $default = $this->__('Your language : <strong>%s</strong>', $this->getLanguageName());

        $content = Mage::getStoreConfig('dbm_country_popup/dbm_country_popup_' . $lang . '/dbm_country_popup_' . $lang . '_topbar_language');
        $content = str_replace("%language%", $this->getLanguageName(), $content);

        if($content == ''){
            $content = $default;
        }
        return $content;
    }
}