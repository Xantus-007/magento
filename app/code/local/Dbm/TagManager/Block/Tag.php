<?php

class Dbm_TagManager_Block_Tag extends Mage_Core_Block_Template
{
    protected $ecommerceData;
    
    protected function _construct()
    {
        $session = Mage::getSingleton('customer/session');

        $languages = Zend_Locale::getTranslationList('language', Mage::app()->getLocale()->getLocaleCode());
        $languageCode = substr(Mage::getStoreConfig('general/locale/code', Mage::app()->getStore()->getId()),0,2);
        $language = uc_words($languages[$languageCode]);

        if($language)
        {
            $this->setLanguage($language);
        }
        
        $customerId = ($session->getId()) ? $session->getId() : "";
        $this->setCustomerId($customerId);

        if($locale = Mage::helper('dbm_country')->getBrowserLocale())
        {
            list($lang, $country) = explode('_', $locale);
            if(strlen($country) == 2 && $countryName = $this->_getCountryName($country))
            {
                $this->setCountryName($countryName);
            }
        }
        else
        {
            //Get shipping data
            $shipping = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();//->getShippingMethod();

            if($shipping->getCountryId())
            {
                $shippingCountryName = $this->_getCountryName($shipping->getCountryId());
                $this->setCountryName($shippingCountryName);
            }
        }
    }
    
    public function _toHtml()
    {
        if($this->getType() != 'dbm_tagmanager/tag') $this->setTemplate('dbm/tagmanager/data.phtml');

        return parent::_toHtml();
    }

    public function getTagId()
    {
        return Mage::getStoreConfig('dbm_tagmanager/config/id');
    }
    
    public function getLayerDataJs()
    {
        if(!empty($this->ecommerceData) && $this->hasLayerData())
        {
            return Mage::helper('dbm_tagmanager')->encodeLayerData($this->_setDataInLayer($this->ecommerceData));
        }
        
        return false;
    }
    
    public function isDebug()
    {
        return ($_SERVER['REMOTE_ADDR'] == '193.251.87.9' || $_SERVER['REMOTE_ADDR'] == '::1');
    }

    protected function _getCountryName($countryCode)
    {
        return Mage::app()->getLocale()
            ->getCountryTranslation($countryCode)
        ;
    }

    protected function _getCountryNameFromId($countryId)
    {
         $code = Mage::getModel('directory/country')->load($country_id);
         return $this->_getCountryName($countryCode);
    }
    
    protected function _setDataInLayer($data)
    {
        if(is_array($data))
        {
            return (count($data) == 0) ? $this->getLayerData() : array_map(array($this, '_setDataInLayer'), $data);
        }
        else
        {
            return $data;
        }
    }
}