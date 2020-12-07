<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Observer
 *
 * @author dlote
 */
class Dbm_Country_Model_Observer {
    
    public function predispatchHandler()
    {
        $helper = Mage::helper('dbm_country');
        $lang = Mage::getModel('core/cookie')->get($helper->getCookieName('lang'));
        $country = Mage::getModel('core/cookie')->get($helper->getCookieName('country'));

        if(isset($lang) && $lang != null && isset($country) && $country != null && !Mage::app()->getStore()->isAdmin())
        {
            /*
            $storeView = Mage::helper('dbm_country')->getStoreViewByLocale($country, $lang);
            Mage::app()->setCurrentStore($storeView->getCode());
            */

                $currency = Mage::helper('dbm_country')->getDeviseByCountry($country);
                $current_currency = Mage::app()->getStore()->getCurrentCurrency()->getCurrencyCode();

            if($currency != $current_currency){
                Mage::app()->getStore()->getCurrentCurrency()->setCurrencyCode($currency);
                //Mage::app()->getStore()->setCurrentCurrencyCode($currency);
            }
        }
    }

    public function setlocaleHandler(){
    }
    
    public function redirectHandler()
    {
        $helper = Mage::helper('dbm_country');
        $request = Mage::app()->getRequest();
        
        if(Mage::app()->getStore()->getCode() != Mage_Core_Model_Store::ADMIN_CODE 
            && !($request->getModuleName() == 'dbm-country' && $request->getActionName() == 'switch' && $request->getControllerName() == 'gateway')
            //&& !($request->getModuleName() == 'dbm-customer' && $request->getActionName() == 'switch' && $request->getControllerName() == 'mobile')
            && !($request->getModuleName() == 'vads')
            && !($request->getModuleName() == 'paypal')
            && !($request->getMOduleName() == 'cybermut')
            && !preg_match("#^linkedin|viadeo|twitter|facebook|googlebot|googlebot-news|googlebot-image|googlebot-video|googlebot-mobile|mediapartners-google|adsbot-google|msnbot|yahoo|voilabot|exabot|ask jeeves|google page speed#i", $_SERVER["HTTP_USER_AGENT"])
            /*&& Mage::helper('dbm_utils')->isHomePage()*/)
        {
            if(!$helper->hasAutoRedirectCookie())
            {
                if($helper->canAutoRedirect())
                {
                    //redirect
                    $helper->doAutoRedirect();
                }
            }
        }
    }
}
