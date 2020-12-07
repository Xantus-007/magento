<?php

class Dbm_Country_GatewayController extends Mage_Core_Controller_Front_Action
{
    /**
     * Set cookie for shipping country
     */
    public function switchAction()
    {
        $locale = $this->getRequest()->getParam('locale');
        $from = $this->getRequest()->getParam('from');
        $helper = Mage::helper('dbm_country');
        
        if($locale && strlen($locale))
        {
            $store = Mage::app()->getStore();
            $baseUrl = $store->getBaseUrl();
            $baseUrl = str_replace('http://', '', $baseUrl);
            $helper = Mage::helper('dbm_country');
            
            list($domain, $rewriteBase) = explode('/', $baseUrl);
            $directory = '/';
            
            if(strlen($rewriteBase) > 0)
            {
                $directory = '/'.$rewriteBase;
            }
            
            list($lang, $country) = explode('_', $locale);
            
            $country = strtoupper($country);
            $lang = strtolower($lang);

            Mage::getModel('core/cookie')->set($helper->getCookieName('country'), $country);
            Mage::getModel('core/cookie')->set($helper->getCookieName('lang'), $lang);
            $helper->setAutoRedirectCookie();
        }
        
        $url = Mage::getUrl('/');
        $this->getResponse()->setRedirect($url);
    }
}
