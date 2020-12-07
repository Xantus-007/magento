<?php
/**
 * Atwix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Atwix Mod
 * @package     Atwix_Ipstoreswitcher
 * @author      Atwix Core Team
 * @copyright   Copyright (c) 2014 Atwix (http://www.atwix.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* app/code/local/Atwix/Ipstoreswitcher/Model/Observer.php */

use GeoIp2\Database\Reader;

class Atwix_Ipstoreswitcher_Model_Observer
{
    
    /**
     * Array store code based on GeoIP
     */
    private $_codes = array(
        'FR' => 'fr',
        'DE' => 'de',
        'IT' => 'it',
        'ES' => 'es',
        'HK' => 'hk_en',
        'GB' => 'uk_en',
        'US' => 'us_en',
    );

    /**
     * Array store locale based on GeoIP
     */
    private $_locale = array(
        'FR' => 'fr_FR',
        'DE' => 'de_DE',
        'IT' => 'it_IT',
        'ES' => 'es_ES',
        'HK' => 'en_HK',
        'GB' => 'en_GB',
        'US' => 'en_US',
    );
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        require_once Mage::getBaseDir('lib') . DS . 'atwix' . DS . 'geoip2.phar';
    }
    
    /**
     * IP EX PER COUNTRY : 
     * PL : 92.240.207.105
     * USA : 47.240.207.105
     * UK : 25.240.207.105
     * ES : 173.211.83.184
     * IT : 185.238.89.145
     * BE : 216.74.104.114
     * FR : 37.97.83.105
     * DE : 88.204.45.79
     * CHINA : 66.78.51.138
     * JP : 178.171.78.241
     * INDIA : 158.46.135.82
     * CANADA : 45.73.173.68
     * SOUTH AFRICA : 92.240.200.10
     * EGYPT : 185.231.175.99
     * MAURITIUS : 102.115.240.245
     * HONG KONG : 67.227.111.251
     */

    /**
     * redirects customer to store view based on GeoIP
     * @param $event
     */
    public function controllerActionPredispatch($event)
    {
        $reader = new Reader(Mage::getBaseDir('lib') . DS . 'atwix' . DS . 'GeoLite2-Country.mmdb');
        $ipAddress = $this->get_client_ip_server();

        if ($ipAddress !== 'UNKNOWN') {
            try {
                $infoIp = $reader->country($ipAddress);
            } catch (\Exception $e) {
                // No info found
                return;
            }

            $countryCode = $infoIp->country->isoCode;
            if ($countryCode) {
                $locale = isset($this->_locale[$infoIp->country->isoCode]) ? $this->_locale[$infoIp->country->isoCode] : '';
                $code = isset($this->_codes[$infoIp->country->isoCode]) ? $this->_codes[$infoIp->country->isoCode] : '';
                if ($code) {
                    $store = Mage::getModel('core/store')->load($code, 'code');
                }
                $helper = Mage::helper('dbm_country');
                if (!$code || ($store && !$store->getId())) {
                    $langs = $helper->getAllowedLanguagesByCountryCode($countryCode);
                    reset($langs);
                    $lang = key($langs);
                    try {
                        $store = $helper->getStoreViewByLocale($infoIp->country->isoCode, $lang);
                        $locale = $lang . '_' . $infoIp->country->isoCode;
                    } catch (Exception $e) {
                        // Country not recognized
                        // Mauritius for example isnt a country in Magento
                        return;
                    }
                }
                $currentStoreId = Mage::app()->getStore()->getId();
                if ($currentStoreId === $store->getId() ||
                    !$store->getIsActive()) {
                    return;
                }
                if ($store->getCode() != Mage::app()->getStore()->getCode() &&
                    $store->getCode() !== null &&
                    $locale !== '' &&
                    !$helper->getFlagDisplayCookie()) {
                    $helper->setFlagDisplayCookie('yes');
                    if (!Mage::registry('flagDisplayCookie')) {
                        Mage::register('flagDisplayCookie', true);
                    }
                }
            }
        }
    }

    /**
     * Get client ip server
     */
    function get_client_ip_server() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }
}
