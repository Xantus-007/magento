<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of data
 *
 * @author dlote
 */
class Dbm_Country_Helper_Data extends Mage_Core_Helper_Abstract
{
    const COOKIE_NAME_COUNTRY = 'dbm_country_switch_country_';
    const COOKIE_NAME_LANGUAGE = 'dbm_country_switch_language_';
    const COOKIE_NAME_REDIRECT = 'dbm_country_redirect';
    const COOKIE_NAME_FLAG_DISPLAY = 'dbm_country_flag_display';
    const REDIRECT_DEFAULT_LANGUAGE = 'en';
    const REDIRECT_DEFAULT_COUNTRY = 'IE';
    const REDIRECT_GATEWAY_URL = 'dbm-country/gateway/switch';
    const REDIRECT_POPUP_REG_VAR = 'dbm_country_reg_autopopup';
    const CACHE_IP_PREFIX = 'DBM_COUNTRY_IP_';
    
    const EVENT_AUTO_REDIRECT = 'dbm_country_auto_redirect';
    
    protected $addresse_id;

    public function getCookieName($type)
    {
        $storeCode = Mage::app()->getStore()->getCode();
        $cookieName = null;
        
        switch($type)
        {
            case 'country':
                $cookieName = self::COOKIE_NAME_COUNTRY;
                break;
            case 'lang': 
                $cookieName = self::COOKIE_NAME_LANGUAGE;
                break;
        }
        
        if($cookieName)
        {
            return $cookieName.$storeCode;
        }
    }
    
    public function getAllowedStocks()
    {
        return array(
            'eu' => array(
                'code' => 'monbento',
                'prefix' => ''
            ),
            'us'=> array(
                'code' => 'monbento_us',
                'prefix' => 'us_'
            ),
	    'hk' => array(
 		'code' => 'monbento_hk',
		'prefix' => 'hk_'
	    ),
            'uk'=> array(
                'code' => 'monbento_uk',
                'prefix' => 'uk_'
            )
        );
    }
    
    public function getStockConfig()
    {
        $allowedStocks = $this->getAllowedStocks();
        $config = array();
        
        foreach($allowedStocks as $stockId => $data)
        {
            $path = 'dbm_country/dbm_country_inventory/dbm_country_inventory_stock_'.$stockId;
            $config[$stockId] = explode(',', Mage::getStoreConfig($path));
        }
        
        return $config;
    }
    
    public function getWebsiteIdByCountry($search)
    {
        $config =  $this->getStockConfig();
        $stocks = $this->getAllowedStocks();
        
        foreach($config as $stockId => $countries)
        {
            $websiteCode = $stocks[$stockId]['code'];
            
            foreach($countries as $country)
            {
                if(strtolower($stocks[$stockId]['prefix'].$country) == strtolower($stocks[$stockId]['prefix'].$search))
                {
                    return $websiteCode;
                }
            }
        }
    }
    
    public function getStockConfigByWebsiteCode($websiteCode)
    {
        $config = $this->getAllowedStocks();
        
        foreach($config as $stockId => $data)
        {
            if($data['code'] == $websiteCode)
            {
                return $data;
            }
        }
    }
    
    public function getStoreViewByLocale($country, $lang, $mobile=true)
    {
        $trans = Mage::helper('dbm_share');
        $storeViewCollection = Mage::getModel('core/store')->getCollection();

        //Fetch stock / website and storeview
        $websiteCode = $this->getWebsiteIdByCountry($country);
        $website = Mage::getModel('core/website')->load($websiteCode);
        
        if(!$websiteCode || !$website->getId())
        {
            Mage::throwException($trans->__('Wrong country or website'));
        }
        else
        {
            $config = $this->getStockConfigByWebsiteCode($websiteCode);
            $prefix = $config['prefix'];
            
            if($mobile) {
                $storeViewCollection->addFieldToFilter('website_id', $website->getId())
                    ->addFieldToFilter('code', strtolower($prefix.$this->getDefaultLanguageByCountryCode($country)));
            } else {
                $storeViewCollection->addFieldToFilter('website_id', $website->getId())
                    ->addFieldToFilter('code', strtolower($prefix.$lang));
            }
            if(count($storeViewCollection)) {
                return $storeViewCollection->getFirstItem();
            }
        }
    }
    
    public function getDeviseByCountry($country){
        $country = strtoupper($country);
        $devises = array(
            'GBP' => explode(',', Mage::getStoreConfig('dbm_country/dbm_country_devise/dbm_country_devise_livre')),
            'EUR' => explode(',', Mage::getStoreConfig('dbm_country/dbm_country_devise/dbm_country_devise_euro')),
            'USD' => explode(',', Mage::getStoreConfig('dbm_country/dbm_country_devise/dbm_country_devise_dollar')),
	    'HKD' => explode(',', Mage::getStoreConfig('dbm_country/dbm_country_devise/dbm_country_devise_hkdollar'))
        );
        
        foreach ($devises as $key => $value) {
            if(in_array($country, $value)){
                return $key;
            }
        }
    }
    
    public function getCurrencyByLocale($devise){
        return Mage::app()->getLocale()->currency($devise)->getSymbol();
    } 
    
    public function getMaskCurrencyByLocale($devise,$locale) {
        $currency = Mage::app()->getLocale()->currency($devise);
        return str_replace('0.00', '[PRIX]', str_replace(',', '.', $currency->toCurrency(null, array('locale' => $locale))));
    }
    
    public function getDefaultLayoutHandles(Mage_Core_Controller_Front_Action $controller)
    {
        return array('default', 'country_default', strtolower($controller->getFullActionName()));
    }
    
    public function getPopupUrl()
    {
        return Mage::getUrl('dbm-country/countries/getpopup');
    }
    
    public function getBrowserLocale()
    {
        $raw = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $locales = explode(",",str_replace("-","_", $raw));
        $result = array();
        
        foreach($locales as &$locale)
        {
            list($locale) = explode(';', $locale);
            
            if(strstr($locale, '_'))
            {
                list($lang, $country) = explode('_', $locale);
                
                $result  = $lang.'_'.strtoupper($country);
                break;
            }
        }
        
        if(!$result)
        {
            //Get country from api:
            $geoData = $this->getGeoipData();
            $lang = reset($locales);
            $allowedLangs = $this->getAllowedLanguagesByCountryCode($geoData['country']);
            
            if($allowedLangs && count($allowedLangs))
            {
                if(!isset($allowedLangs[$lang]) && isset($allowedLangs[self::REDIRECT_DEFAULT_LANGUAGE]))
                {
                    $lang = self::REDIRECT_DEFAULT_LANGUAGE;
                }
                elseif(!isset($allowedLangs[$lang]))
                {
                    $lang = reset(array_keys($allowedLangs));
                }
            }
            
            $result = $lang.'_'.$geoData['country'];
            
            //echo '<pre>LOCALE : '.$result.'</pre>';
        }
        
        return $result;
    }
    
    public function getGeoipData()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $result = null;
        
        $cache = Mage::getSingleton('core/cache');
        $cacheId = self::CACHE_IP_PREFIX.$ip;
        
        if(!($country = $cache->load($cacheId)))
        {
            if($ip == '127.0.0.1')
            {
                //$ip = '193.251.87.9';//DBM
                //$ip = '125.58.128.0'; //CHINA
                //$ip = '196.22.223.255'; //STH AFRICA
                //$ip = '210.1.63.255'; //THAILAND
                $ip = '49.237.10.20';
            }

            //$url = 'http://api.hostip.info/get_html.php?ip='.$ip //DEPRECATED
            //$url = 'http://freegeoip.net/json/'.$ip; //DEPRECATED
            $url = 'https://freegeoip.app/json/'.$ip;
            
            try {
                $geoData = file_get_contents($url);
                $geoData = Zend_Json::decode($geoData);
            } catch (Exception $ex) {
                $geoData = null;
            }

            if(is_array($geoData) && isset($geoData['country_code']))
            {
                //$result['country'] = $res[0][1];
                $result['country'] = $geoData['country_code'];
            }
            else
            {
                $result['country'] = self::REDIRECT_DEFAULT_COUNTRY;
            }

            if($result['country'] == 'XX')
            {
                $result['country'] = self::REDIRECT_DEFAULT_COUNTRY;
            }
            
            $cache->save($result['country'], $cacheId, array(Mage_Catalog_Model_Product::CACHE_TAG), 86400);
        }
        else
        {
            $result['country'] = $country;
        }
        
        return $result;
    }
    
    public function hasAutoRedirectCookie()
    {
        $cookie = Mage::getModel('core/cookie')->get(self::COOKIE_NAME_REDIRECT);
        
        return $cookie === false ? false : true;
    }
    
    public function setAutoRedirectCookie()
    {
        $cookie = Mage::getModel('core/cookie')->set(self::COOKIE_NAME_REDIRECT, true);
        return $cookie;
    }
    
    public function hasFlagDisplayCookie()
    {
        $cookie = Mage::getModel('core/cookie')->get(self::COOKIE_NAME_FLAG_DISPLAY);
        
        return $cookie === 'yes' ? true : false;
    }
    
    public function setFlagDisplayCookie($value)
    {
        // Do not use real boolean
        // It fail check if value = false when you test if cookie exist
        // Use string
        return Mage::getModel('core/cookie')->set(self::COOKIE_NAME_FLAG_DISPLAY, $value, null, null, null, false, false);
    }

    public function printFlagDisplayCookie($value)
    {
        $cookie = Mage::getModel('core/cookie');

        $date = new \Datetime('now');
        $date->add(new \DateInterval('P20D'));

        $domain = $cookie->getDomain();
        $path = $cookie->getPath();
        $secure = $cookie->isSecure();
        $httponly = $cookie->getHttponly();
        
        $str = 'document.cookie = "'.self::COOKIE_NAME_FLAG_DISPLAY.'='.$value.';expires='.$date->format(\DateTime::COOKIE).';domain=.'.$domain.';path='.$path;

        if ($secure){
            $str = $str.';Secure';
        }
        if ($httponly){
            //$str = $str.';HttpOnly';
        }

        return $str.'";';
    }

    public function getFlagDisplayCookie()
    {
        return Mage::getModel('core/cookie')->get(self::COOKIE_NAME_FLAG_DISPLAY);
    }
    
    public function getAutoRedirectData()
    {
        //Get nav locale
        list($bLang, $bCountry) = explode('_', $this->getBrowserLocale());
        $config = $this->getAllowedLanguagesByCountryCode($bCountry);
        
        if(count($config))
        {
            foreach($config as $langCode => $langLabel)
            {
                if(strtolower($langCode) == strtolower($bLang))
                {
                    return array(
                        'lang' => $bLang, 
                        'country' => $bCountry
                    ); 
                }
            }
            
            $langs = $this->getAllowedLanguagesByCountryCode($bCountry);
            if(isset($langs[self::REDIRECT_DEFAULT_LANGUAGE]))
            {
                $lang = self::REDIRECT_DEFAULT_LANGUAGE;
            }
            else
            {
                foreach($langs as $lang => $langName)
                {
                    break;
                }
            }
            
            return array(
                'lang' => $lang, 
                'country' => $bCountry
            );
        }
    }
    
    public function doAutoRedirect()
    {
        try {
            $url = $this->getAutoRedirectUrl();
            //echo '<pre>REDIRECTING  TO : '.$url.'</pre>';
            //exit();
            Mage::app()->getResponse()->setRedirect($url);
        } catch (Exception $ex) {
            Mage::log('WRONG REDIRECT --> country: '.$conf['country'].' | Lang: '.$conf['lang']);
            return false;
        }
    }
    
    public function canAutoRedirect()
    {
        
        $result = false;
        $conf = $this->getAutoRedirectData();
        
        if($conf)
        {
            try {
                $currentStoreId = Mage::app()->getStore()->getId();
                $destStore = $this->getStoreViewByLocale($conf['country'], $conf['lang']);
                
                if($destStore->getId() != $currentStoreId)
                {
                    $result = true;
                }
            } catch (Exception $ex) {
                
            }
        }
        else
        {
            Mage::log('DISPLAY SHOULD DISPLAY POPUP?', null, 'api.xml');
            $this->shouldDisplayPopup(true);
        }
        
        return $result;
    }
    
    public function getAutoRedirectUrl()
    {
        $conf = $this->getAutoRedirectData();
        $storeView = $this->getStoreViewByLocale($conf['country'], $conf['lang']);
        
        if($storeView)
        {
            $result = $storeView->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).self::REDIRECT_GATEWAY_URL.'/locale/'.implode('_', $conf).'/from/'.  base64_encode($_SERVER['REQUEST_URI']);
        }
        
        return $result;
    }
    
    public function shouldDisplayPopup($trigger)
    {
        Mage::register(self::REDIRECT_POPUP_REG_VAR, true);
    }
    
    public function getShouldDisplayPopup()
    {
        return Mage::registry(self::REDIRECT_POPUP_REG_VAR);
    }
    
    public function getDefaultLanguageByCountryCode($countryCode)
    {
        $countryCode = trim(strtoupper($countryCode));
        $result = array();
        $languageCodes = array(
            'fr' => $this->__('French'), 
            'en' => $this->__('English'), 
            'it' => $this->__('Italian'), 
            'es' => $this->__('Spanish'), 
            'de' => $this->__('German')
        );

        foreach ($languageCodes as $key => $value) {
            $options = explode(',', Mage::getStoreConfig('dbm_country/dbm_country_countries/dbm_country_' . strtolower($key) ) );
            
            //echo '<pre>LANG : '.$key.' - '.print_r($options, true).'</pre>';
            
            if(in_array($countryCode, $options)){
                return $key;
            }
        }
    }

    public function getAllowedLanguagesByCountryCode($countryCode)
    {
        $countryCode = trim(strtoupper($countryCode));
        $result = array();
        $languageCodes = array(
            'fr' => $this->__('French'), 
            'en' => $this->__('English'), 
            'it' => $this->__('Italian'), 
            'es' => $this->__('Spanish'), 
            'de' => $this->__('German')
        );

        foreach ($languageCodes as $key => $value) {
            $options = explode(',', Mage::getStoreConfig('dbm_country/dbm_country_countries/dbm_country_' . strtolower($key) ) );
            
            //echo '<pre>LANG : '.$key.' - '.print_r($options, true).'</pre>';
            
            if(in_array($countryCode, $options)){
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    public function isStoreUS()
    {
        $store = Mage::app()->getStore();
        $storeCode = $store->getCode();
        //$storeCode = "hk_en";
    
        $allowed= $this->getAllowedStocks();     
        $code = strpos($storeCode, "_");
        if($code === false)
        {
            return false;    
        }else{
            $explode =  explode("_",$storeCode);
            if($explode[0]."_" == $allowed['us']['prefix'])
            {
                return true;
            }       
        }
        return false;
    }
    
    public function isStoreHK()
    {
        $store = Mage::app()->getStore();
        $storeCode = $store->getCode();
        //$storeCode = "hk_en";
    
        $allowed= $this->getAllowedStocks();     
        $code = strpos($storeCode, "_");
        if($code === false)
        {
            return false;    
        }else{
            $explode =  explode("_",$storeCode);
            if($explode[0]."_" == $allowed['hk']['prefix'])
            {
                return true;
            }       
        }
        return false;
    }
    
    /**
     * Returns true if current store is in queried stock
     * @param string $search
     * @return boolean
     */
    public function isStock($search)
    {
        $search = strtolower($search);
        $result = false;
        $allowedStocks =  $this->getAllowedStocks();
        
        foreach($allowedStocks as $stock)
        {
            if(($search == 'eu' && $stock['prefix'] == '') || $search.'_' == $stock['prefix'])
            {
                $code = $stock['code'];
                if($code == '')
                {
                    $code = 'monbento';
                }
                break;
            }
        }
        
        if($code)
        {
            $currentWebsite = Mage::getModel('core/website')->load(Mage::app()->getStore()->getWebsiteId());
            
            
            $result = $currentWebsite->getCode() == $code;
        }
        
        return $result;
    }
    
    /**
     * Returns true if current country & lang is IT 
     * @return boolean
     */
    public function isAddress()
    {  
        $this->addresse_id = Mage::app()->getRequest()->getParam('id');
    }

    /**
     * Returns true if current country & lang is IT 
     * @return boolean
     */
    public function isIT()
    {
        $country = strtolower(Mage::getModel('core/cookie')->get($this->getCookieName('country')));
        $lang = strtolower(Mage::getModel('core/cookie')->get($this->getCookieName('lang')));

        if ($country == 'it' && $lang == 'it') {
            if (Mage::getSingleton('customer/session')->isLoggedIn() && 
                $customer = Mage::getSingleton('customer/session')->getCustomer()) {
                if (!$customer->getData('fiscal_id')) {
                    $customer->setData('fiscal_id', ' ')->save();
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Returns true if current country & lang is IT 
     * @return boolean
     */
    public function showFiscalId()
    {
        $country = strtolower(Mage::getModel('core/cookie')->get($this->getCookieName('country')));
        $lang = strtolower(Mage::getModel('core/cookie')->get($this->getCookieName('lang')));

        if($customer = Mage::getSingleton('customer/session')->getCustomer()){
            $defaultBilling  = $customer->getDefaultBillingAddress();

            if($defaultBilling){
                $addresse_id = $defaultBilling->getData('entity_id');
                $address = Mage::getModel('customer/address')->load($addresse_id);
                return ($address->getData('country_id') == 'IT');
            }
        }

        if($country == 'it' && $lang == 'it')
            return true;

        return false;        
    }
}
