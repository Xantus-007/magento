<?php

class Monbento_StoreLocator_Model_Rewrite_Unirgy_Location extends Unirgy_StoreLocator_Model_Location
{
	public function fetchCoordinates()
    {
        //$url = Mage::getStoreConfig('ustorelocator/general/google_geo_url');
        if (!$url) {
            $url = "https://maps.googleapis.com/maps/api/geocode/json";
        }
        $url .= strpos($url, '?')!==false ? '&' : '?';
        $url .= 'address='.urlencode(preg_replace('#\r|\n#', ' ', $this->getAddress()))."&output=csv";
	//Mage::log('MON URL : ' . $url);

        $cinit = curl_init();
        curl_setopt($cinit, CURLOPT_URL, $url);
        curl_setopt($cinit, CURLOPT_HEADER,0);
        curl_setopt($cinit, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($cinit, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($cinit);

        if (empty($response)) {
            return $This;
        }

        /*$result = explode(',', $response);
        if (sizeof($result)!=4 || $result[0]!='200') {
            //echo '<pre>'.$response.'</pre>';
            return $this;
        }*/
        $result = json_decode($response); //Mage::log('MALAT : ' . print_r($result->results, true));
        $this->setLatitude($result->results[0]->geometry->location->lat)->setLongitude($result->results[0]->geometry->location->lng);
        return $this;
    }
}
