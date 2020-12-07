<?php

/**
 * Description of Switch
 *
 * @author dlote
 */
class Dbm_Country_Block_Switch extends Mage_Core_Block_Template{
    const COOKIE_AUTOPOPUP = 'dbm_country_autopopup';
    const COOKIE_AUTOPOPUP_DURATION = 86400;
    const CACHE_ID = 'dbm_country_switch';
    const JSON_CONFIG_CACHE_TTL = 86400;
    
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 0
        ));
    }
    
    public function getAvailableCountries()
    {
        $countryAllowed = explode(',', Mage::getStoreConfig('general/country/allow'));
        
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
        }

        $options = $this->_options;
        
        usort($options, array($this, '_cmp'));
        
        foreach ($options as $key => $option) {
            if(!in_array($option['value'], $countryAllowed)){
                unset($options[$key]);
            }
        }
        
        if(!isset($isMultiselect)){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
    
    public function getPopupBackground(){
        $store = Mage::app()->getStore()->getCode();
        
        $file = Mage::getStoreConfig('dbm_country_popup/dbm_country_popup_' . $store . '/dbm_country_popup_' . $store . '_background');
        
        if($file != ''){
            return '/media/dbm_country/popup/' . $file;
        }else{
            return false;
        }
    }
    
    public function getCountryName(){
        $data = $this->_getCurrentLocale();
        return $data['country'];
    }
    
    public function getLanguageName(){
        $data = $this->_getCurrentLocale();
        return $data['language'];
    }
    
    public function isAutoPopupAllowed()
    {
        $result = false;
        
        if(Mage::helper('dbm_utils')->isHomePage())
        {
            $result = Mage::helper('dbm_country')->getShouldDisplayPopup();
        }
        else
        {
            $result = false;
        }
        
        return $result;
        
        /*
        $cookie = Mage::getModel('core/cookie')->get(self::COOKIE_AUTOPOPUP);
        $result = false;
        
        $currentLocale = $this->_getCurrentLocale();
        list($browserLang, $browserCountry) = explode('_', Mage::helper('dbm_country')->getBrowserLocale());
        
        //Display autopopup only if : store 1 (FR : monbento.com) && browser not FR && cookie not defined yet
        if(!$cookie &&
            ($currentLocale['languageCode'] != $browserLang || $currentLocale['countryCode'] != $browserCountry))
        {
            Mage::getModel('core/cookie')->set(self::COOKIE_AUTOPOPUP, self::COOKIE_AUTOPOPUP_DURATION);
            
            $result = true;
        }
        
        return $result;
         * 
         */
    }
        
    public function getNotFoundPopup(){
        $lang =  substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
        
        $content = Mage::getStoreConfig('dbm_country_popup/dbm_country_popup_' . $lang . '/dbm_country_popup_' . $lang . '_notavailable');
        
        if($content == ''){
            $lang = 'fr';
            $content = Mage::getStoreConfig('dbm_country_popup/dbm_country_popup_' . $lang . '/dbm_country_popup_' . $lang . '_notavailable');
        }
        
        return $content;
    }
    
    public function getTopBarTextContent(){
        $lang =  substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
        
        $default = $this->__('Your delivery country is %s and your language is %s', $this->getCountryName(), $this->getLanguageName());
        
        $content = Mage::getStoreConfig('dbm_country_popup/dbm_country_popup_' . $lang . '/dbm_country_popup_' . $lang . '_topbar');
        $content = str_replace("%country%", $this->getCountryName(), $content);
        $content = str_replace("%language%", $this->getLanguageName(), $content);
        
        if($content == ''){
            $content = $default;
        }
        return $content;
    }
    
    public function getCountryConfigJson()
    {
        //Intensve, let's cache
        
        $cacheId = self::CACHE_ID.'_'.Mage::app()->getStore()->getId()
            .'_'.Mage::app()->getLocale()->getLocaleCode();
        $cache = Mage::getSingleton('core/cache');
        
        if(!($result = $cache->load($cacheId)))
        {
            $helper = Mage::helper('dbm_country');
            $result = array();

            foreach($this->getAvailableCountries() as $country)
            {
                if(is_array($country) && $country['value'])
                {
                    $languages = $helper->getAllowedLanguagesByCountryCode($country['value']);
                    $languageData = array();

                    foreach($languages as $langCode => $language)
                    {
                        try {
                            $storeView = $helper->getStoreViewByLocale($country['value'], $langCode, false);
                            if($storeView && $storeView->getId())
                            {
                                $languageData[] = array(
                                    'code' => $langCode,
                                    'language' => $language,
                                    'url' => Mage::app()->getStore($storeView->getId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'country/gateway/switch/locale/'.urlencode($langCode.'_'.strtoupper($country['value']))
                                );
                            }
                        } catch (Exception $ex) {}
                    }

                    $country['languages'] = $languageData;
                    $result[$country['value']] = $country;
                }
            }

            $result = Zend_Json::encode($result);
            $cache->save($result, $cacheId, array(Mage_Catalog_Model_Product::CACHE_TAG), self::JSON_CONFIG_CACHE_TTL);
        }
        
        return $result;
    }
    
    protected function _getCurrentLocale()
    {
        $mLocale = Mage::app()->getLocale();
        $helper = Mage::helper('dbm_country');
        $testCountry = Mage::getModel('core/cookie')->get($helper->getCookieName('country'));
        $testLang = Mage::getModel('core/cookie')->get($helper->getCookieName('lang'));
                
        if(strlen($testCountry) && strlen($testLang))
        {
            $sLocaleData = array($testLang, $testCountry);
            $sLocale = $testLang.'_'.$testCountry;
        }
        else
        {
            $sLocale = $mLocale->getDefaultLocale();
            
            $countryCode = Mage::getStoreConfig('general/country/default');
            
            $sLocaleData = explode('_', $sLocale);
            $sLocaleData[1] = $countryCode;
        }

        $currentLocale = $mLocale->getLocale();
        $sCurrentLocaleData = explode('_', $currentLocale);
        $zLocale = new Zend_Locale($sLocale);
        
        $languages = $zLocale->getTranslationList('Language', $sCurrentLocaleData[0], 2);
        $countries = $zLocale->getTranslationList('Territory', $sCurrentLocaleData[1], 2);
        
        $result = array(
            'language' => $languages[$sLocaleData[0]],
            'country' => $countries[$sLocaleData[1]],
            'languageCode' => $sCurrentLocaleData[0],
            'countryCode' => $sCurrentLocaleData[1]
        );
        
        return $result;
    }
    
    protected function _wd_remove_accents($str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); 
        $str = preg_replace('#&[^;]+;#', '', $str);
        return $str;
    }

    protected function _cmp($a, $b)
    {
        $_a = $this->_wd_remove_accents($a["label"]);
        $_b = $this->_wd_remove_accents($b["label"]);
        return strcmp($_a, $_b);
    }
}
