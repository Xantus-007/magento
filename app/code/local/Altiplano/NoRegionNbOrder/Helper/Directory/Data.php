<?php

class Altiplano_NoRegionNbOrder_Helper_Directory_Data extends Mage_Directory_Helper_Data
{

    public function getRegionJson()
    {

        Varien_Profiler::start('TEST: '.__METHOD__);
        if (!$this->_regionJson) {
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE'.Mage::app()->getStore()->getId();
            if (Mage::app()->useCache('config')) {
                $json = Mage::app()->loadCache($cacheKey);
            }
            if (empty($json)) {
                $countryIds = array();
                foreach ($this->getCountryCollection() as $country) {
                    $countryIds[] = $country->getCountryId();
                }
                $collection = Mage::getModel('directory/region')->getResourceCollection()
                    ->addCountryFilter($countryIds)
                    ->load();
                $regions = array();
                foreach ($collection as $region) {
                    if (in_array($region->getCountryId(), array('AU', 'CA', 'DE', 'GB', 'IE', 'US'))) {
                    		if (!$region->getRegionId()) {
                        		continue;
                    		}
                    		$regions[$region->getCountryId()][$region->getRegionId()] = array(
                        		'code'=>$region->getCode(),
                        		'name'=>$region->getName()
                    		);
                    }
                }
                $json = Mage::helper('core')->jsonEncode($regions);

                if (Mage::app()->useCache('config')) {
                    Mage::app()->saveCache($json, $cacheKey, array('config'));
                }
            }
            $this->_regionJson = $json;
        }

        Varien_Profiler::stop('TEST: '.__METHOD__);
        return $this->_regionJson;
    }

}