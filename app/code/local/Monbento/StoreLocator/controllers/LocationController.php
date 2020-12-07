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

require_once(Mage::getModuleDir('controllers','Unirgy_StoreLocator').DS.'LocationController.php');


class Monbento_StoreLocator_LocationController extends Unirgy_StoreLocator_LocationController
{
    public function mapAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function searchAction()
    {
        $params = $this->getRequest()->getParams();
        $helper = Mage::helper('ustorelocator');

        $latlng = $helper->getSearchResult($params['zipcode']);
        list($latitude,$longitude) = explode('====', $latlng);

        header('Content-type: text/xml');
        header("HTTP/1.1 200 OK");
        header("Status: 200");
        $xml = '<?xml version="1.0" encoding="utf-8"?><markers>';

        try {
            $num = (int)Mage::getStoreConfig('ustorelocator/general/num_results');
            $units = Mage::getStoreConfig('ustorelocator/general/distance_units');
            $radius = 50;
            $storeCol = Mage::getModel('ustorelocator/location')->getCollection()
                ->addAreaFilter(
                    $latitude,
                    $longitude,
                    $radius,
                    $this->getRequest()->getParam('units', $units)
                )
                ->addProductTypeFilter($this->getRequest()->getParam('type'));

            $privateFields = Mage::getConfig()->getNode('global/ustorelocator/private_fields');
            $i = 0;

            if($storeCol->count())
            {
                foreach($storeCol as $storeData)
                {
                    $xml .= $helper->getMarkerXml($storeData);
                }
            }

        } catch (Exception $e) {
            $node = $dom->createElement('error');
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute('message', $e->getMessage());
        }

        echo $xml.='</markers>';
    }



    /**
     * Show store information on stores list page using parameters
     */
    public function locationlistdescriptionAction()
    {
        $blockLocation = $this->getLayout()->createBlock('core/template')
                ->setTemplate('unirgy/storelocator/storelocator_location.phtml')
                ->toHtml();

        echo $blockLocation;
    }


    /**
     * Show store information on map window location using parameters
     */
    public function infowindowdescriptionAction(){
            echo '{{#location}}
            <div class="loc-name">{{name}}</div>
            <div>{{address_display}}</div>
            <div>{{phone}}</div>
            <div><a href="https://{{web}}" target="_blank">{{web}}</a></div>
            {{/location}}';
    }

    /**
    * get Collection of all active Stores
    * Set store parameters values for map using Marker tag of xml
    */
    public function xmlAction()
    {
            $storeCol = Mage::getModel('ustorelocator/location')->getCollection();
            $helper = Mage::helper('ustorelocator');
            header('Content-type: text/xml');
            header("HTTP/1.1 200 OK");
            header("Status: 200");

            $xml = '<?xml version="1.0" encoding="utf-8"?><markers>';
                        if($storeCol->count()){
                         foreach($storeCol as $storeData){
                            $xml .= $helper->getMarkerXml($storeData);
             }
                        }

            echo $xml.='</markers>';
    }
}
