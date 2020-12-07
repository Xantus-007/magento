<?php

class LanguageManager
{
    
    public function getAllowedLangs()
    {
        return array('fr', 'en', 'es', 'de', 'it', 'en_us', 'fr_us');
    }
    
    public function getBrowserLocale() {
        $allowedLangs = $this->getAllowedLangs();
        $raw = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $locales = explode(",", str_replace("-", "_", $raw));
        //$result = array();

        foreach ($locales as &$locale) {
            list($locale) = explode(';', $locale);

            if (strstr($locale, '_')) {
                list($lang, $country) = explode('_', $locale);

                $result = $lang . '_' . strtoupper($country);
                break;
            }
        }
        
        if (!$result) {
            //Get country from api:
            $geoData = $this->getGeoipData();
            $lang = reset($locales);
            
            list($lang) = explode('_', $lang);
            
            if ($allowedLangs && count($allowedLangs)) {
                if (!isset($allowedLangs[$lang]) && isset($allowedLangs['en'])) {
                    $lang = 'en';
                } elseif (!isset($allowedLangs[$lang])) {
                    $lang = reset(array_keys($allowedLangs));
                }
            }
            
            $result = $lang . '_' . $geoData['country'];
        }
        
        return $result;
    }

    public function getGeoipData() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $result = null;

        if ($ip == '127.0.0.1') {
            //$ip = '193.251.87.9';//DBM
            //$ip = '125.58.128.0'; //CHINA
            //$ip = '196.22.223.255'; //STH AFRICA
            //$ip = '210.1.63.255'; //THAILAND
            //$ip = '49.237.10.20';
            $ip = '5.255.67.41';
        }
        
        //$geoData = file_get_contents('http://api.hostip.info/get_html.php?ip='.$ip);
        $geoData = file_get_contents('http://freegeoip.net/json/' . $ip);
        $geoData = json_decode($geoData);
        
        if (is_object($geoData) && isset($geoData->country_code)) {
            $result['country'] = $geoData->country_code;
        } else {
            $result['country'] = 'GB';
        }

        if ($result['country'] == 'XX') {
            $result['country'] = 'GB';
        }
        
        return $result;
    }
}