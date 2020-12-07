<?php

class Dbm_Share_Helper_Api extends Mage_Core_Helper_Abstract
{
    public function getFullConfig($locale)
    {
        $helper = Mage::helper('dbm_share');
        $cHelper = Mage::helper('dbm_customer');
        $locales = Mage::helper('dbm_share')->getAllowedLocales();
        
        $locale = $this->testLocale($locale, $locales);
        
        list($lang, $country) = explode('_', $locale);
        $session = Mage::getSingleton('core/session');
        $session->setLocale($lang.'_'.strtoupper($country));
        
        $messages = array(
            'monbento://orderSuccess' => array(
                'fr_fr' => 'Votre commande est enregistrée',
                'en_gb' => 'Order is complete'
            ),
            'monbento://orderCancel' => array(
                'fr_fr' => 'Commande annulée',
                'en_gb' => 'Order was canceled'
            ),
            'monbento://orderError' => array(
                'fr_fr' => 'Une erreur a eu lieu à l\'enregistrement de votre commande',
                'en_gb' => 'An error occured while saving your order'
            ),
            'monbento://loginSuccess' => array(
                'fr_fr' => 'Bienvenue',
                'en_gb' => 'Welcome'
            ),
            'monbento://forgotFinished' => array(
                'fr_fr' => 'Mot de passe envoyé',
                'en_gb' => 'Password sent'
            )
        );
    
        $this->translateMessages($messages, $locale, $locales);
        $profileStatus = $cHelper->getProfileStatus($locale);
        
        return array(
            'currency' => $this->getDefaultDevise($locale, false),
            'mask_currency' => $this->getDefaultDevise($locale, true),
            'locales' => $helper->wsdlize($helper->getAllowedStoreViews()),
            'customisable_product' => 3380,
            'default_storeview' => $this->getDefaultStoreView($locale),
            'default_locale' => $this->getDefaultLocale($locale),
            'bundle_background' => Mage::helper('dbm_share')->getBaseUrl().'skin/frontend/default/monbento/images/club/bundle/background.png',
            'home_screens' => $helper->wsdlize($this->getImagesFromConfig('dbm_share/dbm_share_homescreen/dbm_share_homescreen_%s', 'applications/homescreen/%s', $locale)),
            'splash_screens' => $helper->wsdlize($this->getImagesFromConfig('dbm_share/dbm_share_splash/dbm_share_splash_%s', 'applications/splash/%s', $locale)),
            'home_buttons_left' => $helper->wsdlize($this->getImagesFromConfig('dbm_share/dbm_share_homebuttons/dbm_share_homebuttons01_%s', 'applications/homebuttons/%s/left', $locale)),
            'home_buttons_right' => $helper->wsdlize($this->getImagesFromConfig('dbm_share/dbm_share_homebuttons/dbm_share_homebuttons02_%s', 'applications/homebuttons/%s/right', $locale)),
            'customer_status' => $helper->wsdlize($profileStatus),
            'price_labels' => $helper->wsdlize($this->getPriceLabels()),
            'news_feed' => 'http://www.monbento.com/blog/cat/actu-mon-bento/rss',
            'videos_page' => $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'videos')),
            'default_category_id' => 45,
            'popular_category_id' => Dbm_Share_Model_Category::POPULAR_ID,
            'time_units' => $helper->wsdlize($helper->getTimeUnitsForConfig()),
            'checkout_url' =>           $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'checkout')),
            'profile_url' =>            $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'profile')),
            'register_url' =>           $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'register')),
            'fidelity_url' =>           $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'fidelity')),
            'sponsorship_url' =>        $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'sponsorship')),
            'dashboard_url' =>          $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'dashboard')),
            'cgv_url' =>                $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'cgv')),
            'cgu_url' =>                $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'cgu')),
            'mentions_url' =>           $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'mentions')),
            'lost_password_url' =>      $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'lostPassword')),
            'a_propos_url' =>           $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'apropos')),
            'caracteristiques_url' =>   $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'caracteristiques')),
            'aide_clubento_url' =>      $this->_getUrl('dbm-customer/mobile/switch', array('mode' => 'aideClubento')),
            
            'bundle_image_url' =>   $this->_getUrl('dbm-catalog/bundle/generate_image'),
            'customer_status_images' => $helper->wsdlize($this->getCustomerStatusImages($profileStatus)),
            'social_plateforms' => array(Dbm_Customer_Helper_Data::SOCIAL_PLATEFORM_FACEBOOK),
            'messages' => $helper->wsdlize($messages)
        );
    }
    
    public function translateMessages(&$messages, $locale, $locales)
    {
        foreach($messages as $key => &$message)
        {
            $this->mergeLocales($locale, $locales, $message);
        }
    }
    
    public function getStoreViews()
    {
        
    }
    
    public function getPriceLabels(){
        $priceLibelle = array(
            1 => $this->__('Cheap'),
            2 => $this->__('Average cost'),
            3 => $this->__('Expensive')
        );
        
        return $priceLibelle;
    }
    
    public function getImagesFromConfig($storeConfig, $imagePath, $newLocale)
    {
        $locales = Mage::helper('dbm_share')->getAllowedLocalesWithoutExcludeLocales();
        $result = array();
        
        foreach($locales as $locale)
        {
            $fullStoreConfig = sprintf($storeConfig, $locale);
            $imageUrl = Mage::getStoreConfig($fullStoreConfig);

            $result[$locale] = Mage::getBaseUrl('media').sprintf($imagePath, $locale).'/'.$imageUrl;
        }
        
        $this->mergeLocales($newLocale, $locales, $result);
        
        return $result;
    }

    public function getCustomerStatusImages($statuses)
    {
        $result = array();

        foreach($statuses as $id => $status)
        {
            $value = Mage::getStoreConfig('dbm_customer/dbm_customer_status_images/dbm_customer_status_'.$id);
            if($value)
            {
                $result[$id] = Mage::getBaseUrl('media').'applications/customer_status/status_'.$id.'/'.$value;
            }
        }

        return $result;
    }
    
    public function getDefaultStoreView($locale)
    {
        list($lang, $country) = explode('_', $locale);
        $helper = Mage::helper('dbm_country');
        $storeView = $helper->getStoreViewByLocale($country, $lang);
        
        Mage::log('GETTING CONFIG LOCALE : '.$storeView->getId(), null, 'debug.log'); 

        return $storeView->getId();

        list($lang, $country) = explode('_', $locale);
        $lang = strtolower($lang);
        
        switch(strtolower($lang))
        {
            case 'fr':
                $storeView = 1;
                break;
            case 'en':
                $storeView = 2;
                break;
            default:
                $storeView = 2;
                break;
        }
        
        return $storeView;
    }
    
    public function mergeLocales($locale, $locales, &$data)
    {
        $langs = array();
        
        if(!in_array($locale, $locales))
        {
            foreach($locales as $_locale)
            {
                $_locale = strtolower($_locale);
                list($lang, $country) = explode('_', $_locale);

                $langs[$lang] = $_locale;
            }
            
            list($searchLang, $searchCountry) = explode('_', $locale);
            
            if(isset($langs[$searchLang]))
            {
                $sourceLocale = $langs[$searchLang];
                
                $data[strtolower($locale)] = $data[$sourceLocale];
            }
        }
    }
    
    public function testLocale($locale, $locales)
    {
        list($lang, $country) = explode('_', $locale);
        $locale = $lang.'_'.strtoupper($country);
        $localelist = Zend_Locale::getLocaleList();
        foreach($localelist as $key => $value) {
            if($key == $locale) {
                Mage::log('ZEND LOCALE EXISTE : OUI', null, 'debug.log');
                return strtolower($locale);
            }
        }
        
        Mage::log('ZEND LOCALE EXISTE : NON', null, 'debug.log');
        
        $langs = array();
        
        if(!in_array($locale, $locales))
        {
            foreach($locales as $_locale)
            {
                $_locale = strtolower($_locale);
                list($lang, $country) = explode('_', $_locale);

                $langs[$lang] = $_locale;
            }
            
            list($searchLang, $searchCountry) = explode('_', $locale);
            
            if(isset($langs[$searchLang]))
            {
                return $langs[$searchLang];
            }
        }
        
        return 'en_IE';
    }
    
    public function getDefaultLocale($locale)
    {
        list($lang, $country) = explode('_', strtolower($locale));
        
        switch($lang)
        {
            case 'fr':
                $result = 'fr_fr';
                break;
            default:
                $result = 'en_gb';
                break;
        }
        
        return $result;
    }
    
    public function getDefaultDevise($locale, $masque)
    {
        list($lang, $country) = explode('_', strtolower($locale));
        $devise = Mage::helper('dbm_country')->getDeviseByCountry($country);
        if(!$masque) {
            return Mage::helper('dbm_country')->getCurrencyByLocale($devise);
        } else {
            return Mage::helper('dbm_country')->getMaskCurrencyByLocale($devise,$lang.'_'.strtoupper($country));
        }
    }
    
    protected function _getUrl($url, $userParams = array())
    {
        $params = array(
            '_query' => $userParams
        );
        $result = str_replace('index.php/', '', Mage::getUrl($url, $params));
        
        return $result;
    }
}
