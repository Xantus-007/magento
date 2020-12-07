<?php

/**
 * Description of Atwix_Observer
 *
 * @author kpignot
 */
class Monbento_Site_Model_Rewrite_Atwix_Observer extends Atwix_Ipstoreswitcher_Model_Observer
{
    /**
     * redirects customer to store view based on GeoIP
     * @param $event
     */
    public function controllerActionPredispatch($event)
    {
        $request = Mage::app()->getRequest();
        if (Mage::app()->getStore()->getCode() != Mage_Core_Model_Store::ADMIN_CODE 
            && !($request->getModuleName() == 'dbm-country' && $request->getActionName() == 'switch' && $request->getControllerName() == 'gateway')
            && !($request->getModuleName() == 'vads')
            && !($request->getModuleName() == 'paypal')
            && !($request->getMOduleName() == 'cybermut')
            && !preg_match("#^linkedin|viadeo|twitter|facebook|googlebot|googlebot-news|googlebot-image|googlebot-video|googlebot-mobile|mediapartners-google|adsbot-google|msnbot|yahoo|voilabot|exabot|ask jeeves|google page speed#i", $_SERVER["HTTP_USER_AGENT"])) {
            
            $helper = Mage::helper('dbm_country');
            if (!$helper->hasAutoRedirectCookie() &&
                !$helper->hasFlagDisplayCookie()) {
                parent::controllerActionPredispatch($event);
            }
        }
    }
}
