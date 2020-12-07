<?php
/**
 * Unirgy_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Unirgy
 * @package    Unirgy_StoreLocator
 * @copyright  Copyright (c) 2008 Unirgy LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Unirgy
 * @package    Unirgy_StoreLocator
 * @author     Boris (Moshe) Gurevich <moshe@unirgy.com>
 */
class Monbento_StoreLocator_Helper_Data extends Unirgy_StoreLocator_Helper_Data
{
    protected $_locations = array();


    public function getMainTable()
    {
    	return "ustorelocator_location";
    }

    public function getLocation($id)
    {
        if (!isset($this->_locations[$id])) {
            $location = Mage::getModel('ustorelocator/location')->load($id);
            $this->_locations[$id] = $location->getId() ? $location : false;
        }
        return $this->_locations[$id];
    }

    /**
     *
     * @param type $zipcode
     * @return type store collection
     */
    public function getSearchResult($zipcode) {
        return $this->_getLatLngOfSearch($zipcode);
    }

    protected function _getLatLngOfSearch($search)
    {
        $urlWebServiceGoogle = 'https://maps.google.com/maps/api/geocode/json?address=%s&sensor=false&language=fr&key='.Mage::getStoreConfig('ustorelocator/general/google_api_key');
        $address = (is_numeric($search) && strlen($search) == 5) ? $search : $search;

        $url = vsprintf($urlWebServiceGoogle, urlencode($address));

        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 1
                )
            )
        );
        $get = file_get_contents($url, 0, $ctx);

        if($get)
        {
            $response = json_decode($get);
            if (!empty($response->status) && $response->status != "OK")
            {
                return false;
            }
            elseif(!empty($response->status))
            {
                $latitude =  $response->results[0]->geometry->location->lat;
                $longitude = $response->results[0]->geometry->location->lng;
                return $latitude.'===='.$longitude;
            }
        }

        return false;
    }


    public function getMarkerXml($storeData)
    {
    	return '<marker storeid="'.$storeData->getId().'" name="'.htmlspecialchars($storeData->getTitle(), ENT_XML1 | ENT_COMPAT, 'UTF-8').'" lat="'.$storeData->getLatitude().'" lng="'.$storeData->getLongitude().'" category="'.$storeData->getType().'" address_display="'.htmlspecialchars($storeData->getAddressDisplay(), ENT_XML1 | ENT_COMPAT, 'UTF-8').'" phone="'.$storeData->getPhone().'" web="'.$storeData->getWebsiteUrl().'" />';
    }

}
