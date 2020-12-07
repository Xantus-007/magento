<?php

class Dbm_Map_Helper_Data extends Mage_Core_Helper_Abstract
{
    const GOOGLE_PLACES_GATEWAY = 'https://maps.googleapis.com/maps/api/place/autocomplete/';
    
    public function predict($string)
    {
        $result = array();
        $googleApi = Mage::getStoreConfig('dbm_map/dbm_map_api/dbm_map_api_google');
        $output = 'json';
        
        $params = array(
            'key' => $googleApi,
            'input' => $string,
            'sensor' => 'false'
        );
        
        $paramString = array();
        
        foreach($params as $key => $val)
        {
            $paramString[] = $key .'='.$val;
        }
        
        $url = self::GOOGLE_PLACES_GATEWAY.$output.'?'.implode('&', $paramString);
        
        $stringResult = file_get_contents($url);
        $jsonResult = Zend_Json::decode($stringResult);
        
        foreach($jsonResult['predictions'] as $part)
        {
            $result[] = $part['description'];
        }
        
        return $result;
    }
}